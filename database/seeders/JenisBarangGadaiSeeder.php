<?php

namespace Database\Seeders;

use App\Models\JenisBarangGadai;
use Illuminate\Database\Seeder;

class JenisBarangGadaiSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            [
                'name' => 'Laptop / Notebook',
                'category' => 'Elektronik',
                'description' => 'Laptop, notebook, atau ultrabook semua merek dan tipe',
                'base_value_per_unit' => 3000000,
                'unit' => 'unit',
                'max_loan_percentage' => 80,
                'conditions' => ['Baru', 'Sangat Baik', 'Baik', 'Cukup'],
                'brands' => [
                    ['name' => 'Apple MacBook', 'value' => 12000000],
                    ['name' => 'Dell / HP / Lenovo Gaming', 'value' => 8000000],
                    ['name' => 'ASUS ROG / Acer Predator', 'value' => 7000000],
                    ['name' => 'Merek Lainnya', 'value' => 3000000],
                ],
                'requirements' => ['Charger asli', 'Box (jika ada)', 'Catatan kondisi baterai'],
                'is_active' => true,
            ],
            [
                'name' => 'Smartphone / HP',
                'category' => 'Elektronik',
                'description' => 'Handphone, smartphone semua merek',
                'base_value_per_unit' => 1000000,
                'unit' => 'unit',
                'max_loan_percentage' => 80,
                'conditions' => ['Baru', 'Sangat Baik', 'Baik', 'Cukup'],
                'brands' => [
                    ['name' => 'iPhone 14/15 Pro', 'value' => 14000000],
                    ['name' => 'iPhone 12/13', 'value' => 7000000],
                    ['name' => 'Samsung Galaxy S Series', 'value' => 6000000],
                    ['name' => 'Samsung / Xiaomi Flagship', 'value' => 4000000],
                    ['name' => 'Merek Lainnya', 'value' => 1500000],
                ],
                'requirements' => ['Charger asli', 'Box (jika ada)'],
                'is_active' => true,
            ],
            [
                'name' => 'TV / Televisi',
                'category' => 'Elektronik',
                'description' => 'Televisi LED, OLED, QLED semua ukuran',
                'base_value_per_unit' => 1500000,
                'unit' => 'unit',
                'max_loan_percentage' => 70,
                'conditions' => ['Baru', 'Sangat Baik', 'Baik', 'Cukup'],
                'brands' => [
                    ['name' => 'Samsung / LG 50"+ Smart TV', 'value' => 5000000],
                    ['name' => 'Samsung / LG 32"-49"', 'value' => 2500000],
                    ['name' => 'Sony Bravia', 'value' => 3000000],
                    ['name' => 'Merek Lainnya', 'value' => 1500000],
                ],
                'requirements' => ['Remote', 'Kabel power', 'Box (jika ada)'],
                'is_active' => true,
            ],
            [
                'name' => 'Kulkas / Lemari Es',
                'category' => 'Peralatan Rumah Tangga',
                'description' => 'Kulkas 1 pintu, 2 pintu, side by side',
                'base_value_per_unit' => 1000000,
                'unit' => 'unit',
                'max_loan_percentage' => 70,
                'conditions' => ['Baru', 'Sangat Baik', 'Baik', 'Cukup'],
                'brands' => [
                    ['name' => 'Samsung / LG 2 Pintu Besar', 'value' => 4000000],
                    ['name' => 'Panasonic / Sharp 2 Pintu', 'value' => 2500000],
                    ['name' => '1 Pintu Semua Merek', 'value' => 1000000],
                ],
                'requirements' => ['Kondisi kompresor baik', 'Tidak ada kebocoran freon'],
                'is_active' => true,
            ],
            [
                'name' => 'Rice Cooker / Magic Com',
                'category' => 'Peralatan Rumah Tangga',
                'description' => 'Rice cooker, magic com, magic jar',
                'base_value_per_unit' => 200000,
                'unit' => 'unit',
                'max_loan_percentage' => 70,
                'conditions' => ['Baru', 'Sangat Baik', 'Baik'],
                'brands' => [
                    ['name' => 'Philips / Miyako Besar', 'value' => 500000],
                    ['name' => 'Merek Standar', 'value' => 200000],
                ],
                'requirements' => ['Sendok nasi', 'Kabel power', 'Berfungsi normal'],
                'is_active' => true,
            ],
            [
                'name' => 'Kompor Gas',
                'category' => 'Peralatan Rumah Tangga',
                'description' => 'Kompor gas tanam atau kompor portabel',
                'base_value_per_unit' => 300000,
                'unit' => 'unit',
                'max_loan_percentage' => 70,
                'conditions' => ['Baru', 'Sangat Baik', 'Baik'],
                'brands' => [
                    ['name' => 'Rinnai / Winn Gas Tanam', 'value' => 800000],
                    ['name' => 'Portable / Standar', 'value' => 300000],
                ],
                'requirements' => ['Tidak ada kebocoran', 'Nyala normal'],
                'is_active' => true,
            ],
            [
                'name' => 'Kipas Angin / AC',
                'category' => 'Peralatan Rumah Tangga',
                'description' => 'Kipas angin standing, atau AC split',
                'base_value_per_unit' => 300000,
                'unit' => 'unit',
                'max_loan_percentage' => 70,
                'conditions' => ['Baru', 'Sangat Baik', 'Baik', 'Cukup'],
                'brands' => [
                    ['name' => 'AC 1 PK Daikin/Panasonic', 'value' => 3500000],
                    ['name' => 'AC 0.5 PK Merek Standar', 'value' => 1500000],
                    ['name' => 'Kipas Angin Standing', 'value' => 300000],
                ],
                'requirements' => ['Berfungsi normal', 'Remote (untuk AC)'],
                'is_active' => true,
            ],
            [
                'name' => 'Perhiasan Emas',
                'category' => 'Perhiasan & Logam Mulia',
                'description' => 'Cincin, kalung, gelang, anting emas',
                'base_value_per_unit' => 1000000,
                'unit' => 'gram',
                'max_loan_percentage' => 85,
                'conditions' => ['24 Karat', '22 Karat', '18 Karat'],
                'brands' => [
                    ['name' => 'Emas 24K (per gram)', 'value' => 1050000],
                    ['name' => 'Emas 22K (per gram)', 'value' => 900000],
                    ['name' => 'Emas 18K (per gram)', 'value' => 750000],
                ],
                'requirements' => ['Sertifikat (jika ada)', 'Berat dalam gram'],
                'is_active' => true,
            ],
            [
                'name' => 'Sepeda / Sepeda Listrik',
                'category' => 'Kendaraan & Transportasi',
                'description' => 'Sepeda gunung, sepeda road, sepeda lipat, atau sepeda listrik',
                'base_value_per_unit' => 500000,
                'unit' => 'unit',
                'max_loan_percentage' => 75,
                'conditions' => ['Baru', 'Sangat Baik', 'Baik', 'Cukup'],
                'brands' => [
                    ['name' => 'Sepeda Listrik Premium', 'value' => 5000000],
                    ['name' => 'Sepeda MTB / Road Premium', 'value' => 3000000],
                    ['name' => 'Sepeda Standar', 'value' => 500000],
                ],
                'requirements' => ['Kondisi rangka baik', 'Rem berfungsi', 'Tidak ada modifikasi ilegal'],
                'is_active' => true,
            ],
            [
                'name' => 'Motor / Sepeda Motor',
                'category' => 'Kendaraan & Transportasi',
                'description' => 'Sepeda motor bebek, matic, sport',
                'base_value_per_unit' => 5000000,
                'unit' => 'unit',
                'max_loan_percentage' => 80,
                'conditions' => ['Sangat Baik', 'Baik', 'Cukup'],
                'brands' => [
                    ['name' => 'Honda / Yamaha Terbaru', 'value' => 18000000],
                    ['name' => 'Honda / Yamaha 2-3 Tahun', 'value' => 13000000],
                    ['name' => 'Motor Lainnya', 'value' => 7000000],
                ],
                'requirements' => ['STNK asli', 'BPKB asli', 'KTP pemilik', 'Pajak tidak mati lebih dari 2 tahun'],
                'is_active' => true,
            ],
        ];

        foreach ($items as $item) {
            JenisBarangGadai::create($item);
        }
    }
}
