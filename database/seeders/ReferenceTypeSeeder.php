<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\ReferenceType;

class ReferenceTypeSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        ReferenceType::truncate();

        $data = [
            ['reference_type' => 'Spouse'],
            ['reference_type' => 'Guarantor'],
            ['reference_type' => 'Father'],
            ['reference_type' => 'Mother'],
            ['reference_type' => 'Brother'],
            ['reference_type' => 'Sister'],
            ['reference_type' => 'Relative'],
            ['reference_type' => 'Friend'],
        ];

        ReferenceType::insert($data);
    }
}
