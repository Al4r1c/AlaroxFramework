<?php
namespace Tests\lib;

use AlaroxFramework\utils\restclient\CurlClient;

class CurlClientTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var CurlClient
     */
    private $_curlClient;

    public function setUp()
    {
        $this->_curlClient = new CurlClient();
    }

    public function testInstance()
    {
        $this->assertInstanceOf('\\AlaroxFramework\\utils\\restclient\\CurlClient', $this->_curlClient);
    }

    public function testSetCurl()
    {
        $this->_curlClient->setCurl($curl = $this->getMock('\\AlaroxFramework\\utils\restclient\\Curl'));

        $this->assertAttributeSame($curl, '_curl', $this->_curlClient);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCurlErrone()
    {
        $this->_curlClient->setCurl(array());
    }

    public function testSetParser()
    {
        $this->_curlClient->setParser($parser = $this->getMock('\\AlaroxFramework\\utils\\parser\\Parser'));

        $this->assertAttributeSame($parser, '_parser', $this->_curlClient);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetParserErrone()
    {
        $this->_curlClient->setParser('exception');
    }

    public function testSetCompressor()
    {
        $this->_curlClient->setCompressor($compressor = $this->getMock('\AlaroxFramework\utils\compressor\Compressor'));

        $this->assertAttributeSame($compressor, '_compressor', $this->_curlClient);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetCompressorErrone()
    {
        $this->_curlClient->setCompressor('exception');
    }

    public function testSetTime()
    {
        $this->_curlClient->setTime(1234567890);

        $this->assertAttributeEquals(1234567890, '_time', $this->_curlClient);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetTimeInteger()
    {
        $this->_curlClient->setTime('exception');
    }

    public function testSetParallelCurl()
    {
        $this->_curlClient->setParallelCurl($compressor = $this->getMock('\AlaroxFramework\utils\restclient\ParallelCurl'));

        $this->assertAttributeSame($compressor, '_parallelCurl', $this->_curlClient);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetParallelCurlErrone()
    {
        $this->_curlClient->setParallelCurl('exception');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExecuterRestServerTest()
    {
        $this->_curlClient->executer('exception', $this->getMock('\AlaroxFramework\utils\ObjetRequete'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExecuterObjetRequeteTest()
    {
        $this->_curlClient->executer($this->getMock('\AlaroxFramework\cfg\rest\RestServer'), 'exception');
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuterCurlNotSet()
    {
        $this->_curlClient->executer(
            $this->getMock('\AlaroxFramework\cfg\rest\RestServer'),
            $this->getMock('\AlaroxFramework\utils\ObjetRequete')
        );
    }

    public function methodSwitch($method, $format = null, $formatCompress = null, $tabParam = array())
    {
        if (is_null($format)) {
            $format = 'json';
        }

        $this->setUp();

        $parser = $this->getMock('\AlaroxFramework\utils\parser\Parser', array('parse'));
        $compressor = $this->getMock('\AlaroxFramework\utils\compressor\Compressor', array('compress'));
        $parallelCurl = $this->getMock('AlaroxFramework\utils\restclient\ParallelCurl', array('executerCurl'));

        $curl =
            $this->getMock(
                '\AlaroxFramework\utils\restclient\Curl',
                array('prepare', 'ajouterOption', 'ajouterHeaders')
            );
        $restServer =
            $this->getMock(
                '\AlaroxFramework\cfg\rest\RestServer',
                array('isAuthEnabled', 'getUrl', 'getFormatEnvoi', 'getParametresUri', 'getCompressor')
            );
        $objetRequete =
            $this->getMock('\AlaroxFramework\utils\ObjetRequete', array('getUri', 'getMethodeHttp', 'getBody'));


        $restServer->expects($this->once())->method('isAuthEnabled')->will($this->returnValue(false));
        $restServer->expects($this->once())->method('getUrl')->will($this->returnValue('http://server.url.com/'));
        $restServer->expects($this->atLeastOnce())->method('getFormatEnvoi')->will($this->returnValue('json'));

        $objetRequete->expects($this->once())->method('getUri')->will($this->returnValue('/mon/uri'));
        $objetRequete->expects($this->once())->method('getMethodeHttp')->will($this->returnValue($method));
        $objetRequete->expects($this->any())->method('getBody')->will($this->returnValue(array('param' => 'value')));

        if (!empty($tabParam)) {
            $restServer->expects($this->once())->method('getParametresUri')->will($this->returnValue($tabParam));
        }


        $curl->expects($this->atLeastOnce())->method('ajouterOption');


        $curl->expects($this->atLeastOnce())->method('ajouterHeaders');
        $curl->expects($this->once())->method('prepare');

        $parallelCurl->expects($this->once())
        ->method('executerCurl')
        ->with($curl)
        ->will(
                $this->returnValue(
                    array('WORKINGDUDE', array('content_type' => 'application/json; charset=utf-8', 'http_code' => 200))
                )
            );

        $parser->expects($this->any())->method('parse')->with(array('param' => 'value'), $format)->will(
            $this->returnValue('{"id":"1"}')
        );

        if (!is_null($formatCompress)) {
            $restServer->expects($this->once())->method('getCompressor')->will($this->returnValue($formatCompress));

            $compressor->expects($this->any())->method('compress')->with('{"id":"1"}', $formatCompress)->will(
                $this->returnValue('compressedData:{"id":"1"}')
            );
        }


        $this->_curlClient->setCurl($curl);
        $this->_curlClient->setParser($parser);
        $this->_curlClient->setCompressor($compressor);
        $this->_curlClient->setTime(1234567890);
        $this->_curlClient->setParallelCurl($parallelCurl);
        $resultObjetReponse = $this->_curlClient->executer($restServer, $objetRequete);

        $this->assertEquals(200, $resultObjetReponse->getStatusHttp());
        $this->assertEquals('application/json', $resultObjetReponse->getFormatMime());

        $this->assertEquals('WORKINGDUDE', $resultObjetReponse->getDonneesReponse());
    }

    public function testExecuterNoAuthAllMethod()
    {
        $this->methodSwitch('GET', 'txt');
        $this->methodSwitch('PUT');
        $this->methodSwitch('POST');
        $this->methodSwitch('DELETE');
    }

    public function testExecuterNoAuthCompressor()
    {
        $this->methodSwitch('POST', 'json', 'gzip');
    }

    public function testExecuterNoAuthAllMethodWithUriParam()
    {
        $this->methodSwitch('GET', 'txt', null, array('param' => 'value'));
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuterNoAuthInvalidMethod()
    {
        $curl = $this->getMock('\AlaroxFramework\utils\restclient\Curl');
        $restServer = $this->getMock('\AlaroxFramework\cfg\rest\RestServer');
        $objetRequete = $this->getMock('\AlaroxFramework\utils\ObjetRequete', array('getUri', 'getMethodeHttp'));
        $compressor = $this->getMock('\AlaroxFramework\utils\compressor\Compressor');
        $parallelCurl = $this->getMock('AlaroxFramework\utils\restclient\ParallelCurl');

        $objetRequete->expects($this->once())->method('getUri')->will($this->returnValue('/mon/uri'));
        $objetRequete->expects($this->once())->method('getMethodeHttp')->will($this->returnValue('EXCPETIONMETHOD'));


        $this->_curlClient->setCurl($curl);
        $this->_curlClient->setCompressor($compressor);
        $this->_curlClient->setTime(1234567890);
        $this->_curlClient->setParallelCurl($parallelCurl);
        $this->_curlClient->setParser($this->getMock('\AlaroxFramework\utils\parser\Parser'));
        $resultObjetReponse = $this->_curlClient->executer($restServer, $objetRequete);

        $this->assertEquals(200, $resultObjetReponse->getStatusHttp());
        $this->assertEquals('application/json', $resultObjetReponse->getFormatMime());
        $this->assertEquals('{"id":"1"}', $resultObjetReponse->getDonneesReponse());
    }

    public function testAuth()
    {
        $parser = $this->getMock('\AlaroxFramework\utils\parser\Parser', array('parse'));
        $compressor = $this->getMock('\AlaroxFramework\utils\compressor\Compressor');
        $parallelCurl = $this->getMock('AlaroxFramework\utils\restclient\ParallelCurl', array('executerCurl'));
        $curl =
            $this->getMock(
                '\AlaroxFramework\utils\restclient\Curl',
                array('prepare', 'ajouterOption', 'ajouterHeaders', 'ajouterUnHeader')
            );

        $auth =
            $this->getMock(
                '\AlaroxFramework\cfg\rest\Auth',
                array('getPrivateKey', 'getUsername', 'getAuthentifMethode')
            );
        $restServer =
            $this->getMock(
                '\AlaroxFramework\cfg\rest\RestServer',
                array('isAuthEnabled', 'getFormatEnvoi', 'getAuth')
            );
        $objetRequete =
            $this->getMock('\AlaroxFramework\utils\ObjetRequete', array('getUri', 'getMethodeHttp', 'getBody'));
        $parallelCurl->expects($this->once())
        ->method('executerCurl')
        ->with($curl)
        ->will(
                $this->returnValue(
                    array('{"id":"1"}', array('content_type' => 'application/json; charset=utf-8', 'http_code' => 200))
                )
            );


        $restServer->expects($this->once())->method('isAuthEnabled')->will($this->returnValue(true));
        $restServer->expects($this->once())->method('getAuth')->will($this->returnValue($auth));
        $restServer->expects($this->atLeastOnce())->method('getFormatEnvoi')->will($this->returnValue('json'));

        $auth->expects($this->once())->method('getPrivateKey')->will($this->returnValue('KEY'));
        $auth->expects($this->once())->method('getUsername')->will($this->returnValue('username'));
        $auth->expects($this->once())->method('getAuthentifMethode')->will($this->returnValue('Basic'));

        $objetRequete->expects($this->once())->method('getUri')->will($this->returnValue('/mon/uri'));
        $objetRequete->expects($this->once())->method('getMethodeHttp')->will($this->returnValue('GET'));
        $objetRequete->expects($this->once())->method('getBody')->will($this->returnValue(array('param' => 'value')));

        $curl->expects($this->atLeastOnce())->method('ajouterUnHeader')->with(
            'Authorization',
            'Basic username:/1xdD2ZiYV6hrBMOQBWtNDH6mDi+BfsdjAMTMwzmaq0='
        );
        $curl->expects($this->atLeastOnce())->method('ajouterOption');
        $curl->expects($this->atLeastOnce())->method('ajouterHeaders');
        $curl->expects($this->once())->method('prepare');
        $this->_curlClient->setTime(1234567890);
        $this->_curlClient->setParallelCurl($parallelCurl);

        $parser->expects($this->once())->method('parse')->with(array('param' => 'value'), 'txt')->will(
            $this->returnValue('{"id":"1"}')
        );


        $this->_curlClient->setCurl($curl);
        $this->_curlClient->setParser($parser);
        $this->_curlClient->setCompressor($compressor);
        $resultObjetReponse = $this->_curlClient->executer($restServer, $objetRequete);

        $this->assertEquals(200, $resultObjetReponse->getStatusHttp());
        $this->assertEquals('application/json', $resultObjetReponse->getFormatMime());
        $this->assertEquals('{"id":"1"}', $resultObjetReponse->getDonneesReponse());
    }
}
