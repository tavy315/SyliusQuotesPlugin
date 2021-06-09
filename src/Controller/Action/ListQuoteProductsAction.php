<?php

declare(strict_types=1);

namespace Tavy315\SyliusQuotesPlugin\Controller\Action;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tavy315\SyliusQuotesPlugin\Context\QuoteContextInterface;
use Twig\Environment;

final class ListQuoteProductsAction
{
    private ?int $productLimit;

    private QuoteContextInterface $quoteContext;

    private Environment $twig;

    public function __construct(QuoteContextInterface $quoteContext, Environment $twig, ?int $productLimit)
    {
        $this->productLimit = $productLimit;
        $this->quoteContext = $quoteContext;
        $this->twig = $twig;
    }

    public function __invoke(string $document, Request $request): Response
    {
        $quote = $this->quoteContext->getCustomerQuote($document);

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse([
                'html' => $this->twig->render('@Tavy315SyliusQuotesPlugin/Account/CustomerQuote/Grid/_products.html.twig', [
                    'products' => $this->quoteContext->getQuoteProducts($quote),
                    'quote'    => $quote,
                ]),
            ]);
        }

        return new Response($this->twig->render('@Tavy315SyliusQuotesPlugin/Account/CustomerQuote/Grid/show.html.twig', [
            'products' => $this->quoteContext->getQuoteProducts($quote, $this->productLimit),
            'quote'    => $quote,
        ]));
    }
}
