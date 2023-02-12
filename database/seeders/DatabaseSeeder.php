<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use App\Models\ProductType;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $productTypeEnabled = \App\Models\ProductType::create([
            'name' => 'Enabled',
            'status' => 1
        ]);

        $productTypeDisabled = \App\Models\ProductType::create([
            'name' => 'Disabled',
            'status' => 0
        ]);

        \App\Models\Product::factory(6)
            ->for($productTypeEnabled)
            ->create();

        \App\Models\Product::factory(4)
            ->for($productTypeDisabled)
            ->create();
    }
}
