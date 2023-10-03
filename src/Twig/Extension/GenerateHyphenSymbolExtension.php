<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\GenerateHyphenSymbolRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class GenerateHyphenSymbolExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            // If your filter generates SAFE HTML, you should add a third
            // parameter: ['is_safe' => ['html']]
            // Reference: https://twig.symfony.com/doc/3.x/advanced.html#automatic-escaping
            new TwigFilter('filter_name', [GenerateHyphenSymbolRuntime::class, 'doSomething']),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('generateHyphen', [$this, 'generateHyphen']),
        ];
    }

    public function generateHyphen(string $category, int $numberOfUnderscore)
    {
        $result = '';
        for ($i = 1; $i <= $numberOfUnderscore; $i++) {
            $result .= '-';
        }

        return $result.$category;
    }
}
