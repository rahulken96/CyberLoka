<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\HeadOfFamily>
 */
class HeadOfFamilyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('id_ID');

        return [
            'date_of_birth'         => $faker->dateTimeBetween('1980-01-01', '2011-12-31')->format('Y-m-d'),
            'image'                 => $faker->imageUrl(),
            'occupation'            => $faker->jobTitle(),
            'nik'                   => $faker->nik(),
            'gender'                => $faker->randomElement(['pria', 'wanita']),
            'martial_status'        => $faker->randomElement(['single', 'menikah']),
        ];
    }
}
