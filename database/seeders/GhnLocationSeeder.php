<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\DB;

class GhnLocationSeeder extends Seeder
{
    public function run()
    {
        set_time_limit(0); 
        
        $token = env('GHN_API_TOKEN');
        if (!$token || $token == 'your_ghn_token_here') {
            $this->command->error('LỖI: Chưa có Token hoặc Token chưa được nhận.');
            return;
        }

        $headers = ['Token' => $token];

        // 1. Tỉnh / Thành
        $this->command->info('1/3 - Đang đồng bộ danh sách Tỉnh/Thành...');
        $responseProv = Http::withoutVerifying()
            ->withHeaders($headers)
            ->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/province');

        $provinces = $responseProv->json('data');

        if (empty($provinces)) {
            $this->command->error('Lỗi API GHN (Tỉnh/Thành): ' . $responseProv->body());
            return;
        }

        foreach ($provinces as $p) {
            DB::table('provinces')->updateOrInsert(
                ['id' => $p['ProvinceID']],
                ['name' => $p['ProvinceName']]
            );
        }

        // 2. Quận / Huyện
        $this->command->info('2/3 - Đang đồng bộ danh sách Quận/Huyện...');
        $responseDist = Http::withoutVerifying()
            ->withHeaders($headers)
            ->get('https://online-gateway.ghn.vn/shiip/public-api/master-data/district');
            
        $districts = $responseDist->json('data');

        if (empty($districts)) {
            $this->command->error('Lỗi API GHN (Quận/Huyện): ' . $responseDist->body());
            return;
        }

        // Lấy danh sách ID của các Tỉnh đã được lưu thành công vào DB
        $existingProvinceIds = DB::table('provinces')->pluck('id')->toArray();

        foreach ($districts as $d) {
            // CHỈ LƯU NHỮNG QUẬN/HUYỆN THUỘC VỀ TỈNH ĐANG TỒN TẠI TRONG DB
            if (in_array($d['ProvinceID'], $existingProvinceIds)) {
                DB::table('districts')->updateOrInsert(
                    ['id' => $d['DistrictID']],
                    [
                        'province_id' => $d['ProvinceID'],
                        'name' => $d['DistrictName']
                    ]
                );
            } else {
                // Hiển thị cảnh báo nhỏ để biết API trả về data thừa
                $this->command->warn("Đã bỏ qua Huyện [{$d['DistrictName']}] vì Tỉnh ID [{$d['ProvinceID']}] không tồn tại.");
            }
        }

        // 3. Phường / Xã
        $this->command->info('3/3 - Đang đồng bộ Phường/Xã (Vui lòng đợi khoảng 2-5 phút)...');
        // Vì ta chỉ query dựa trên ID quận/huyện sẵn có trong DB, nên bước này tự động an toàn 100%
        $districtIds = DB::table('districts')->pluck('id');
        
        foreach ($districtIds as $districtId) {
            $responseWard = Http::withoutVerifying()
                ->withHeaders($headers)
                ->get("https://online-gateway.ghn.vn/shiip/public-api/master-data/ward?district_id={$districtId}");
            
            $wards = $responseWard->json('data');

            if (!empty($wards)) {
                $insertData = [];
                foreach ($wards as $w) {
                    $insertData[] = [
                        'code' => (string) $w['WardCode'],
                        'district_id' => $districtId,
                        'name' => $w['WardName'],
                    ];
                }
                DB::table('wards')->insertOrIgnore($insertData);
            }
        }

        $this->command->info('HOÀN TẤT! Dữ liệu địa phương đã được lưu vào Database.');
    }
}