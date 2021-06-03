<?php

declare(strict_types=1);

namespace Tavy315\SyliusQuotesPlugin\Repository;

use Doctrine\ORM\QueryBuilder;
use Sylius\Component\Customer\Model\CustomerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Tavy315\SyliusQuotesPlugin\Entity\CustomerQuoteInterface;

interface CustomerQuoteRepositoryInterface extends RepositoryInterface
{
    public function findOneByCustomerAndDocument(CustomerInterface $customer, string $documentNo): ?CustomerQuoteInterface;

    public function createByCustomerQueryBuilder($customerId): QueryBuilder;
}
