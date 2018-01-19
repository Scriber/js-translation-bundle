<?php
namespace Scriber\Bundle\JsTranslationBundle\Tests;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\JsTranslationBundle\ScriberJsTranslationBundle;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ScriberJsTranslationBundleTest extends TestCase
{
    public function testInstanceOfBundle()
    {
        $bundle = new ScriberJsTranslationBundle();

        static::assertInstanceOf(Bundle::class, $bundle);
    }
}
