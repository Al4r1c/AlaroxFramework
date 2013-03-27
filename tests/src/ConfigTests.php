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

        $suite->addTestSuite('\Tests\Config\ConfigTest');
        $suite->addTestSuite('\Tests\Config\RouteMapTest');
        $suite->addTestSuite('\Tests\Config\ServerTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'ConfigTests::main') {
    ConfigTests::main();
}