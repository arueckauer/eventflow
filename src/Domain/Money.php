<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

/**
 * Represents a monetary amount in a single currency, for example "12.34 EUR".
 *
 * The amount is stored in the currency's minor unit (cents) as an integer, so
 * that no rounding errors can occur. A Money is immutable: the arithmetic
 * operations return new instances and never modify the operands.
 *
 * The amount may not be negative when a Money is first created, but it may
 * become negative as the result of a subtraction, for example to express a
 * refund.
 */
final readonly class Money
{
    /**
     * @throws NegativeAmountException
     * @throws UnknownCurrencyException
     */
    public static function from(int $amount, string $currency): self
    {
        if ($amount < 0) {
            throw NegativeAmountException::from($amount);
        }

        return new self($amount, Currency::fromCode($currency));
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

    /**
     * @throws CurrencyMismatchException
     */
    public function add(self $other): self
    {
        $this->ensureSameCurrency($other);

        return new self($this->amount + $other->amount, $this->currency);
    }

    /**
     * @throws CurrencyMismatchException
     */
    public function subtract(self $other): self
    {
        $this->ensureSameCurrency($other);

        return new self($this->amount - $other->amount, $this->currency);
    }

    public function equals(self $other): bool
    {
        return $this->amount === $other->amount && $this->currency === $other->currency;
    }

    /**
     * @throws CurrencyMismatchException
     */
    private function ensureSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw CurrencyMismatchException::between($this->currency, $other->currency);
        }
    }
}
