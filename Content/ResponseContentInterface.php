<?php
namespace Scriber\Bundle\JsTranslationBundle\Content;

interface ResponseContentInterface
{
    /**
     * @param array $translations
     *
     * @return string
     */
    public static function getContent(array $translations): string;
}
