<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use Exception as PhpException;

final class NameMustNotBeEmptyException extends PhpException implements Exception
{
    public static function create(): self
    {
        return new self('Attendee name must not be empty.');
    }
}
