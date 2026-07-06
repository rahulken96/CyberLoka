<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\SocialAssistanceRecipient>
 */
class SocialAssistanceRecipientFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = \Faker\Factory::create('id_ID');

        $banks = [
            'bri',
            'bni',
            'bca',
            'mandiri',
            'bsi',
            'jago',
            'cimb',
        ];

        return [
            'social_recipient_code' => (string) Str::uuid()->toString(),
            'bank'                  => $faker->randomElement($banks),
            'account_bank'          => $faker->randomNumber(9, true),
            'amount'                => $faker->randomNumber(5, true),
            'image'                 => $faker->imageUrl(),
            'reason'                => $faker->sentence(6),
            'status'                => $faker->randomElement(['pending', 'approved', 'rejected']),
        ];
    }
}
