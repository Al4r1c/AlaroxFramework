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

        $suite->addTestSuite('\Tests\lib\ObjetReponseTest');
        $suite->addTestSuite('\Tests\lib\ObjetRequeteTest');
        $suite->addTestSuite('\Tests\lib\HtmlReponse');
        $suite->addTestSuite('\Tests\lib\View');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'LibTests::main') {
    ConfigTests::main();
}