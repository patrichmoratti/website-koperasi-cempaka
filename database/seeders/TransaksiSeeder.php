<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\JenisBarangGadai;
use App\Models\PengajuanGadai;
use App\Models\TransaksiGadai;
use App\Models\PembayaranGadai;
use App\Models\Simpanan;
use App\Models\BiayaOperasional;
use App\Models\ShuPeriod;
use App\Models\ShuDistribution;
use App\Services\ShuCalculatorService;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class TransaksiSeeder extends Seeder
{
    public function run(): void
    {
        $admin    = User::where('email','admin@mony.id')->first();
        $sari     = User::where('email','sari@mony.id')->first();
        $dedi     = User::where('email','dedi@mony.id')->first();
        $rina     = User::where('email','rina@mony.id')->first();
        $laptop   = JenisBarangGadai::where('name','like','%Laptop%')->first();
        $hp       = JenisBarangGadai::where('name','like','%Smartphone%')->first();
        $emas     = JenisBarangGadai::where('name','like','%Emas%')->first();

        // ── PENGAJUAN & TRANSAKSI GADAI AKTIF ──────────
        $pengajuan1 = PengajuanGadai::create([
            'anggota_id'          => $sari->id,
            'jenis_barang_id'     => $laptop->id,
            'description'         => 'Laptop ASUS ROG Strix G15 2022, RAM 16GB, SSD 512GB',
            'weight_or_quantity'  => '1 unit',
            'condition'           => 'Baik',
            'estimated_value'     => 7000000,
            'loan_request_amount' => 5000000,
            'item_photo_paths'    => [],
            'status'              => 'diterima',
            'processed_by'        => $admin->id,
            'processed_at'        => now()->subDays(20),
            'submitted_at'        => now()->subDays(21),
        ]);

        $transaksi1 = TransaksiGadai::create([
            'anggota_id'         => $sari->id,
            'pengajuan_id'       => $pengajuan1->id,
            'jenis_barang_id'    => $laptop->id,
            'item_description'   => $pengajuan1->description,
            'item_photo_paths'   => [],
            'appraisal_value'    => 6500000,
            'loan_amount'        => 5000000,
            'interest_rate'      => 8.00,
            'pawn_date'          => now()->subDays(20),
            'due_date'           => now()->subDays(20)->addMonths(4),
            'status'             => 'aktif',
            'warehouse_location' => 'Rak A-01',
            'reference_number'   => 'TRX-' . now()->format('Ymd') . '-SARI01',
        ]);

        // Pembayaran bunga bulan 1 untuk transaksi1
        PembayaranGadai::create([
            'transaksi_gadai_id' => $transaksi1->id,
            'payment_type'       => 'bunga',
            'paid_months'        => [now()->subDays(20)->format('Y-m')],
            'amount'             => 400000,
            'status'             => 'confirmed',
            'submitted_at'       => now()->subDays(10),
            'confirmed_by'       => $admin->id,
            'confirmed_at'       => now()->subDays(9),
        ]);

        // ── GADAI DEDI (HP) AKTIF ──────────────────────
        $pengajuan2 = PengajuanGadai::create([
            'anggota_id'          => $dedi->id,
            'jenis_barang_id'     => $hp->id,
            'description'         => 'iPhone 13 Pro Max 256GB Black, kondisi mulus',
            'weight_or_quantity'  => '1 unit',
            'condition'           => 'Sangat Baik',
            'estimated_value'     => 9000000,
            'loan_request_amount' => 6500000,
            'item_photo_paths'    => [],
            'status'              => 'diterima',
            'processed_by'        => $admin->id,
            'processed_at'        => now()->subDays(35),
            'submitted_at'        => now()->subDays(36),
        ]);

        $transaksi2 = TransaksiGadai::create([
            'anggota_id'         => $dedi->id,
            'pengajuan_id'       => $pengajuan2->id,
            'jenis_barang_id'    => $hp->id,
            'item_description'   => $pengajuan2->description,
            'item_photo_paths'   => [],
            'appraisal_value'    => 8500000,
            'loan_amount'        => 6500000,
            'interest_rate'      => 8.00,
            'pawn_date'          => now()->subDays(35),
            'due_date'           => now()->subDays(35)->addMonths(4),
            'status'             => 'aktif',
            'warehouse_location' => 'Rak B-05',
            'reference_number'   => 'TRX-' . now()->format('Ymd') . '-DEDI01',
        ]);

        // ── GADAI RINA (EMAS) SELESAI/DITEBUS ─────────
        $pengajuan3 = PengajuanGadai::create([
            'anggota_id'          => $rina->id,
            'jenis_barang_id'     => $emas->id,
            'description'         => 'Kalung emas 22 karat, berat 5 gram',
            'weight_or_quantity'  => '5 gram',
            'condition'           => '22 Karat',
            'estimated_value'     => 4500000,
            'loan_request_amount' => 3500000,
            'item_photo_paths'    => [],
            'status'              => 'diterima',
            'processed_by'        => $admin->id,
            'processed_at'        => Carbon::now()->subMonths(3),
            'submitted_at'        => Carbon::now()->subMonths(3)->subDay(),
        ]);

        $transaksi3 = TransaksiGadai::create([
            'anggota_id'         => $rina->id,
            'pengajuan_id'       => $pengajuan3->id,
            'jenis_barang_id'    => $emas->id,
            'item_description'   => $pengajuan3->description,
            'item_photo_paths'   => [],
            'appraisal_value'    => 4200000,
            'loan_amount'        => 3500000,
            'interest_rate'      => 8.00,
            'pawn_date'          => Carbon::now()->subMonths(3),
            'due_date'           => Carbon::now()->subMonths(3)->addMonths(4),
            'status'             => 'ditebus',
            'warehouse_location' => 'Brankas C-01',
            'reference_number'   => 'TRX-' . Carbon::now()->subMonths(3)->format('Ymd') . '-RINA01',
        ]);

        PembayaranGadai::create([
            'transaksi_gadai_id' => $transaksi3->id,
            'payment_type'       => 'tebus',
            'paid_months'        => [],
            'amount'             => 3780000,
            'status'             => 'confirmed',
            'submitted_at'       => Carbon::now()->subMonths(1),
            'confirmed_by'       => $admin->id,
            'confirmed_at'       => Carbon::now()->subMonths(1)->addDay(),
        ]);

        // ── SIMPANAN ───────────────────────────────────
        foreach ([$sari, $dedi, $rina] as $user) {
            // Simpanan pokok
            Simpanan::create([
                'anggota_id'    => $user->id,
                'type'          => 'pokok',
                'amount'        => 500000,
                'period_month'  => null,
                'period_year'   => null,
                'status'        => 'confirmed',
                'submitted_at'  => now()->subMonths(6),
                'confirmed_by'  => $admin->id,
                'confirmed_at'  => now()->subMonths(6),
            ]);

            // Simpanan wajib beberapa bulan
            for ($i = 5; $i >= 1; $i--) {
                Simpanan::create([
                    'anggota_id'    => $user->id,
                    'type'          => 'wajib',
                    'amount'        => 100000,
                    'period_month'  => now()->subMonths($i)->month,
                    'period_year'   => now()->subMonths($i)->year,
                    'status'        => 'confirmed',
                    'submitted_at'  => now()->subMonths($i),
                    'confirmed_by'  => $admin->id,
                    'confirmed_at'  => now()->subMonths($i)->addDay(),
                ]);
            }
        }

        // ── BIAYA OPERASIONAL ──────────────────────────
        $biaya = [
            ['date' => now()->subMonths(2), 'category' => 'Gaji',       'amount' => 3000000,  'description' => 'Gaji pengurus bulan lalu'],
            ['date' => now()->subMonths(2), 'category' => 'Listrik',    'amount' => 350000,   'description' => 'Tagihan listrik kantor'],
            ['date' => now()->subMonths(1), 'category' => 'Gaji',       'amount' => 3000000,  'description' => 'Gaji pengurus bulan ini'],
            ['date' => now()->subMonths(1), 'category' => 'Internet',   'amount' => 250000,   'description' => 'Langganan internet'],
            ['date' => now()->subMonths(1), 'category' => 'ATK',        'amount' => 150000,   'description' => 'Alat tulis kantor'],
            ['date' => now(),               'category' => 'Listrik',    'amount' => 380000,   'description' => 'Tagihan listrik'],
            ['date' => now(),               'category' => 'Operasional','amount' => 200000,   'description' => 'Biaya operasional lain-lain'],
        ];

        foreach ($biaya as $b) {
            BiayaOperasional::create(array_merge($b, ['recorded_by' => $admin->id]));
        }

        // ── SHU PERIOD ────────────────────────────────
        $shuPeriod = ShuPeriod::create([
            'year'                => now()->year - 1,
            'status'              => 'published',
            'total_income'        => 8400000,
            'total_expenses'      => 3200000,
            'total_shu'           => 5200000,
            'pct_dana_cadangan'   => 25,
            'pct_jasa_modal'      => 25,
            'pct_jasa_usaha'      => 30,
            'pct_dana_pengurus'   => 10,
            'pct_dana_pendidikan' => 5,
            'pct_dana_sosial'     => 5,
            'alloc_dana_cadangan' => 1300000,
            'alloc_jasa_modal'    => 1300000,
            'alloc_jasa_usaha'    => 1560000,
            'alloc_dana_pengurus' => 520000,
            'alloc_dana_pendidikan' => 260000,
            'alloc_dana_sosial'   => 260000,
        ]);

        $totalSavings = 500000 * 3 + 100000 * 5 * 3;
        $totalInterest = 400000;

        foreach ([$sari, $dedi, $rina] as $user) {
            $savings  = 500000 + 100000 * 5;
            $interest = $user->id === $sari->id ? 400000 : 0;

            ShuDistribution::create([
                'shu_period_id'              => $shuPeriod->id,
                'anggota_id'                 => $user->id,
                'total_savings'              => $savings,
                'member_savings_proportion'  => $savings / $totalSavings,
                'member_jasa_modal'          => 1300000 * ($savings / $totalSavings),
                'total_interest_paid'        => $interest,
                'member_interest_proportion' => $totalInterest > 0 ? $interest / $totalInterest : 0,
                'member_jasa_usaha'          => $totalInterest > 0 ? 1560000 * ($interest / $totalInterest) : 0,
                'total_shu_received'         => (1300000 * ($savings / $totalSavings)) + ($totalInterest > 0 ? 1560000 * ($interest / $totalInterest) : 0),
                'withdrawal_status'          => 'pending',
            ]);
        }
    }
}
