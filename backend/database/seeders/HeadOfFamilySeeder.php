<?php

namespace Database\Seeders;

use Database\Factories\FamilyMemberFactory;
use Database\Factories\HeadOfFamilyFactory;
use Database\Factories\UserFactory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class HeadOfFamilySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // UserFactory::new()->count(15)->create()->each(function ($value, $key) {
        //     HeadOfFamilyFactory::new()->count(1)->create([
        //         'user_code' => $value->user_code,
        //     ]);
        // });

        UserFactory::new()->count(15)->create()->each(function ($value, $key) {
            $gender = fake()->randomElement(['pria', 'wanita']);

            $headOfFamily = HeadOfFamilyFactory::new()->create([
                'user_code'      => $value->user_code,
                'gender'         => $gender,
                'martial_status' => 'menikah', // Kepala keluarga pasti menikah jika punya anggota
                'date_of_birth'  => fake()->dateTimeBetween('1960-01-01', '1990-12-31')->format('Y-m-d'), // Usia dewasa
            ]);

            // Kepala keluarga masuk sebagai anggota dengan relasi ayah/ibu
            FamilyMemberFactory::new()->create([
                'family_code'   => $headOfFamily->family_code,
                'user_code'     => $value->user_code,
                'role'          => 'kepala',
                'relation'      => $gender === 'pria' ? 'ayah' : 'ibu', // Relasi sesuai gender
                'martial_status'=> 'menikah',
                'date_of_birth' => $headOfFamily->date_of_birth,
            ]);

            // Anggota keluarga dengan relasi yang unik dan masuk akal
            $memberRelations = $gender === 'pria'
                ? ['ibu', 'anak', 'anak', 'saudara'] // Jika kepala = ayah → pasangan = ibu
                : ['ayah', 'anak', 'anak', 'saudara'] // Jika kepala = ibu  → pasangan = ayah
            ;

            UserFactory::new()->count(4)->create()->each(function ($newVal, $newKey) use ($headOfFamily, $memberRelations) {
                FamilyMemberFactory::new()->create([
                    'family_code'   => $headOfFamily->family_code,
                    'user_code'     => $newVal->user_code,
                    'role'          => 'anggota',
                    'relation'      => $memberRelations[$newKey],
                    'date_of_birth' => $memberRelations[$newKey] === 'anak'
                        ? fake()->dateTimeBetween('2000-01-01', '2020-12-31')->format('Y-m-d') // Anak lebih muda
                        : fake()->dateTimeBetween('1960-01-01', '1995-12-31')->format('Y-m-d'),
                ]);
            });
        });
    }
}
