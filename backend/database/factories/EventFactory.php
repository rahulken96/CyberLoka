<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Event>
 */
class EventFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('id_ID');

        $events = [
            'Penyuluhan Kesehatan Warga',
            'Kerja Bakti Rutin Desa',
            'Pasar Malam Rakyat',
            'Lomba 17 Agustus',
            'Rapat Karang Taruna',
            'Pembagian Sembako Gratis',
            'Sosialisasi Pertanian Modern',
            'Pentas Seni Tradisional',
            'Pelatihan UMKM Desa',
            'Jalan Sehat Bersama'
        ];

        return [
            'event_code'    => (string) Str::uuid()->toString(),
            'image'         => $faker->imageUrl(),
            'name'          => $faker->unique()->randomElement($events),
            'description'   => $faker->paragraph(),
            'price'         => $faker->numberBetween(10000, 1000000),
            'date_event'    => $faker->dateTimeBetween('now', '+1 year')->format('Y-m-d H:i:s'),
            'is_active'     => $faker->boolean(70),
        ];
    }
}
