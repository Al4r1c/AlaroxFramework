<?php
namespace Tests;

use AlaroxFileManager\FileManager\File;

class FactoryMock extends \PHPUnit_Framework_TestCase
{
    /**
     * @param string $type
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    protected function recupererMockSelonNom($type, $methodes = array())
    {
        $mock = null;

        switch (strtolower($type)) {
            case 'file':
                $mock = $this->getMockFile($methodes);
                break;
            default:
                new \Exception('Mock type not found.');
                break;
        }

        return $mock;
    }

    protected function getMockAbstractClass($mockClass, $tabMethodes = array())
    {
        return $this->getMockForAbstractClass($mockClass, array(), '', true, true, true, $tabMethodes);
    }

    /**
     * @param array $methodes
     * @return \PHPUnit_Framework_MockObject_MockObject|File
     */
    protected function getMockFile($methodes = array())
    {
        return $this->getMock('AlaroxFileManager\FileManager\File', $methodes);
    }
}