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
                'latitude'    => '0.00890762',
                'longitude'   => '-51.05988005',
                'parent_id'   => null,
            ],
            [
                'name'        => 'DTI',
                'description' => 'Description for Unit 2',
                'email'       => 'email@test12com',
                'phone'       => '1234567890',
                'latitude'    => '0.00890762',
                'longitude'   => '-51.05988005',
                'parent_id'   => '1',
            ],
            [
                'name'        => 'DGP',
                'description' => 'Description for Unit 3',
                'email'       => 'email@test3.com',
                'phone'       => '1234567890',
                'latitude'    => '0.00890762',
                'longitude'   => '-51.05988005',
                'parent_id'   => '1',
            ],
            [
                'name'        => 'DFINC',
                'description' => 'Description for Unit 4',
                'email'       => 'email@test4.com',
                'phone'       => '1234567890',
                'latitude'    => '0.00890762',
                'longitude'   => '-51.05988005',
                'parent_id'   => '1',
            ],
            [
                'name'        => 'DEA',
                'description' => 'Description for Unit 5',
                'email'       => 'email@test5.com',
                'phone'       => '1234567890',
                'latitude'    => '0.00890762',
                'longitude'   => '-51.05988005',
                'parent_id'   => '1',
            ],
            [
                'name'        => 'DTI - Desenvolvimento',
                'description' => 'Description for Unit 6',
                'email'       => 'email@test6.com',
                'phone'       => '1234567890',
                'latitude'    => '0.00890762',
                'longitude'   => '-51.05988005',
                'parent_id'   => '2',
            ],
            [
                'name'        => 'DFINC - Contabilidade',
                'description' => 'Description for Unit 7',
                'email'       => 'email@test7.com',
                'phone'       => '1234567890',
                'latitude'    => '0.00890762',
                'longitude'   => '-51.05988005',
                'parent_id'   => '4',
            ],
            [
                'name'        => 'DEPLAN',
                'description' => 'Description for Unit 8',
                'email'       => 'email@test8.com',
                'phone'       => '1234567890',
                'latitude'    => '0.00890762',
                'longitude'   => '-51.05988005',
                'parent_id'   => '1',
            ],
            [
                'name'        => 'DTI - Infraestrutura',
                'description' => 'Description for Unit 9',
                'email'       => 'email@test9.com',
                'phone'       => '1234567890',
                'latitude'    => '0.00890762',
                'longitude'   => '-51.05988005',
                'parent_id'   => '2',
            ],
            [
                'name'        => 'DFINC - Orçamento',
                'description' => 'Description for Unit 10',
                'email'       => 'email@test10.com',
                'phone'       => '1234567890',
                'latitude'    => '0.00890762',
                'longitude'   => '-51.05988005',
                'parent_id'   => '4',
            ],
            [
                'name'        => 'DFINC - Finanças',
                'description' => 'Description for Unit 11',
                'email'       => 'email@test11.com',
                'phone'       => '1234567890',
                'latitude'    => '0.00890762',
                'longitude'   => '-51.05988005',
                'parent_id'   => '4',
            ],
        ]);
    }
}
