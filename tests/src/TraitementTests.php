<?php
namespace Tests;

if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'TraitementTests::main');
}

class TraitementTests
{
    public static function main()
    {
        \PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('TestSuite');

        $suite->addTestSuite('\Tests\traitement\DispatcherTest');
        $suite->addTestSuite('\Tests\traitement\GenericControllerTest');

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'TraitementTests::main') {
    TraitementTests::main();
}