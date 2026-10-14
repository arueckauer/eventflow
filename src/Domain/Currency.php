<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function in_array;

final readonly class Currency
{
    /**
     * @throws UnknownCurrencyException
     */
    public static function fromString(string $code): self
    {
        if (!in_array($code, ['EUR', 'GBP'], true)) {
            throw UnknownCurrencyException::from($code);
        }

        return new self($code);
    }

    private function __construct(private string $code)
    {
    }

    public function asString(): string
    {
        return $this->code;
    }

    public function equals(self $other): bool
    {
        return $this->code === $other->code;
    }
}
