<?php

namespace App\Twig\Extension;

use App\Twig\Runtime\TypeCastingExtensionRuntime;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class TypeCastingExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('int', function ($value) {
                return (int) $value;
            }),
            new TwigFilter('float', function ($value) {
                return (float) $value;
            }),
            new TwigFilter('string', function ($value) {
                return (string) $value;
            }),
            new TwigFilter('bool', function ($value) {
                return (bool) $value;
            }),
            new TwigFilter('array', function (object $value) {
                return (array) $value;
            }),
            new TwigFilter('object', function (array $value) {
                return (object) $value;
            }),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('function_name', [TypeCastingExtensionRuntime::class, 'doSomething']),
        ];
    }
}
