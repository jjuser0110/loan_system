<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Bouncer;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	Bouncer::role()->firstOrCreate([ 'name' => 'admin', 'title' => 'Admin', ]);
    }
}
