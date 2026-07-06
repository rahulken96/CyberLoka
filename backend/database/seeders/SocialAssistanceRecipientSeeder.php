<?php

namespace Database\Seeders;

use App\Models\HeadOfFamily;
use App\Models\SocialAssistance;
use App\Models\SocialAssistanceRecipient;
use Database\Factories\SocialAssistanceRecipientFactory;
use Illuminate\Database\Seeder;

class SocialAssistanceRecipientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $socialAssists   = SocialAssistance::all();
        $headOfFamilies  = HeadOfFamily::all();

        foreach ($socialAssists as $socialAssist) {
            foreach ($headOfFamilies as $headOfFamily) {
                SocialAssistanceRecipientFactory::new()->create([
                    'social_code'   => $socialAssist->social_code,
                    'family_code'   => $headOfFamily->family_code
                ]);
            }
        }
    }
}
