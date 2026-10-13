<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function sprintf;
use Exception as PhpException;

final class UnknownCurrencyException extends PhpException implements Exception
{
    public static function fromCode(string $code): self
    {
        return new self(sprintf('Currency code "%s" is not supported.', $code));
    }
}
