<?php

namespace App\Http\Controllers\Backend;

use App\Models\City;
use App\Models\Ward;
use App\Models\District;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class CityController extends Controller
{
     public function postJson(){

        $city = json_decode(file_get_contents("C:/Users/Hoang/Downloads/hanhchinhvn-master/hanhchinhvn-master/dist/tinh_tp.json"), true);

        $district = json_decode(file_get_contents("C:/Users/Hoang/Downloads/hanhchinhvn-master/hanhchinhvn-master/dist/quan_huyen.json"), true);

        $ward = json_decode(file_get_contents("C:/Users/Hoang/Downloads/hanhchinhvn-master/hanhchinhvn-master/dist/xa_phuong.json"), true);

        foreach ($city as $item){
            City::create([
                'name' => $item['name'],
                'code' => $item['code'],
                'type' => $item['type'],
                'slug' => $item['slug'],
                'name_with_type' => $item['name_with_type'],
            ]);
        }

        foreach ($district as $item){
            District::create([
                'name' => $item['name'],
                'code' => $item['code'],
                'type' => $item['type'],
                'slug' => $item['slug'],
                'name_with_type' => $item['name_with_type'],
                'path' => $item['path'],
                'parent_code' => $item['parent_code'],
                'city_id' => City::where('code', $item['parent_code'])->first()->id,
                'path_with_type' => $item['path_with_type'],
            ]);
        }

        foreach ($ward as $item){
            Ward::create([
                'name' => $item['name'],
                'code' => $item['code'],
                'type' => $item['type'],
                'slug' => $item['slug'],
                'name_with_type' => $item['name_with_type'],
                'path' => $item['path'],
                'parent_code' => $item['parent_code'],
                'district_id' => District::where('code', $item['parent_code'])->first()->id,
                'path_with_type' => $item['path_with_type'],
            ]);
        }

        return 'done';
    }
}
