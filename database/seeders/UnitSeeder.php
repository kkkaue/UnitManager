<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('units')->insert([
            [
                'name'        => 'Ministério Público',
                'description' => 'Description for Unit 1',
                'email'       => 'email@test1.com',
                'phone'       => '1234567890',
                'latitude'    => '12.3456',
                'longitude'   => '45.6789',
                'parent_id'   => null,
            ],
            [
                'name'        => 'DTI',
                'description' => 'Description for Unit 2',
                'email'       => 'email@test12com',
                'phone'       => '1234567890',
                'latitude'    => '12.3456',
                'longitude'   => '45.6789',
                'parent_id'   => '1',
            ],
            [
                'name'        => 'DRH',
                'description' => 'Description for Unit 3',
                'email'       => 'email@test3.com',
                'phone'       => '1234567890',
                'latitude'    => '12.3456',
                'longitude'   => '45.6789',
                'parent_id'   => '1',
            ],
            [
                'name'        => 'DSIS',
                'description' => 'Description for Unit 4',
                'email'       => 'email@test4.com',
                'phone'       => '1234567890',
                'latitude'    => '12.3456',
                'longitude'   => '45.6789',
                'parent_id'   => '2',
            ],
            [
                'name'        => 'INFRA',
                'description' => 'Description for Unit 5',
                'email'       => 'email@test5.com',
                'phone'       => '1234567890',
                'latitude'    => '12.3456',
                'longitude'   => '45.6789',
                'parent_id'   => '2',
            ],
        ]);
    }
}
