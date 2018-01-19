<?php
namespace Scriber\Bundle\JsTranslationBundle\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\JsTranslationBundle\DependencyInjection\ScriberJsTranslationExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ScriberJsTranslationExtensionTest extends TestCase
{
    public function testConfigurationLoading(): void
    {
        $filesToLoad = [
            'services.xml',
        ];

        $filesToLoadCallbacks = array_map(function ($file) {
            return static::callback(function ($v) use ($file) { return $this->callbackEndsWith($file, $v); });
        }, $filesToLoad);

        $container = $this->createMock(ContainerBuilder::class);

        $container
            ->expects(static::atLeastOnce())
            ->method('fileExists')
            ->withConsecutive(...$filesToLoadCallbacks);

        $extension = new ScriberJsTranslationExtension();
        $extension->load(
            [],
            $container
        );
    }

    private function callbackEndsWith(string $haystack, string $needle): bool
    {
        $length = strlen($needle);
        if ($length === 0) {
            return false;
        }

        return (substr($haystack, -$length) === $needle);
    }
}
