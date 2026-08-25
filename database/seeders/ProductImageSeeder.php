<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ProductImageSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('product_images')->insert([
            // Kinh dị
            ['product_id' => 1, 'image' => 'shining.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 2, 'image' => 'it_1.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 3, 'image' => 'it_2.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 4, 'image' => 'ringu.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 5, 'image' => 'rasen.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 6, 'image' => 'loop.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 7, 'image' => 'cthulhu.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 8, 'image' => 'dracula.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 9, 'image' => 'nghi_can_x.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 10, 'image' => 'thanh_gia_rong.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 11, 'image' => 'misery.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 12, 'image' => 'pet_sematary.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 13, 'image' => 'ky_an_anh_trang.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 14, 'image' => 'de_thi_dam_mau.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 15, 'image' => 'cuong_vong.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 16, 'image' => 'frankenstein.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 17, 'image' => 'another.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 18, 'image' => 'bup_be_goi_hon.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 19, 'image' => 'nguoi_ca_say_ngu.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 20, 'image' => 'carrie.jpg', 'is_primary' => 1, 'sort_order' => 1],

            // Tiểu thuyết
            ['product_id' => 21, 'image' => 'nha_gia_kim.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 22, 'image' => 'rung_na_uy.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 23, 'image' => 'kafka_ben_bo_bien.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 24, 'image' => 'giet_con_chim_nhai.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 25, 'image' => 'bo_gia.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 26, 'image' => 'chien_tranh_hoa_binh.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 27, 'image' => 'suoi_nguon.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 28, 'image' => 'khong_gia_dinh.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 29, 'image' => 'doi_gio_hu.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 30, 'image' => 'kieu_hanh_dinh_kien.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 31, 'image' => 'tieng_chim_hot.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 32, 'image' => 'tram_nam_co_don.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 33, 'image' => 'so_do.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 34, 'image' => 'tat_den.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 35, 'image' => 'bat_tre_dong_xanh.jpg', 'is_primary' => 1, 'sort_order' => 1],

            // Trinh thám
            ['product_id' => 36, 'image' => 'sherlock_1.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 37, 'image' => 'sherlock_2.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 38, 'image' => 'an_mang_tau_toc_hanh.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 39, 'image' => 'va_roi_ai_cung_chet.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 40, 'image' => 'mat_ma_da_vinci.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 41, 'image' => 'thien_than_ac_quy.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 42, 'image' => 'su_im_lang_cua_bay_cuu.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 43, 'image' => 'rong_do.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 44, 'image' => 'co_gai_co_hinh_xam_rong.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 45, 'image' => 'toi_ac_va_hinh_phat.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 46, 'image' => 'bach_da_hanh.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 47, 'image' => 'toi_pham_may_man.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 48, 'image' => 'an_mang_song_nile.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 49, 'image' => 'bieu_tuong_that_truyen.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 50, 'image' => 'hoa_nguc.jpg', 'is_primary' => 1, 'sort_order' => 1],

            // Truyện ngụ ngôn
            ['product_id' => 51, 'image' => 'ngu_ngon_aesop.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 52, 'image' => 'la_fontaine.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 53, 'image' => 'con_meo_day_hai_au_bay.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 54, 'image' => 'oc_sen_cham_chap.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 56, 'image' => 'trai_suc_vat.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 57, 'image' => 'ngon_tay_mui_sa.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 58, 'image' => 'tho_va_rua.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 59, 'image' => 'qua_va_cong.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 60, 'image' => 'thay_boi_xem_voi.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 61, 'image' => 'ech_ngoi_day_gieng.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 62, 'image' => 'deo_nhac_cho_meo.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 63, 'image' => 'soi_va_cuu.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 64, 'image' => 'kien_va_ve.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 65, 'image' => 'tri_khon_cua_ta_day.jpg', 'is_primary' => 1, 'sort_order' => 1],

            // Manga
            ['product_id' => 66, 'image' => 'one_piece_1.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 67, 'image' => 'one_piece_100.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 68, 'image' => 'conan_1.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 69, 'image' => 'conan_100.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 70, 'image' => 'doraemon_1.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 71, 'image' => 'doraemon_tuyen_tap.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 72, 'image' => 'naruto_1.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 73, 'image' => 'demonslayer_1.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 74, 'image' => 'jujutsu_1.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 75, 'image' => 'dragonball_1.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 76, 'image' => 'slamdunk_1.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 77, 'image' => 'aot_1.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 78, 'image' => 'spy_1.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 79, 'image' => 'haikyu_1.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 80, 'image' => 'monster_1.jpg', 'is_primary' => 1, 'sort_order' => 1],

            // Cổ tích
            ['product_id' => 81, 'image' => 'co_tich_grimm.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 82, 'image' => 'andersen.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 83, 'image' => 'nghin_le_mot_dem.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 84, 'image' => 'co_tich_vn.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 85, 'image' => 'tam_cam.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 86, 'image' => 'thach_sanh.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 87, 'image' => 'trau_cau.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 88, 'image' => 'thanh_giong.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 89, 'image' => 'son_tinh_thuy_tinh.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 90, 'image' => 'ho_guom.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 91, 'image' => 'lo_lem.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 92, 'image' => 'bach_tuyet.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 93, 'image' => 'aladdin.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 94, 'image' => 'cay_tre_tram_dot.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 95, 'image' => 'an_khe_tra_vang.jpg', 'is_primary' => 1, 'sort_order' => 1],

            // Văn học nước ngoài
            ['product_id' => 96, 'image' => 'hoang_tu_be.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 97, 'image' => 'ong_gia_va_bien_ca.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 98, 'image' => 'gatsby.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 99, 'image' => 'nha_tho_duc_ba.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 100, 'image' => 'nhung_nguoi_khon_kho.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 101, 'image' => 'cuon_theo_chieu_gio.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 102, 'image' => '1984.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 103, 'image' => 'anh_em_nha_karamazov.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 104, 'image' => 'don_quijote.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 105, 'image' => 'lord_of_the_rings_1.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 106, 'image' => 'hobbit.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 107, 'image' => 'moi_tinh_dau.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 108, 'image' => 'tieng_goi_noi_hoang_da.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 109, 'image' => 'tra_hoa_nu.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 110, 'image' => 'ten_cua_doa_hong.jpg', 'is_primary' => 1, 'sort_order' => 1],

            //Triết lý sống
            ['product_id' => 111, 'image' => 'dac_nhan_tam.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 112, 'image' => 'quay_ganh_lo_di.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 113, 'image' => 'hieu_ve_trai_tim.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 114, 'image' => 'lam_nhu_choi.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 115, 'image' => 'tim_kiem_le_song.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 116, 'image' => 'muon_kiep_nhan_sinh_1.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 117, 'image' => 'muon_kiep_nhan_sinh_2.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 118, 'image' => 'loi_song_toi_gian.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 119, 'image' => 'bon_thoa_uoc.jpg', 'is_primary' => 1, 'sort_order' => 1],
            ['product_id' => 120, 'image' => 'dam_bi_ghet.jpg', 'is_primary' => 1, 'sort_order' => 1],

        ]);
    }
}
