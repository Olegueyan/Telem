<?php

namespace App\Extension;

use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter("price", [$this, 'formatPrice']),
            new TwigFilter("dateFr", [$this, 'dateInFrenchFormat'])
        ];
    }

    public function getFunctions()
    {
        return [
            new TwigFunction("dateFr", [$this, 'dateInFrenchFormat'])
        ];
    }

    public function formatPrice(int $priceInCents, int $decimals = 2): string
    {
        $formattedPrice = number_format(($priceInCents/100), $decimals);

        return $formattedPrice." €";
    }

    public function dateInFrenchFormat(\DateTimeInterface $date): string
    {
        return date_format($date, 'd/m/Y');
    }
}