<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * Thrown when a valid request cannot be completed because of current data state
 * (for example reviewing an order that is not eligible yet).
 *
 * Unlike an HTTP 409 abort, these conflicts are recoverable: controllers catch
 * the exception, roll the transaction back, and redirect back with a friendly
 * Arabic message so the user can act on the feedback.
 */
class BusinessConflictException extends RuntimeException
{
    public function __construct(string $userMessage)
    {
        parent::__construct($userMessage);
    }

    /**
     * The message intended for display to the end user.
     */
    public function userMessage(): string
    {
        return $this->getMessage();
    }
}
