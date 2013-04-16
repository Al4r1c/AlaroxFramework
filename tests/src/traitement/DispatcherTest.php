<?php
namespace Tests\traitement;

use AlaroxFramework\cfg\route\RouteMap;
use AlaroxFramework\traitement\Dispatcher;
use AlaroxFramework\traitement\restclient\RestClient;
use Tests\fakecontrollers\TestCtrl;

class DispatcherTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Dispatcher
     */
    private $_dispatcher;

    public function setUp()
    {
        $this->_dispatcher = new Dispatcher();
    }

    /**
     * @param string $uri
     * @param RouteMap $routeMap
     * @param array $tabVariablesAttendus
     * @param bool $i18n
     */
    private function setFakeInfos($uri, $routeMap, $tabVariablesAttendus = array(), $i18n = false)
    {
        $restClient = $this->getMock('\AlaroxFramework\traitement\restclient\RestClient');
        $restInfos = $this->getMock('\AlaroxFramework\cfg\configs\RestInfos');
        $ctrlFactory = $this->getMock('\AlaroxFramework\cfg\configs\ControllerFactory', array('__call'));

        $ctrlFactory->expects($this->once())
            ->method('__call')
            ->with(
                $this->equalTo('testctrl'),
                $this->callback(
                    function ($o) use ($tabVariablesAttendus) {
                        foreach ($tabVariablesAttendus as $uneVariableAttendu) {
                            if (!array_key_exists($uneVariableAttendu, $o[1])) {
                                return false;
                            }
                        }

                        return count($o) == 2 && ($o[0] instanceof RestClient) && is_array($o[1]);
                    }
                )
            )
            ->will($this->returnValue(new TestCtrl()));

        $this->_dispatcher->setUriDemandee($uri);
        $this->_dispatcher->setRestInfos($restInfos);
        $this->_dispatcher->setRouteMap($routeMap);
        $this->_dispatcher->setControllerFactory($ctrlFactory);
        $this->_dispatcher->setI18nActif($i18n);

        $this->_dispatcher->setRestClient($restClient);
    }

    /**
     * @param string $uri
     * @param RouteMap $routeMap
     */
    private function setFakeInfosForException($uri, $routeMap)
    {
        $restClient = $this->getMock('\AlaroxFramework\traitement\restclient\RestClient');
        $restInfos = $this->getMock('\AlaroxFramework\cfg\configs\RestInfos');
        $ctrlFactory = $this->getMock('\AlaroxFramework\cfg\configs\ControllerFactory');

        $this->_dispatcher->setUriDemandee($uri);
        $this->_dispatcher->setRestInfos($restInfos);
        $this->_dispatcher->setRouteMap($routeMap);
        $this->_dispatcher->setControllerFactory($ctrlFactory);
        $this->_dispatcher->setI18nActif(false);

        $this->_dispatcher->setRestClient($restClient);
    }

    public function testSetUri()
    {
        $this->_dispatcher->setUriDemandee('/mon/uri/');

        $this->assertAttributeEquals('/mon/uri/', '_uriDemandee', $this->_dispatcher);
    }

    /**
     * @expectedException \Exception
     */
    public function testSetUriFausse()
    {
        $this->_dispatcher->setUriDemandee('');
    }

    public function testSetRouteMap()
    {
        $this->_dispatcher->setRouteMap($routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap'));

        $this->assertAttributeEquals($routeMap, '_routeMap', $this->_dispatcher);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetRouteMapErrone()
    {
        $this->_dispatcher->setRouteMap('exception');
    }

    public function testSetRestInfos()
    {
        $this->_dispatcher->setRestInfos($restInfos = $this->getMock('\AlaroxFramework\cfg\configs\RestInfos'));

        $this->assertSame($restInfos, $this->_dispatcher->getRestInfos());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetRestInfosErrone()
    {
        $this->_dispatcher->setRestInfos('exception');
    }

    public function testSetControllerFactory()
    {
        $this->_dispatcher->setControllerFactory(
            $ctrlFacto = $this->getMock('\AlaroxFramework\cfg\configs\ControllerFactory')
        );

        $this->assertAttributeSame($ctrlFacto, '_controllerFactory', $this->_dispatcher);
    }

    public function testSetRestClient()
    {
        $this->_dispatcher->setRestClient(
            $restInfos = $this->getMock('\AlaroxFramework\traitement\restclient\RestClient')
        );

        $this->assertAttributeSame($restInfos, '_restClient', $this->_dispatcher);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetRestClientErrone()
    {
        $this->_dispatcher->setRestClient('exception');
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetControllerFactoryErrone()
    {
        $this->_dispatcher->setControllerFactory('exception');
    }

    public function testSetI18nActif()
    {
        $this->_dispatcher->setI18nActif(true);

        $this->assertAttributeEquals(true, '_i18nActif', $this->_dispatcher);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetI18nActifBool()
    {
        $this->_dispatcher->setI18nActif('exception');
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuterNecessiteToutSet()
    {
        $this->_dispatcher->executerActionRequise();
    }

    public function testExecuterUriVideGoDefaultCtrl()
    {
        $route = $this->getMock('\AlaroxFramework\cfg\route\Route', array('getDefaultAction'));
        $routeMap =
            $this->getMock(
                '\AlaroxFramework\cfg\route\RouteMap',
                array('getStaticAliases', 'getControlerParDefaut', 'getUneRouteByController')
            );


        $route->expects($this->once())
            ->method('getDefaultAction')
            ->will($this->returnValue('indexAction'));

        $routeMap->expects($this->once())
            ->method('getControlerParDefaut')
            ->will($this->returnValue('testctrl'));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));

        $routeMap->expects($this->once())
            ->method('getUneRouteByController')
            ->with('testctrl')
            ->will($this->returnValue($route));


        $this->setFakeInfos('/', $routeMap);

        $this->assertEquals('THIS IS INDEX ACTION', $this->_dispatcher->executerActionRequise());
    }

    /**
     * @expectedException \Exception
     */
    public function testControllerFactoryException()
    {
        $restClient = $this->getMock('\AlaroxFramework\traitement\restclient\RestClient');
        $restInfos = $this->getMock('\AlaroxFramework\cfg\configs\RestInfos');
        $ctrlFactory = $this->getMock('\AlaroxFramework\cfg\configs\ControllerFactory', array('__call'));
        $route = $this->getMock('\AlaroxFramework\cfg\route\Route', array('getDefaultAction'));
        $routeMap =
            $this->getMock(
                '\AlaroxFramework\cfg\route\RouteMap',
                array('getStaticAliases', 'getControlerParDefaut', 'getUneRouteByController')
            );


        $route->expects($this->once())
            ->method('getDefaultAction')
            ->will($this->returnValue('indexAction'));

        $routeMap->expects($this->once())
            ->method('getControlerParDefaut')
            ->will($this->returnValue('testctrl'));

        $routeMap->expects($this->once())
            ->method('getUneRouteByController')
            ->with('testctrl')
            ->will($this->returnValue($route));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));

        $ctrlFactory->expects($this->once())
            ->method('__call')
            ->will($this->throwException(new \Exception()));

        $this->_dispatcher->setUriDemandee('/');
        $this->_dispatcher->setRestInfos($restInfos);
        $this->_dispatcher->setRouteMap($routeMap);
        $this->_dispatcher->setControllerFactory($ctrlFactory);
        $this->_dispatcher->setI18nActif(false);
        $this->_dispatcher->setRestClient($restClient);

        $this->_dispatcher->executerActionRequise();
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuterUriVideRouteNonTrouvee()
    {
        $routeMap =
            $this->getMock(
                '\AlaroxFramework\cfg\route\RouteMap',
                array('getStaticAliases', 'getControlerParDefaut', 'getUneRouteByController')
            );

        $routeMap->expects($this->once())
            ->method('getControlerParDefaut')
            ->will($this->returnValue('testctrl'));

        $routeMap->expects($this->once())
            ->method('getUneRouteByController')
            ->with('testctrl')
            ->will($this->returnValue(null));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));


        $this->setFakeInfosForException('/', $routeMap);

        $this->assertEquals('THIS IS INDEX ACTION', $this->_dispatcher->executerActionRequise());
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuterUriVideActionDefautNonSet()
    {
        $route = $this->getMock('\AlaroxFramework\cfg\route\Route', array('getDefaultAction'));
        $routeMap =
            $this->getMock(
                '\AlaroxFramework\cfg\route\RouteMap',
                array('getStaticAliases', 'getControlerParDefaut', 'getUneRouteByController')
            );

        $route->expects($this->once())
            ->method('getDefaultAction')
            ->will($this->returnValue(null));

        $routeMap->expects($this->once())
            ->method('getControlerParDefaut')
            ->will($this->returnValue('testctrl'));

        $routeMap->expects($this->once())
            ->method('getUneRouteByController')
            ->with('testctrl')
            ->will($this->returnValue($route));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));


        $this->setFakeInfosForException('/', $routeMap);

        $this->assertEquals('THIS IS INDEX ACTION', $this->_dispatcher->executerActionRequise());
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuterActionMaisActionNexistePas()
    {
        $route = $this->getMock('\AlaroxFramework\cfg\route\Route', array('getDefaultAction'));
        $routeMap =
            $this->getMock(
                '\AlaroxFramework\cfg\route\RouteMap',
                array('getStaticAliases', 'getControlerParDefaut', 'getUneRouteByController')
            );


        $route->expects($this->once())
            ->method('getDefaultAction')
            ->will($this->returnValue('unknownAction'));

        $routeMap->expects($this->once())
            ->method('getControlerParDefaut')
            ->will($this->returnValue('testctrl'));

        $routeMap->expects($this->once())
            ->method('getUneRouteByController')
            ->with('testctrl')
            ->will($this->returnValue($route));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));


        $this->setFakeInfos('/', $routeMap);

        $this->_dispatcher->executerActionRequise();
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuterActionMaisActionMethodePrivee()
    {
        $route = $this->getMock('\AlaroxFramework\cfg\route\Route', array('getDefaultAction'));
        $routeMap =
            $this->getMock(
                '\AlaroxFramework\cfg\route\RouteMap',
                array('getStaticAliases', 'getControlerParDefaut', 'getUneRouteByController')
            );


        $route->expects($this->once())
            ->method('getDefaultAction')
            ->will($this->returnValue('privatemethod'));

        $routeMap->expects($this->once())
            ->method('getControlerParDefaut')
            ->will($this->returnValue('testctrl'));

        $routeMap->expects($this->once())
            ->method('getUneRouteByController')
            ->with('testctrl')
            ->will($this->returnValue($route));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));


        $this->setFakeInfos('/', $routeMap);

        $this->_dispatcher->executerActionRequise();
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuterUriNonVideMaisRouteNonTrouvee()
    {
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases', 'getRoutes'));

        $routeMap->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue(array()));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));

        $this->setFakeInfosForException('/uridemandee', $routeMap);

        $this->_dispatcher->executerActionRequise();
    }

    public function testExecuterUriNonVideUriSansBaseVide()
    {
        $route =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route', array('getUri', 'getController', 'getDefaultAction')
            );
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases', 'getRoutes'));


        $route->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue('/uridemandee'));

        $route->expects($this->once())
            ->method('getController')
            ->will($this->returnValue('testctrl'));

        $route->expects($this->once())
            ->method('getDefaultAction')
            ->will($this->returnValue('indexAction'));

        $routeMap->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));


        $this->setFakeInfos('/uridemandee', $routeMap);

        $this->assertEquals('THIS IS INDEX ACTION', $this->_dispatcher->executerActionRequise());
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuterUriNonVideUriSansBaseVideMaisActionDefautNonSet()
    {
        $route =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route', array('getUri', 'getController', 'getDefaultAction')
            );
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases', 'getRoutes'));

        $route->expects($this->once())
            ->method('getUri')
            ->will($this->returnValue('/uridemandee'));

        $route->expects($this->once())
            ->method('getController')
            ->will($this->returnValue('testctrl'));

        $route->expects($this->once())
            ->method('getDefaultAction')
            ->will($this->returnValue(null));

        $routeMap->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));


        $this->setFakeInfosForException('/uridemandee', $routeMap);

        $this->_dispatcher->executerActionRequise();
    }

    public function testExecuterUriNonVideUriSansBaseNonVideMappingStatiqueTrouve()
    {
        $route =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route', array('getUri', 'getController', 'getMapping')
            );
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases', 'getRoutes'));


        $route->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue('/monuri'));

        $route->expects($this->once())
            ->method('getController')
            ->will($this->returnValue('testctrl'));

        $route->expects($this->once())
            ->method('getMapping')
            ->will(
                $this->returnValue(
                    array(
                        '/unevariable' => 'myActionFirst'
                    )
                )
            );


        $routeMap->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));


        $this->setFakeInfos('/monuri/unevariable', $routeMap);

        $this->assertEquals('myFirst ACTION', $this->_dispatcher->executerActionRequise());
    }

    public function testExecuterUriNonVideUriSansBaseNonVideMappingGeneriqueTrouve()
    {
        $route =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route', array('getUri', 'getController', 'getMapping')
            );
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases', 'getRoutes'));


        $route->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue('/monuri'));

        $route->expects($this->once())
            ->method('getController')
            ->will($this->returnValue('testctrl'));

        $route->expects($this->once())
            ->method('getMapping')
            ->will(
                $this->returnValue(
                    array(
                        '/*' => 'myActionSecond'
                    )
                )
            );

        $routeMap->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));


        $this->setFakeInfos('/monuri/unevariable', $routeMap);

        $this->assertEquals('mySecond ACTION', $this->_dispatcher->executerActionRequise());
    }

    public function testExecuterUriNonVideUriSansBaseNonVideMappingStatiqueGeneriqueTrouve()
    {
        $route =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route', array('getUri', 'getController', 'getMapping')
            );
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases', 'getRoutes'));


        $route->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue('/monuri'));

        $route->expects($this->once())
            ->method('getController')
            ->will($this->returnValue('testctrl'));

        $route->expects($this->once())
            ->method('getMapping')
            ->will(
                $this->returnValue(
                    array(
                        '/uri/*' => 'myActionSecond'
                    )
                )
            );

        $routeMap->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));


        $this->setFakeInfos('/monuri/uri/everything', $routeMap);

        $this->assertEquals('mySecond ACTION', $this->_dispatcher->executerActionRequise());
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuterUriNonVideUriSansBaseNonVideMappingStatiqueGeneriqueNonTrouve()
    {
        $route =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route',
                array('getUri', 'getController', 'getMapping')
            );
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases', 'getRoutes'));


        $route->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue('/monuri'));

        $route->expects($this->once())
            ->method('getController')
            ->will($this->returnValue('testctrl'));

        $route->expects($this->once())
            ->method('getMapping')
            ->will(
                $this->returnValue(
                    array(
                        '/uri/*' => 'myActionSecond'
                    )
                )
            );

        $routeMap->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));

        $this->setFakeInfosForException('/monuri/notfound/everything', $routeMap);

        $this->_dispatcher->executerActionRequise();
    }

    /**
     * @expectedException \Exception
     */
    public function testExecuterUriNonVideUriSansBaseNonVideMappingTooMuchArg()
    {
        $route =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route',
                array('getUri', 'getController', 'getMapping')
            );
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases', 'getRoutes'));


        $route->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue('/monuri'));

        $route->expects($this->once())
            ->method('getController')
            ->will($this->returnValue('testctrl'));

        $route->expects($this->once())
            ->method('getMapping')
            ->will(
                $this->returnValue(
                    array(
                        '/foundway/*' => 'myActionSecond'
                    )
                )
            );

        $routeMap->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));


        $this->setFakeInfosForException('/monuri/foundway/everything/toomuchthere', $routeMap);

        $this->_dispatcher->executerActionRequise();
    }

    public function testExecuterUriNonVideUriSansBaseNonVideMappingUtiliseVariable()
    {
        $route =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route', array('getUri', 'getController', 'getPattern', 'getMapping')
            );
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases', 'getRoutes'));


        $route->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue('/monuri'));

        $route->expects($this->once())
            ->method('getController')
            ->will($this->returnValue('testctrl'));

        $route->expects($this->once())
            ->method('getMapping')
            ->will(
                $this->returnValue(
                    array(
                        '/*' => '$first?'
                    )
                )
            );

        $route->expects($this->once())
            ->method('getPattern')
            ->will($this->returnValue('/$first?/$second?/third'));


        $routeMap->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));


        $this->setFakeInfos('/monuri/myActionFirst', $routeMap, array('first'));

        $this->assertEquals('myFirst ACTION', $this->_dispatcher->executerActionRequise());
    }

    public function testExecuterUriNonVideUriSansBaseNonVideMappingUtiliseVariablePatternParticulier()
    {
        $route =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route', array('getUri', 'getController', 'getPattern', 'getMapping')
            );
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases', 'getRoutes'));


        $route->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue('/monuri'));

        $route->expects($this->once())
            ->method('getController')
            ->will($this->returnValue('testctrl'));

        $route->expects($this->once())
            ->method('getMapping')
            ->will(
                $this->returnValue(
                    array(
                        '/*-*_*' => '$first?'
                    )
                )
            );

        $route->expects($this->once())
            ->method('getPattern')
            ->will($this->returnValue('/$index?-$first?_$second?'));


        $routeMap->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));


        $this->setFakeInfos(
            '/monuri/indexAction-myActionFirst_myActionSecond', $routeMap, array('index', 'first', 'second')
        );

        $this->assertEquals('myFirst ACTION', $this->_dispatcher->executerActionRequise());
    }

    public function testExecuterUriNonVideUriSansBaseNonVideMappingPlusLongQuePattern()
    {
        $route =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route', array('getUri', 'getController', 'getPattern', 'getMapping')
            );
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases', 'getRoutes'));


        $route->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue('/monuri'));

        $route->expects($this->once())
            ->method('getController')
            ->will($this->returnValue('testctrl'));

        $route->expects($this->once())
            ->method('getMapping')
            ->will(
                $this->returnValue(
                    array(
                        '/*/*/*' => 'myActionFirst'
                    )
                )
            );

        $route->expects($this->once())
            ->method('getPattern')
            ->will($this->returnValue('/$first?'));


        $routeMap->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));


        $this->setFakeInfos('/monuri/seg1/seg2/seg3', $routeMap, array('first'));

        $this->assertEquals('myFirst ACTION', $this->_dispatcher->executerActionRequise());
    }


    /**
     * @expectedException \Exception
     */
    public function testExecuterUriNonVideUriSansBaseNonVideMaisACtionNonTrouvee()
    {
        $route =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route',
                array('getUri', 'getController', 'getMapping')
            );
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases', 'getRoutes'));


        $route->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue('/monuri'));

        $route->expects($this->once())
            ->method('getController')
            ->will($this->returnValue('testctrl'));

        $route->expects($this->once())
            ->method('getMapping')
            ->will(
                $this->returnValue(
                    array(
                        '/urifirst' => 'myActionFirst'
                    )
                )
            );

        $routeMap->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));


        $this->setFakeInfosForException('/monuri/nothing', $routeMap);

        $this->_dispatcher->executerActionRequise();
    }

    public function testStatic()
    {
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases'));


        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array('/pageStatique')));


        $this->setFakeInfosForException('/pageStatique/nothing/file', $routeMap);

        $this->assertInstanceOf('AlaroxFramework\\utils\\View', $view = $this->_dispatcher->executerActionRequise());
        $this->assertEquals('nothing/file.twig', $view->getViewName());
    }

    /**
     * @expectedException \Exception
     */
    public function testStaticUriVide()
    {
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases'));


        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array('/pageStatique')));


        $this->setFakeInfosForException('/pageStatique', $routeMap);

        $this->_dispatcher->executerActionRequise();
    }

    public function testExecuterI18nActifUriNonVideUriSansBaseNonVideMappingStatiqueTrouve()
    {
        $route =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route', array('getUri', 'getController', 'getMapping')
            );
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases', 'getRoutes'));


        $route->expects($this->any())
            ->method('getUri')
            ->will($this->returnValue('/monuri'));

        $route->expects($this->once())
            ->method('getController')
            ->will($this->returnValue('testctrl'));

        $route->expects($this->once())
            ->method('getMapping')
            ->will(
                $this->returnValue(
                    array(
                        '/unevariable' => 'myActionFirst'
                    )
                )
            );


        $routeMap->expects($this->once())
            ->method('getRoutes')
            ->will($this->returnValue(array($route)));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));


        $this->setFakeInfos('/fr/monuri/unevariable', $routeMap, array(), true);

        $this->assertEquals('myFirst ACTION', $this->_dispatcher->executerActionRequise());
    }

    public function testExecuterI18nActifUriVideGoDefaultCtrl()
    {
        $route = $this->getMock('\AlaroxFramework\cfg\route\Route', array('getDefaultAction'));
        $routeMap =
            $this->getMock(
                '\AlaroxFramework\cfg\route\RouteMap',
                array('getStaticAliases', 'getControlerParDefaut', 'getUneRouteByController')
            );


        $route->expects($this->once())
            ->method('getDefaultAction')
            ->will($this->returnValue('indexAction'));

        $routeMap->expects($this->once())
            ->method('getControlerParDefaut')
            ->will($this->returnValue('testctrl'));

        $routeMap->expects($this->once())
            ->method('getStaticAliases')
            ->will($this->returnValue(array()));

        $routeMap->expects($this->once())
            ->method('getUneRouteByController')
            ->with('testctrl')
            ->will($this->returnValue($route));


        $this->setFakeInfos('/fr/', $routeMap, array(), true);

        $this->assertEquals('THIS IS INDEX ACTION', $this->_dispatcher->executerActionRequise());
    }
}