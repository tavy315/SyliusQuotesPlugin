<?php

declare(strict_types=1);

namespace Tavy315\SyliusQuotesPlugin\Context;

use Sylius\Component\Core\Context\ShopperContextInterface;
use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Tavy315\SyliusQuotesPlugin\Entity\CustomerQuoteInterface;
use Tavy315\SyliusQuotesPlugin\Entity\CustomerQuoteProduct;
use Tavy315\SyliusQuotesPlugin\Repository\CustomerQuoteRepositoryInterface;
use Tavy315\SyliusQuotesPlugin\Repository\ProductRepositoryInterface;

final class QuoteContext implements QuoteContextInterface
{
    private CustomerQuoteRepositoryInterface $customerQuoteRepository;

    private ProductRepositoryInterface $productRepository;

    private ShopperContextInterface $shopperContext;

    private TokenStorageInterface $tokenStorage;

    public function __construct(
        CustomerQuoteRepositoryInterface $customerQuoteRepository,
        ProductRepositoryInterface $productRepository,
        ShopperContextInterface $shopperContext,
        TokenStorageInterface $tokenStorage
    ) {
        $this->customerQuoteRepository = $customerQuoteRepository;
        $this->productRepository = $productRepository;
        $this->shopperContext = $shopperContext;
        $this->tokenStorage = $tokenStorage;
    }

    public function getCustomerQuote(string $documentNo): CustomerQuoteInterface
    {
        $customerQuote = $this->customerQuoteRepository->findOneByCustomerAndDocument($this->getCustomer(), $documentNo);

        if ($customerQuote === null) {
            throw new NotFoundHttpException();
        }

        return $customerQuote;
    }

    /**
     * @return array<CustomerQuoteProduct>
     */
    public function getQuoteProducts(CustomerQuoteInterface $quote, ?int $limit = null): array
    {
        $products = [];

        $quoteProducts = \array_slice($quote->getProducts(), 0, $limit);

        foreach ($quoteProducts as $product) {
            $customerQuoteProduct = CustomerQuoteProduct::fromArray($product);

            if ($product['no'] !== '') {
                $customerQuoteProduct->product = $this->productRepository
                    ->createShopListQueryBuilder(
                        $this->shopperContext->getChannel(),
                        $this->shopperContext->getLocaleCode(),
                        [ $product['no'] ]
                    )
                    ->getQuery()
                    ->getOneOrNullResult();
            }

            $products[] = $customerQuoteProduct;
        }

        return $products;
    }

    private function getCustomer(): CustomerInterface
    {
        $token = $this->tokenStorage->getToken();

        if ($token === null) {
            throw new AuthenticationCredentialsNotFoundException('The token storage contains no authentication token. One possible reason may be that there is no firewall configured for this URL.');
        }

        $user = $token ? $token->getUser() : null;

        if (!($user instanceof ShopUserInterface)) {
            throw new AccessDeniedHttpException();
        }

        $customer = $user->getCustomer();

        if (!($customer instanceof CustomerInterface)) {
            throw new AccessDeniedHttpException();
        }

        return $customer;
    }
}
