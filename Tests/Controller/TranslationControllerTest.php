<?php
namespace Scriber\Bundle\JsTranslationBundle\Tests\Controller;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Scriber\Bundle\JsTranslationBundle\Controller\TranslationController;
use Scriber\Bundle\JsTranslationBundle\Exception\InvalidPageException;
use Scriber\Bundle\JsTranslationBundle\Translation\TranslationGenerator;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TranslationControllerTest extends TestCase
{
    /**
     * @var TranslationGenerator|MockObject
     */
    private $generator;

    /**
     * @var Request|MockObject
     */
    private $request;

    protected function setUp()
    {
        $this->generator = $this->createMock(TranslationGenerator::class);
    }

    protected function tearDown()
    {
        $this->generator = null;
    }

    public function testInvalidPage()
    {
        $this->generator
            ->expects(static::once())
            ->method('getTranslations')
            ->willThrowException(new InvalidPageException());

        $controller = new TranslationController($this->generator);

        $this->expectException(NotFoundHttpException::class);
        $controller('', '', '');
    }

    public function testJsonResponse()
    {
        $page = 'test';
        $locale = 'en';
        $format = 'json';

        $translations = [
            'test' => 'test'
        ];

        $this->generator
            ->expects(static::once())
            ->method('getTranslations')
            ->with($page, $locale)
            ->willReturn($translations);

        $controller = new TranslationController($this->generator);
        $result = $controller($page, $locale, $format);

        static::assertInstanceOf(JsonResponse::class, $result);
        static::assertEquals(200, $result->getStatusCode());
        static::assertEquals(json_encode($translations), $result->getContent());
    }

    public function testJsResponse()
    {
        $page = 'test';
        $locale = 'en';
        $format = 'js';

        $translations = [
            'test' => 'test'
        ];

        $this->generator
            ->expects(static::once())
            ->method('getTranslations')
            ->with($page, $locale)
            ->willReturn($translations);

        $controller = new TranslationController($this->generator);
        $result = $controller($page, $locale, $format);

        static::assertInstanceOf(Response::class, $result);
        static::assertEquals(200, $result->getStatusCode());
        static::assertContains(json_encode($translations), $result->getContent());
    }
}
