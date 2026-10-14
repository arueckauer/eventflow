<?php declare(strict_types=1);
namespace Thephpcc\EventFlow\Domain;

use Exception as PhpException;

final class MoneyOverflowException extends PhpException implements Exception
{
    public function __construct()
    {
        parent::__construct('The money amount exceeds the integer range');
    }
}
