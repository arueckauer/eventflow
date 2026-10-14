<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use function is_int;

/**
 * An immutable amount in cents. Only arithmetic may produce negative amounts.
 */
final readonly class Money
{
    /**
     * @throws InvalidMoneyAmountException
     * @throws UnknownCurrencyException
     */
    public static function from(int $amount, string $currency): self
    {
        if ($amount < 0) {
            throw InvalidMoneyAmountException::from($amount);
        }

        return new self($amount, Currency::fromString($currency));
    }

    private function __construct(
        private int $amount,
        private Currency $currency,
    ) {
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency->equals($other->currency);
    }

    /**
     * @throws CurrencyMismatchException
     * @throws MoneyOverflowException
     */
    public function add(self $other): self
    {
        $this->ensureSameCurrency($other);

        return $this->withAmount($this->amount + $other->amount);
    }

    /**
     * @throws CurrencyMismatchException
     * @throws MoneyOverflowException
     */
    public function subtract(self $other): self
    {
        $this->ensureSameCurrency($other);

        return $this->withAmount($this->amount - $other->amount);
    }

    private function ensureSameCurrency(self $other): void
    {
        if (!$this->currency->equals($other->currency)) {
            throw CurrencyMismatchException::from($this->currency, $other->currency);
        }
    }

    private function withAmount(float|int $amount): self
    {
        if (!is_int($amount)) {
            throw MoneyOverflowException::integerRangeExceeded();
        }

        return new self($amount, $this->currency);
    }
}
