<?php

namespace App\Models\Concerns;

use LogicException;

trait AppendOnly
{
    protected static function bootAppendOnly(): void
    {
        static::updating(static fn () => throw new LogicException('This security record is append-only.'));
        static::deleting(static fn () => throw new LogicException('This security record cannot be deleted.'));
    }
}
