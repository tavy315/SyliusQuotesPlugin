<?php

declare(strict_types=1);

namespace Tavy315\SyliusQuotesPlugin\Controller\Action;

use Sylius\Component\Core\Context\ShopperContextInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tavy315\SyliusQuotesPlugin\Context\QuoteContextInterface;
use Tavy315\SyliusQuotesPlugin\Entity\CustomerQuoteInterface;
use Tavy315\SyliusQuotesPlugin\Entity\CustomerQuoteProduct;
use Tavy315\SyliusQuotesPlugin\Repository\ProductRepositoryInterface;
use Twig\Environment;

final class ListQuoteProductsAction
{
    private QuoteContextInterface $quoteContext;

    private ProductRepositoryInterface $productRepository;

    private ShopperContextInterface $shopperContext;

    private Environment $twig;

    public function __construct(
        QuoteContextInterface $quoteContext,
        ProductRepositoryInterface $productRepository,
        Environment $twig,
        ShopperContextInterface $shopperContext
    ) {
        $this->quoteContext = $quoteContext;
        $this->productRepository = $productRepository;
        $this->shopperContext = $shopperContext;
        $this->twig = $twig;
    }

    public function __invoke(string $document, Request $request): Response
    {
        $quote = $this->quoteContext->getCustomerQuote($document);

        return new Response($this->twig->render('@Tavy315SyliusQuotesPlugin/Account/CustomerQuote/Grid/products.html.twig', [
            'products' => $this->getProducts($quote),
            'quote'    => $quote,
        ]));
    }

    /**
     * @return array<CustomerQuoteProduct>
     */
    private function getProducts(CustomerQuoteInterface $quote): array
    {
        $products = [];

        foreach ($quote->getProducts() as $product) {
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
}
