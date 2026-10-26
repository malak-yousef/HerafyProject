<?php

namespace App\Support;

class Money
{
    /**
     * Currency symbol used across the Herafy and CMS interfaces.
     */
    public const SYMBOL = '₪';

    /**
     * Format a monetary amount consistently for display.
     *
     * The stored value is never modified; only its presentation is normalised
     * to two decimal places followed by the currency symbol.
     */
    public static function format(int|float|string|null $value, string $placeholder = '-'): string
    {
        if ($value === null || $value === '') {
            return $placeholder;
        }

        return number_format((float) $value, 2).' '.self::SYMBOL;
    }
}
