<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@mony.id'], [
            'name'           => 'Admin MONY',
            'email'          => 'admin@mony.id',
            'phone'          => '081234567890',
            'nik'            => '3201010101010001',
            'password'       => Hash::make('Admin123!'),
            'role'           => 'admin',
            'account_status' => 'active',
            'address'        => 'Jl. Admin No. 1, Bandung',
        ]);

        User::updateOrCreate(['email' => 'pengurus@mony.id'], [
            'name'           => 'Budi Pengurus',
            'email'          => 'pengurus@mony.id',
            'phone'          => '081234567891',
            'nik'            => '3201010101010002',
            'password'       => Hash::make('Pengurus123!'),
            'role'           => 'pengurus',
            'account_status' => 'active',
            'address'        => 'Jl. Pengurus No. 2, Bandung',
        ]);

        $anggota = [
            [
                'name'           => 'Sari Anggota',
                'email'          => 'sari@mony.id',
                'phone'          => '081234567892',
                'nik'            => '3201010101010003',
                'password'       => Hash::make('Anggota123!'),
                'role'           => 'anggota',
                'account_status' => 'active',
                'address'        => 'Jl. Anggota No. 3, Bandung',
            ],
            [
                'name'           => 'Dedi Wirawan',
                'email'          => 'dedi@mony.id',
                'phone'          => '081234567893',
                'nik'            => '3201010101010004',
                'password'       => Hash::make('Anggota123!'),
                'role'           => 'anggota',
                'account_status' => 'active',
                'address'        => 'Jl. Merpati No. 7, Bandung',
            ],
            [
                'name'           => 'Rina Kusuma',
                'email'          => 'rina@mony.id',
                'phone'          => '081234567894',
                'nik'            => '3201010101010005',
                'password'       => Hash::make('Anggota123!'),
                'role'           => 'anggota',
                'account_status' => 'active',
                'address'        => 'Jl. Melati No. 12, Bandung',
            ],
        ];

        foreach ($anggota as $a) {
            User::updateOrCreate(['email' => $a['email']], $a);
        }
    }
}
