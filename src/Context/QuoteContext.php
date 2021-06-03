<?php

declare(strict_types=1);

namespace Tavy315\SyliusQuotesPlugin\Context;

use Sylius\Component\Core\Model\ShopUserInterface;
use Sylius\Component\Customer\Model\CustomerInterface;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationCredentialsNotFoundException;
use Tavy315\SyliusQuotesPlugin\Entity\CustomerQuoteInterface;
use Tavy315\SyliusQuotesPlugin\Repository\CustomerQuoteRepositoryInterface;

final class QuoteContext implements QuoteContextInterface
{
    private CustomerQuoteRepositoryInterface $customerQuoteRepository;

    private TokenStorageInterface $tokenStorage;

    public function __construct(CustomerQuoteRepositoryInterface $customerQuoteRepository, TokenStorageInterface $tokenStorage)
    {
        $this->customerQuoteRepository = $customerQuoteRepository;
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
