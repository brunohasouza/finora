<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banksJson = file_get_contents(storage_path('app/private/banks.json'));
        $banks = json_decode($banksJson, true);

        foreach ($banks as $bankData) {
            Bank::updateOrCreate(
                ['code' => $bankData['code']],
                [
                    'name' => $bankData['name'],
                    'shortname' => $bankData['shortname'],
                ]
            );
        }
    }
}
