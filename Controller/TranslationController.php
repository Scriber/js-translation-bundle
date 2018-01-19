<?php
namespace Scriber\Bundle\JsTranslationBundle\Controller;

use Scriber\Bundle\JsTranslationBundle\Content\BazingaJsResponseContent;
use Scriber\Bundle\JsTranslationBundle\Exception\InvalidPageException;
use Scriber\Bundle\JsTranslationBundle\Translation\TranslationGenerator;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TranslationController
{
    /**
     * @var TranslationGenerator
     */
    private $translationGenerator;

    /**
     * @param TranslationGenerator $translationGenerator
     */
    public function __construct(TranslationGenerator $translationGenerator)
    {
        $this->translationGenerator = $translationGenerator;
    }

    /**
     * @param $page
     * @param $locale
     * @param $_format
     *
     * @return Response
     */
    public function __invoke($page, $locale, $_format): Response
    {
        try {
            $translations = $this->translationGenerator->getTranslations($page, $locale);
        } catch (InvalidPageException $e) {
            throw new NotFoundHttpException($e->getMessage(), $e);
        }


        if ($_format === 'js') {
            //Uh. Need a better way for that without templating
            return new Response(BazingaJsResponseContent::getContent($translations));
        }

        return new JsonResponse($translations);
    }
}
