<?php
namespace Scriber\Bundle\JsTranslationBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\JsTranslationBundle\DependencyInjection\Configuration;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class ConfigurationTest extends TestCase
{
    public function testGetConfigTreeBuilder()
    {
        $root = 'scriber_js_translation';

        $configuration = new Configuration();

        $result = $configuration->getConfigTreeBuilder();
        $nameResult = $result->buildTree()->getName();

        static::assertInstanceOf(TreeBuilder::class, $result);
        static::assertEquals($root, $nameResult);
    }
}
