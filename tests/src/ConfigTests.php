<?php
namespace Tests;

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'ConfigTests::main');
}

class ConfigTests
{
    public static function main()
    {
        \PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('TestSuite');

        $suite->addTestSuite('\Tests\config\ConfigTest');
        $suite->addTestSuite('\Tests\config\ControllerFactoryTest');
        $suite->addTestSuite('\Tests\config\GlobalVarsTest');
        $suite->addTestSuite('\Tests\config\InternationalizationTest');
        $suite->addTestSuite('\Tests\config\LangueTest');
        $suite->addTestSuite('\Tests\config\RemoteVarsTest');
        $suite->addTestSuite('\Tests\config\RestInfosTest');
        $suite->addTestSuite('\Tests\config\RouteTest');
        $suite->addTestSuite('\Tests\config\RouteMapTest');
        $suite->addTestSuite('\Tests\config\ServerTest');
        $suite->addTestSuite('\Tests\config\TemplateConfigTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'ConfigTests::main') {
    ConfigTests::main();
}