<?php

namespace Database\Seeders;

use App\Models\KoperasiInfo;
use Illuminate\Database\Seeder;

class KoperasiInfoSeeder extends Seeder
{
    public function run(): void
    {
        KoperasiInfo::updateOrCreate(['id' => 1], [
            'name'               => 'Koperasi Simpan Pinjam Cempaka',
            'vision'             => 'Menjadi koperasi simpan pinjam yang terpercaya, profesional, dan memberikan manfaat nyata bagi seluruh anggota.',
            'mission'            => "1. Memberikan layanan simpan pinjam yang mudah dan terjangkau\n2. Meningkatkan kesejahteraan anggota melalui pengelolaan keuangan yang baik\n3. Menjalankan usaha gadai dengan transparan dan adil\n4. Mendistribusikan SHU secara merata kepada seluruh anggota aktif",
            'address'            => 'Jl. Cempaka Indah No. 12, Kecamatan Sukajadi, Kota Bandung, Jawa Barat 40152',
            'phone'              => '022-87654321',
            'email'              => 'info@kspcempaka.co.id',
            'bank_name'          => 'BCA',
            'bank_account_number'=> '1234567890',
            'bank_account_name'  => 'Koperasi Simpan Pinjam Cempaka',
            'terms_and_conditions' => "SYARAT DAN KETENTUAN LAYANAN GADAI KSP CEMPAKA\n\n1. Anggota wajib menjadi anggota aktif koperasi\n2. Barang gadai harus dalam kondisi baik dan berfungsi\n3. Tenor gadai maksimal 4 bulan\n4. Bunga gadai sebesar 8% per bulan dari nilai pinjaman\n5. Barang yang tidak ditebus setelah bulan ke-5 akan dilelang\n6. Pembayaran bunga dilakukan setiap bulan\n7. Nilai taksir ditentukan oleh pengurus koperasi\n8. Maksimal pinjaman 80% dari nilai taksir",
        ]);
    }
}
