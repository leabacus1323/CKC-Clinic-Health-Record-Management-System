<?php

namespace App\Services;

use App\Models\EducationalLevel;

class PatientEducationRequirement
{
    public static function requiresCourse(?int $educationalLevelId): bool
    {
        if (! $educationalLevelId) {
            return false;
        }

        $level = EducationalLevel::query()->find($educationalLevelId);

        return $level ? self::levelRequiresCourse($level) : false;
    }

    public static function levelRequiresCourse(EducationalLevel $level): bool
    {
        $levelName = strtolower(trim((string) $level->level_name));
        $yearLevel = (int) preg_replace('/\D+/', '', (string) $level->year_level);

        return str_contains($levelName, 'college')
            && in_array($yearLevel, [1, 2, 3, 4], true);
    }

    public static function courseRequiredMessage(): string
    {
        return 'Course is required when the educational level is College and the year level is 1, 2, 3, or 4.';
    }
}
