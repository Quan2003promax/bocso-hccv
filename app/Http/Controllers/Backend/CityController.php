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
        // Sử dụng đường dẫn tương đối từ storage thay vì hardcoded path
        $dataPath = storage_path('app/data/');
        
        // Kiểm tra file có tồn tại không
        $cityFile = $dataPath . 'tinh_tp.json';
        $districtFile = $dataPath . 'quan_huyen.json';
        $wardFile = $dataPath . 'xa_phuong.json';
        
        if (!file_exists($cityFile) || !file_exists($districtFile) || !file_exists($wardFile)) {
            return response()->json([
                'error' => 'Data files not found. Please ensure the JSON files are placed in storage/app/data/'
            ], 404);
        }

        $city = json_decode(file_get_contents($cityFile), true);
        $district = json_decode(file_get_contents($districtFile), true);
        $ward = json_decode(file_get_contents($wardFile), true);

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
