<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use Exception as PhpException;

final class UnknownCurrencyException extends PhpException implements Exception
{
    public static function from(string $code): self
    {
        return new self(
            sprintf('"%s" is not a supported currency', $code),
        );
    }
}
