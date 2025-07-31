<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\State;

class StateSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        State::truncate();

        $data = [
            ['state_name' => 'Johor'],
            ['state_name' => 'Kedah'],
            ['state_name' => 'Kelantan'],
            ['state_name' => 'Melaka'],
            ['state_name' => 'Negeri Sembilan'],
            ['state_name' => 'Pahang'],
            ['state_name' => 'Perak'],
            ['state_name' => 'Perlis'],
            ['state_name' => 'Penang'],
            ['state_name' => 'Sabah'],
            ['state_name' => 'Sarawak'],
            ['state_name' => 'Selangor'],
            ['state_name' => 'Terengganu'],
            ['state_name' => 'Kuala Lumpur'],
            ['state_name' => 'Labuan'],
            ['state_name' => 'Putrajaya'],
        ];

        State::insert($data);
    }
}