<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;

final class SqlDateExpressions
{
    public static function monthNumber(string $column = 'date'): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(strftime('%m', {$column}) AS INTEGER)",
            'pgsql' => "CAST(EXTRACT(MONTH FROM {$column}) AS INTEGER)",
            default => "MONTH({$column})",
        };
    }

    public static function yearNumber(string $column = 'date'): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "CAST(strftime('%Y', {$column}) AS INTEGER)",
            'pgsql' => "CAST(EXTRACT(YEAR FROM {$column}) AS INTEGER)",
            default => "YEAR({$column})",
        };
    }

    public static function yearMonth(string $column = 'date'): string
    {
        return match (DB::connection()->getDriverName()) {
            'sqlite' => "strftime('%Y-%m', {$column})",
            'pgsql' => "TO_CHAR({$column}, 'YYYY-MM')",
            'sqlsrv' => "FORMAT({$column}, 'yyyy-MM')",
            default => "DATE_FORMAT({$column}, '%Y-%m')",
        };
    }
}
