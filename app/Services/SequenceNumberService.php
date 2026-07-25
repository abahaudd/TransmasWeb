<?php

namespace App\Services;

use App\Models\SequenceNumberFormat;
use Illuminate\Support\Facades\DB;
use RuntimeException;

/**
 * Generates formatted, gapless sequence numbers (invoice/receipt/employee/...)
 * from the sequence_number_formats table: {prefix}{separator}{zero-padded incrementer}.
 */
class SequenceNumberService
{
    /**
     * Atomically advance the counter for a category and return the
     * formatted number. Row-locked so concurrent requests never collide.
     */
    public function next(string $category): string
    {
        return DB::transaction(function () use ($category): string {
            $format = SequenceNumberFormat::query()
                ->where('category', $category)
                ->lockForUpdate()
                ->first();

            if (! $format) {
                throw new RuntimeException("No sequence number format configured for category [{$category}].");
            }

            $format->incrementer += 1;
            $format->save();

            return $this->format($format);
        });
    }

    /**
     * Preview the current formatted number without advancing the counter.
     */
    public function current(string $category): ?string
    {
        $format = SequenceNumberFormat::query()->where('category', $category)->first();

        return $format ? $this->format($format) : null;
    }

    protected function format(SequenceNumberFormat $format): string
    {
        $number = $format->length
            ? str_pad((string) $format->incrementer, $format->length, '0', STR_PAD_LEFT)
            : (string) $format->incrementer;

        return collect([$format->prefix, $number])
            ->filter(fn (?string $part): bool => filled($part))
            ->implode($format->separator ?? '');
    }
}
