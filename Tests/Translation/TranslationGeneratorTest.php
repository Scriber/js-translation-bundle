<?php
namespace Scriber\Bundle\JsTranslationBundle\Tests\Translation;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Scriber\Bundle\JsTranslationBundle\Exception\InvalidPageException;
use Scriber\Bundle\JsTranslationBundle\Translation\TranslationGenerator;
use Symfony\Component\Translation\MessageCatalogueInterface;
use Symfony\Component\Translation\TranslatorBagInterface;

class TranslationGeneratorTest extends TestCase
{
    /**
     * @var TranslatorBagInterface|MockObject
     */
    private $translator;

    protected function setUp()
    {
        $this->translator = $this->createMock(TranslatorBagInterface::class);
    }

    protected function tearDown()
    {
        $this->translator = null;
    }

    public function testGetPageConfigWithInvalidPage()
    {
        $generator = new TranslationGenerator($this->translator, ['pages']);

        $this->expectException(InvalidPageException::class);
        $generator->getPageConfig('');
    }

    public function testGetPageConfig()
    {
        $page = 'test';
        $pageConfig = ['test' => 'test'];
        $config = [
            'pages' => [
                $page => $pageConfig
            ]
        ];

        $generator = new TranslationGenerator($this->translator, $config);

        $result = $generator->getPageConfig($page);

        static::assertEquals($pageConfig, $result);
    }

    public function testGetAllTranslationsFromDomain()
    {
        $locale = 'en';
        $domain = 'test';
        $page = 'test';

        $config = [
            'pages' => [
                $page => [
                    $domain => []
                ]
            ]
        ];

        $domainMessages = [
            'test' => 'test'
        ];

        $expected = [
            $locale => [
                $domain => $domainMessages
            ]
        ];

        $catalogue = $this->createMock(MessageCatalogueInterface::class);

        $this->translator
            ->expects(static::once())
            ->method('getCatalogue')
            ->with($locale)
            ->willReturn($catalogue);

        $catalogue
            ->expects(static::once())
            ->method('all')
            ->with($domain)
            ->willReturn($domainMessages);

        $catalogue
            ->expects(static::once())
            ->method('getLocale')
            ->willReturn($locale);

        $catalogue
            ->expects(static::once())
            ->method('getFallbackCatalogue')
            ->willReturn(null);

        $generator = new TranslationGenerator($this->translator, $config);

        $result = $generator->getTranslations($page, $locale);
        static::assertEquals($expected, $result);
    }

    public function testGetMessagesWithFallback()
    {
        $locale = 'pl';
        $fallbackLocale = 'en';

        $domain = 'test';
        $page = 'test';

        $config = [
            'pages' => [
                $page => [
                    $domain => []
                ]
            ]
        ];

        $plMessages = [
            'test' => 'test',
            'test2' => 'test2'
        ];

        $enMessages = [
            'test' => 'test-en',
            'test3' => 'test'
        ];

        $enExpected = array_diff_key($enMessages, $plMessages);

        $expected = [
            $locale => [
                $domain => $plMessages
            ],
            $fallbackLocale => [
                $domain => $enExpected
            ]

        ];

        $catalogue = $this->createMock(MessageCatalogueInterface::class);

        $this->translator
            ->expects(static::once())
            ->method('getCatalogue')
            ->with($locale)
            ->willReturn($catalogue);

        $catalogue
            ->expects(static::atLeastOnce())
            ->method('all')
            ->with($domain)
            ->willReturnOnConsecutiveCalls($plMessages, $enMessages);

        $catalogue
            ->expects(static::atLeastOnce())
            ->method('getLocale')
            ->willReturnOnConsecutiveCalls($locale, $fallbackLocale);

        $catalogue
            ->expects(static::atLeastOnce())
            ->method('getFallbackCatalogue')
            ->willReturnOnConsecutiveCalls($catalogue, null);

        $generator = new TranslationGenerator($this->translator, $config);

        $result = $generator->getTranslations($page, $locale);
        static::assertEquals($expected, $result);
    }

    public function testGetMessagesWithEmptyDomain()
    {
        $locale = 'en';
        $domain = 'test';
        $page = 'test';

        $config = [
            'pages' => [
                $page => [
                    $domain => []
                ]
            ]
        ];

        $domainMessages = [];

        $expected = [];

        $catalogue = $this->createMock(MessageCatalogueInterface::class);

        $this->translator
            ->expects(static::once())
            ->method('getCatalogue')
            ->with($locale)
            ->willReturn($catalogue);

        $catalogue
            ->expects(static::once())
            ->method('all')
            ->with($domain)
            ->willReturn($domainMessages);

        $catalogue
            ->expects(static::once())
            ->method('getLocale')
            ->willReturn($locale);

        $catalogue
            ->expects(static::once())
            ->method('getFallbackCatalogue')
            ->willReturn(null);

        $generator = new TranslationGenerator($this->translator, $config);

        $result = $generator->getTranslations($page, $locale);
        static::assertEquals($expected, $result);
    }


    public function testGetSelectedMessagesFromDomain()
    {
        $locale = 'en';
        $domain = 'test';
        $page = 'test';

        $config = [
            'pages' => [
                $page => [
                    $domain => ['test']
                ],
            ],
        ];

        $domainMessages = [
            'test' => 'test',
            'test2' => 'test2'
        ];

        $expected = [
            $locale => [
                $domain => [
                    'test' => 'test'
                ],
            ],
        ];

        $catalogue = $this->createMock(MessageCatalogueInterface::class);

        $this->translator
            ->expects(static::once())
            ->method('getCatalogue')
            ->with($locale)
            ->willReturn($catalogue);

        $catalogue
            ->expects(static::once())
            ->method('all')
            ->with($domain)
            ->willReturn($domainMessages);

        $catalogue
            ->expects(static::once())
            ->method('getLocale')
            ->willReturn($locale);

        $catalogue
            ->expects(static::once())
            ->method('getFallbackCatalogue')
            ->willReturn(null);

        $generator = new TranslationGenerator($this->translator, $config);

        $result = $generator->getTranslations($page, $locale);
        static::assertEquals($expected, $result);
    }

}
