<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        Schema::disableForeignKeyConstraints();
        $this->call(AdminSeeder::class);
        $this->call(ChucNangSeeder::class);
        $this->call(QuyenSeeder::class);
        $this->call(KhuVucSeeder::class);
        $this->call(BanSeeder::class);
        $this->call(DanhMucSeeder::class);
        $this->call(MonAnSeeder::class);
        $this->call(NhaCungCapSeeder::class);
        $this->call(MaGiamGiaSeeder::class);

    }
}
