<?php

declare(strict_types=1);

namespace Tavy315\SyliusQuotesPlugin\Context;

use Tavy315\SyliusQuotesPlugin\Entity\CustomerQuoteInterface;

interface QuoteContextInterface
{
    public function getCustomerQuote(string $documentNo): CustomerQuoteInterface;
}
