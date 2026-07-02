<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialAssistance>
 */
class SocialAssistanceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('id_ID');

        $namaBansos = [
            'Bantuan Langsung Tunai (BLT)',
            'Program Keluarga Harapan (PKH)',
            'Bantuan Pangan Non Tunai (BPNT)',
            'Kartu Sembako',
            'Bantuan Sosial Tunai (BST)',
            'Program Indonesia Pintar (PIP)',
            'Kartu Indonesia Sehat (KIS)',
            'Bantuan Langsung Tunai Dana Desa (BLT-DD)',
            'Bantuan Subsidi Upah (BSU)',
            'Kartu Prakerja',
            'Bantuan Sosial Beras (BSB)',
            'Bantuan UMKM',
        ];

        $kategoriMap = [
            'sembako' => ['Kartu Sembako', 'Bantuan Pangan Non Tunai (BPNT)', 'Bantuan Sosial Beras (BSB)'],
            'uang'    => ['Bantuan Langsung Tunai (BLT)', 'Program Keluarga Harapan (PKH)', 'Bantuan Sosial Tunai (BST)'],
            'bbm'     => ['Bantuan Subsidi Upah (BSU)'],
            'medis'   => ['Kartu Indonesia Sehat (KIS)'],
        ];

        $category = fake()->randomElement(array_keys($kategoriMap));

        return [
            'social_code'  => (string) Str::uuid()->toString(),
            'image'        => $faker->imageUrl(),
            // 'name'         => $faker->randomElement($namaBansos) . ' ' . $faker->company(),
            'name'         => $faker->randomElement($kategoriMap[$category] ?? $namaBansos),
            'category'     => $category,
            'amount'       => $faker->randomFloat(0, 100000, 10000000),
            'provider'     => $faker->company(),
            'description'  => $faker->sentence(),
            'is_available' => $faker->boolean(70),
        ];
    }
}
