<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\User;
use Spatie\Permission\Models\Role;
use DB;
use Hash;
use Illuminate\Support\Arr;

class UserController extends Controller
{
    function __construct()
    {
         $this->middleware('permission:user-list|user-create|user-edit|user-delete', ['only' => ['index','store']]);
         $this->middleware('permission:user-create', ['only' => ['create','store']]);
         $this->middleware('permission:user-edit', ['only' => ['edit','update']]);
         $this->middleware('permission:user-delete', ['only' => ['destroy']]);
    }
    public function index(Request $request)
    {

        $users = User::orderBy('id','DESC')->paginate(5);

        return view('users.index',compact('users'));

    }


    public function create()
    {
        if(auth()->user()->hasRole('Super-Admin')){
            $roles = Role::pluck('name','name')->all();
        }else{
            $roles = Role::pluck('name','name')->except(['name', 'Super-Admin']);
        }

        return view('users.create',compact('roles'));
    }


    public function store(Request $request)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            'password' => 'required',
            'confirm-password' => 'required|same:password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input);
        $user->assignRole($request->input('roles'));

        $notification = array(
            'message' => 'Người dùng được tạo thành công',
            'alert-type' => 'success'
        );
        return redirect()->route('users.index')
                        ->with($notification);
    }

    public function show($id)
    {
        return redirect()->route('users.index');
    }

    public function edit($id)
    {
        $user = User::find($id);
        if($user->hasRole('Super-Admin')){
            $notification = array(
                'message' => "Bạn không có quyền chỉnh sửa người dùng này",
                'alert-type' => 'error'
            );
            return redirect()->route('users.index')
                            ->with($notification);
        }
        if(auth()->user()->hasRole('Super-Admin')){
            $roles = Role::pluck('name','name')->all();
        }else{
            $roles = Role::pluck('name','name')->except(['name', 'Super-Admin']);
        }
        $userRole = $user->roles->pluck('name','name')->all();

        return view('users.edit',compact('user','roles','userRole'));
    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'same:confirm-password',
            'roles' => 'required'
        ]);

        $input = $request->all();
        if(!empty($input['password'])){
            $input['password'] = Hash::make($input['password']);
        }else{
            $input = Arr::except($input,array('password'));
        }

        $user = User::find($id);
        $user->update($input);
        DB::table('model_has_roles')->where('model_id',$id)->delete();

        $user->assignRole($request->input('roles'));

        $notification = array(
            'message' => 'Người dùng được cập nhật thành công',
            'alert-type' => 'success'
        );
        return redirect()->route('users.index')
                        ->with($notification);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if(auth()->id() == $id){
            $notification = array(
                'message' => "Bạn không thể xóa chính bạn",
                'alert-type' => 'error'
            );
            return redirect()->route('users.index')
                            ->with($notification);
        }
        if($user->hasRole('Super-Admin')){
            $notification = array(
                'message' => "Bạn không có quyền xóa người dùng này",
                'alert-type' => 'error'
            );
            return redirect()->route('users.index')
                            ->with($notification);
        }
        $user->delete();
        $notification = array(
            'message' => "Người dùng được xóa thành công",
            'alert-type' => 'success'
        );
        return redirect()->route('users.index')
                        ->with($notification);
    }
}
