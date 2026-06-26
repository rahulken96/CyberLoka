<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FamilyMember>
 */
class FamilyMemberFactory extends Factory
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
            'family_member_code'    => (string) Str::uuid()->toString(),
            'date_of_birth'         => $faker->dateTimeBetween('1980-01-01', '2011-12-31')->format('Y-m-d'),
            'image'                 => $faker->imageUrl(),
            'occupation'            => $faker->jobTitle(),
            'nik'                   => $faker->nik(),
            'gender'                => $faker->randomElement(['pria', 'wanita']),
            'martial_status'        => $faker->randomElement(['single', 'menikah']),
            'relation'              => $faker->randomElement(["ibu", "ayah", "anak", "saudara"]),
            'role'                  => $faker->randomElement(["kepala", "anggota"]),
        ];
    }
}
