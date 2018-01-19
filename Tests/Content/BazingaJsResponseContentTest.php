<?php
namespace Scriber\Bundle\JsTranslationBundle\Tests\Content;

use PHPUnit\Framework\TestCase;
use Scriber\Bundle\JsTranslationBundle\Content\BazingaJsResponseContent;
use Scriber\Bundle\JsTranslationBundle\Content\ResponseContentInterface;

class BazingaJsResponseContentTest extends TestCase
{
    public function testImplementsResponseContentInterface()
    {
        static::assertInstanceOf(
            ResponseContentInterface::class,
            new BazingaJsResponseContent()
        );
    }

    public function testReturnedString()
    {
        $data = ['test' => 'test'];
        $jsonData = json_encode($data);

        $expected = '(function(t){t.fromJSON(' . $jsonData . ');})(Translator);';
        $result = BazingaJsResponseContent::getContent($data);

        static::assertEquals($expected, $result);
    }
}
