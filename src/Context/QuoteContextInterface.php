<?php

declare(strict_types=1);

namespace Tavy315\SyliusQuotesPlugin\Context;

use Tavy315\SyliusQuotesPlugin\Entity\CustomerQuoteInterface;
use Tavy315\SyliusQuotesPlugin\Entity\CustomerQuoteProduct;

interface QuoteContextInterface
{
    public function getCustomerQuote(string $documentNo): CustomerQuoteInterface;

    /**
     * @return array<CustomerQuoteProduct>
     */
    public function getQuoteProducts(CustomerQuoteInterface $quote, ?int $limit = null): array;
}
