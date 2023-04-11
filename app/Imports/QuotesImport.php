<?php

declare(strict_types=1);

namespace App\Imports;

use App\Models\Perception;
use App\Models\Quote;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class QuotesImport implements ToModel, WithHeadingRow
{
    /**
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $perceptions = Perception::all();

        return new Quote([
            'perception_id' => $perceptions->firstWhere('slug', $row['perception'])->id,
            'content' => $row['content'],
            'author' => $row['author'],
        ]);
    }
}
