<?php
namespace Scriber\Bundle\JsTranslationBundle\Content;

class BazingaJsResponseContent implements ResponseContentInterface
{
    /**
     * @param array $translations
     *
     * @return string
     */
    public static function getContent(array $translations): string
    {
        $translations = ['translations' => $translations];
        return sprintf(
            '(function(t){t.fromJSON(%s);})(Translator);',
            json_encode($translations)
        );
    }
}
