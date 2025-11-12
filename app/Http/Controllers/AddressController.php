<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AddressController extends Controller
{
    private function loadJson($filename)
    {
        $path = public_path("ph-json/{$filename}");
        if (!File::exists($path)) {
            return [];
        }
        return json_decode(File::get($path), true) ?? [];
    }

    public function getRegions()
    {
        $data = $this->loadJson('region.json');
        return response()->json($data);
    }

    public function getProvinces($regionCode)
    {
        $data = $this->loadJson('province.json');
        $filtered = array_filter($data, fn($prov) => $prov['region_code'] == $regionCode);
        return response()->json(array_values($filtered));
    }

    public function getCities($provinceCode)
    {
        $data = $this->loadJson('city.json');
        $filtered = array_filter($data, fn($city) => $city['province_code'] == $provinceCode);
        return response()->json(array_values($filtered));
    }

    public function getBarangays($cityCode)
    {
        $data = $this->loadJson('barangay.json');
        $filtered = array_filter($data, fn($brgy) => $brgy['city_code'] == $cityCode);
        return response()->json(array_values($filtered));
    }
}
