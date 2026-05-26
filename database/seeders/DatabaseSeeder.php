<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Room;
use App\Models\User;
use App\Models\Item;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Roles
        $adminRole = Role::create(['name' => 'admin', 'display_name' => 'Administrator']);
        $petugasRole = Role::create(['name' => 'petugas', 'display_name' => 'Petugas Ruangan']);

        // 2. Seed Users Dasar
        $admin = User::create([
            'name' => 'Admin Puskesmas',
            'email' => 'admin@puskesmas.go.id',
            'password' => Hash::make('password'),
            'role_id' => $adminRole->id,
        ]);

        // 3. Seed 20 Ruangan Puskesmas Standar
        $roomsData = [
            'IGD', 'Poli Umum', 'Poli Gigi', 'Poli KIA', 'Apotek/Farmasi', 
            'Laboratorium', 'Ruang Rawat Inap A', 'Ruang Rawat Inap B', 'Poli Anak', 'Poli Lansia',
            'Ruang Bersalin (VK)', 'Gudang Obat', 'Ruang Tata Usaha', 'Ruang Kepala Puskesmas', 'Kamar Sterilisasi',
            'Poli Gizi', 'Ruang Imunisasi', 'Poli TB-MDR', 'Ruang Pendaftaran', 'Gudang Logistik'
        ];

        foreach ($roomsData as $index => $roomName) {
            $room = Room::create([
                'name' => $roomName,
                'location_floor' => ($index < 12) ? '1' : '2'
            ]);

            // Buat 1 petugas untuk setiap ruangan secara otomatis
            $slug = strtolower(str_replace([' ', '/', '(', ')'], '-', $roomName));
            $petugas = User::create([
                'name' => 'Petugas ' . $roomName,
                'email' => $slug . '@puskesmas.go.id',
                'password' => Hash::make('password'),
                'role_id' => $petugasRole->id,
                'room_id' => $room->id
            ]);

            // Set PJ Ruangan ke petugas yang baru dibuat
            $room->update(['user_id' => $petugas->id]);

            // Dummy Items per Ruangan
            // Ganti bagian Item::create pertama (Tensimeter Digital) jadi seperti ini:
Item::create([
    // Menggunakan index unik + string acak agar tidak bentrok
    'item_code' => 'INV-' . strtoupper(substr($slug, 0, 3)) . '-' . ($index + 1) . rand(10, 99),
    'name' => 'Tensimeter Digital',
    'category' => 'Alat Medis',
    'room_id' => $room->id,
    'qty' => 5,
    'status' => 'Baik'
]);

// Ganti bagian Item::create kedua (AC Split) jadi seperti ini:
Item::create([
    // Menggunakan akhiran index yang berbeda
    'item_code' => 'INV-' . strtoupper(substr($slug, 0, 3)) . '-AC-' . ($index + 1),
    'name' => 'AC Split 1 PK',
    'category' => 'Elektronik',
    'room_id' => $room->id,
    'qty' => 1,
    'status' => ($index % 4 == 0) ? 'Rusak' : 'Baik'
]);
        }
    }
}