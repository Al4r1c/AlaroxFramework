<?php
if (!defined('PHPUnit_MAIN_METHOD')) {
    define('PHPUnit_MAIN_METHOD', 'AllTests::main');
}

class AllTests
{
    public static function main()
    {
        \PHPUnit_TextUI_TestRunner::run(self::suite());
    }

    public static function suite()
    {
        $suite = new \PHPUnit_Framework_TestSuite('AlaroxFramework Tests');

        $suite->addTestSuite('\\Tests\\Framework\\AlaroxFrameworkTest');
        $suite->addTestSuite('\\Tests\\Exceptions\\ErreurHandlerTest');
        $suite->addTest(\Tests\LibTests::suite());
        $suite->addTest(\Tests\ConfigTests::suite());
        $suite->addTest(\Tests\ReponseTests::suite());
        $suite->addTest(\Tests\TraitementTests::suite());

        return $suite;
    }
}

if (PHPUnit_MAIN_METHOD == 'AllTests::main') {
    AllTests::main();
}