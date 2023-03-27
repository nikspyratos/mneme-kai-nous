<?php

namespace App\Helpers;

class EnumHelper
{
    public static function enumToArray(array $cases = []): array
    {
        $data = [];
        foreach ($cases as $case) {
            $data[$case->name] = $case->value;
        }

        return $data;
    }

    public static function enumToFilamentOptionArray(array $cases = []): array
    {
        $data = [];
        foreach ($cases as $case) {
            $data[$case->value] = $case->value;
        }

        return $data;
    }
}
