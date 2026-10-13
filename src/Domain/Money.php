<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

final readonly class Money
{
    /**
     * @throws NegativeAmountException
     * @throws UnknownCurrencyException
     */
    public static function from(int $amount, string $currencyCode): self
    {
        if ($amount < 0) {
            throw NegativeAmountException::fromAmount($amount);
        }

        return new self($amount, Currency::fromCode($currencyCode));
    }

    private function __construct(
        public int $amount,
        public Currency $currency,
    ) {
    }

    /**
     * @throws CurrencyMismatchException
     */
    public function add(self $money): self
    {
        $this->ensureSameCurrency($money);

        return new self($this->amount + $money->amount, $this->currency);
    }

    /**
     * @throws CurrencyMismatchException
     */
    public function subtract(self $money): self
    {
        $this->ensureSameCurrency($money);

        return new self($this->amount - $money->amount, $this->currency);
    }

    /**
     * @throws CurrencyMismatchException
     */
    private function ensureSameCurrency(self $money): void
    {
        if ($this->currency !== $money->currency) {
            throw CurrencyMismatchException::from($this->currency, $money->currency);
        }
    }
}
