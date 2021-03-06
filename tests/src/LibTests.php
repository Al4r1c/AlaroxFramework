<?php
namespace Tests;

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'LibTests::main');
}

class LibTests
{
    public static function main()
    {
        \PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('TestSuite');

        $suite->addTestSuite('\Tests\lib\CurlClientTest');
        $suite->addTestSuite('\Tests\lib\HtmlReponseTest');
        $suite->addTestSuite('\Tests\lib\ObjetReponseTest');
        $suite->addTestSuite('\Tests\lib\ObjetRequeteTest');
        $suite->addTestSuite('\Tests\lib\ParserTest');
        $suite->addTestSuite('\Tests\lib\ParserFactoryTest');
        $suite->addTestSuite('\Tests\lib\RestClientTest');
        $suite->addTestSuite('\Tests\lib\SessionClientTest');
        $suite->addTestSuite('\Tests\lib\ToolsTest');
        $suite->addTestSuite('\Tests\lib\UnparserTest');
        $suite->addTestSuite('\Tests\lib\UnparserFactoryTest');
        $suite->addTestSuite('\Tests\lib\view\AbstractViewTest');
        $suite->addTestSuite('\Tests\lib\view\PlainViewTest');
        $suite->addTestSuite('\Tests\lib\view\TemplateViewTest');
        $suite->addTestSuite('\Tests\lib\view\ViewFactoryTest');
        $suite->addTestSuite('\Tests\lib\compressor\CompressorTest');
        $suite->addTestSuite('\Tests\lib\compressor\CompressorFactoryTest');
        $suite->addTestSuite('\Tests\lib\TwigEnvFactoryTest');


        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'LibTests::main') {
    LibTests::main();
}