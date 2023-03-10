<?php

namespace Database\Seeders;

use App\Enums\PerceptionsIds;
use App\Models\Perception;
use App\Models\Quote;
use Illuminate\Database\Seeder;

class QuoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [
            [
                'perception_id' => PerceptionsIds::DATING_RELATIONSHIPS,
                'content' => 'Beauty needs a witness.',
                'author' => 'Zan Perrion',
            ],
            [
                'perception_id' => PerceptionsIds::AMBITION,
                'content' => 'When you strike at a king, you must kill him.',
                'author' => 'Ralph Waldo Emerson',
            ],
            [
                'perception_id' => PerceptionsIds::LEARNING,
                'content' => 'The purpose of knowledge is action, not knowledge.',
                'author' => 'Aristotle',
            ],
            [
                'perception_id' => PerceptionsIds::LEADERSHIP_COLLABORATION,
                'content' => 'If you want to build a ship, don\'t drum up the men to gather wood, divide the work, and give orders. Instead, teach them to yearn for the vast and endless sea.',
                'author' => null,
            ],
            [
                'perception_id' => PerceptionsIds::FINANCE,
                'content' => 'The hardest financial skill is getting the goalpost to stop moving.',
                'author' => null,
            ],
            [
                'perception_id' => PerceptionsIds::HEALTH,
                'content' => 'When bodies become soft, souls lose their power as well.',
                'author' => 'Socrates',
            ],
            [
                'perception_id' => PerceptionsIds::MYSELF,
                'content' => 'It is inevitable that life will be not just very short but very miserable for those who acquire by great toil what they must keep by greater toil.',
                'author' => 'Seneca',
            ],
        ];

        foreach ($data as $datum) {
            Quote::firstOrCreate($datum);
        }
    }
}
