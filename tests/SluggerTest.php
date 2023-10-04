<?php

namespace App\Tests;

use App\Twig\Extension\AppExtension;
use PHPUnit\Framework\TestCase;

class SluggerTest extends TestCase
{
    /**
     * @dataProvider getSlugs
     */
    public function testSlug($testValue, $expected): void
    {
        $slugger = new AppExtension();

        $this->assertSame($slugger->slugify($testValue), $expected);
    }

    public function testSomething()
    {
        self::assertTrue(true);
    }

    public function getSlugs()
    {
        return [
            ['hello ben', 'hello-ben'],
            ['cell phone', 'cell-phone'],
        ];
    }
}
