<?php
namespace Tests\traitement;

use AlaroxFramework\cfg\route\RouteMap;
use AlaroxFramework\traitement\Dispatcher;
use AlaroxFramework\traitement\NotFoundException;

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
     * @param $routeMap
     * @param string $result
     * @param string $methodName
     * @param array $tabVariablesAttendus
     * @param string $ctrlName
     * @param bool $i18n
     */
    private function setFakeInfos($uri, $routeMap, $result = 'THIS IS INDEX ACTION', $methodName = 'indexAction',
        $tabVariablesAttendus = array(), $ctrlName = 'testctrl',
        $i18n = false)
    {
        $ctrlExecutor = $this->getMock('\AlaroxFramework\traitement\ControllerExecutor', array('executerControleur'));
        $viewFactory = $this->getMock('\AlaroxFramework\utils\view\ViewFactory');

        if ($result instanceof \Exception) {
            $resultatAttendu = $this->throwException($result);
        } else {
            $resultatAttendu = $this->returnValue($result);
        }

        $ctrlExecutor->expects($this->once())
        ->method('executerControleur')
        ->with(
                $this->equalTo($ctrlName),
                $this->equalTo($methodName),
                $this->callback(
                    function ($o) use ($tabVariablesAttendus) {
                        foreach ($tabVariablesAttendus as $clefVariableAttendues => $uneVariableAttendue) {
                            if (!array_key_exists($clefVariableAttendues, $o)) {
                                return false;
                            } elseif ($o[$clefVariableAttendues] != $uneVariableAttendue) {
                                return false;
                            }
                        }

                        return count($o) == count($tabVariablesAttendus) && is_array($o);
                    }
                )
            )
        ->will($resultatAttendu);

        $this->_dispatcher->setUriDemandee($uri);
        $this->_dispatcher->setRouteMap($routeMap);
        $this->_dispatcher->setControllerExecutor($ctrlExecutor);
        $this->_dispatcher->setI18nActif($i18n);
        $this->_dispatcher->setViewFactory($viewFactory);
    }

    /**
     * @param string $uri
     * @param RouteMap $routeMap
     */
    private function setFakeInfosForException($uri, $routeMap)
    {
        $ctrlExecutor = $this->getMock('\AlaroxFramework\traitement\ControllerExecutor');
        $viewFactory = $this->getMock('\AlaroxFramework\utils\view\ViewFactory');

        $this->_dispatcher->setUriDemandee($uri);
        $this->_dispatcher->setRouteMap($routeMap);
        $this->_dispatcher->setControllerExecutor($ctrlExecutor);
        $this->_dispatcher->setI18nActif(false);
        $this->_dispatcher->setViewFactory($viewFactory);
    }

    /**
     * @param string $nomMethode
     * @return \PHPUnit_Framework_MockObject_MockObject
     */
    private function getDefaultRouteMap($nomMethode = 'indexAction')
    {
        $route = $this->getMock('\AlaroxFramework\cfg\route\Route', array('getController', 'getDefaultAction'));
        $routeMap =
            $this->getMock(
                '\AlaroxFramework\cfg\route\RouteMap',
                array('getStaticAliases', 'getRouteParDefaut')
            );

        $route->expects($this->once())
        ->method('getController')
        ->will($this->returnValue('testctrl'));

        $route->expects($this->once())
        ->method('getDefaultAction')
        ->will($this->returnValue($nomMethode));

        $routeMap->expects($this->once())
        ->method('getRouteParDefaut')
        ->will($this->returnValue($route));

        $routeMap->expects($this->once())
        ->method('getStaticAliases')
        ->will($this->returnValue(array()));

        return $routeMap;
    }

    public function testSetUri()
    {
        $this->_dispatcher->setUriDemandee('/mon/uri/');

        $this->assertAttributeEquals('/mon/uri/', '_uriDemandee', $this->_dispatcher);
    }

    /**
     * @expectedException \InvalidArgumentException
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

    public function testSetControllerExecutor()
    {
        $this->_dispatcher->setControllerExecutor(
            $ctrlExecutor = $this->getMock('\AlaroxFramework\traitement\ControllerExecutor')
        );

        $this->assertAttributeSame($ctrlExecutor, '_controllerExecutor', $this->_dispatcher);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetControllerExecutorErrone()
    {
        $this->_dispatcher->setControllerExecutor('exception');
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

    public function testSetViewFactory()
    {
        $viewFactory = $this->getMock('\AlaroxFramework\utils\view\ViewFactory');

        $this->_dispatcher->setViewFactory($viewFactory);

        $this->assertAttributeSame($viewFactory, '_viewFactory', $this->_dispatcher);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetViewFactoryInstance()
    {
        $this->_dispatcher->setViewFactory('bugbug');
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
        $this->setFakeInfos('/', $this->getDefaultRouteMap());

        $this->assertEquals('THIS IS INDEX ACTION', $this->_dispatcher->executerActionRequise());
    }

    /**
     * @expectedException \Exception
     */
    public function testControllerExecutorException()
    {
        $viewFactory = $this->getMock('\AlaroxFramework\utils\view\ViewFactory');

        $ctrlExecutor = $this->getMock('\AlaroxFramework\traitement\ControllerExecutor', array('executerControleur'));
        $ctrlExecutor->expects($this->once())
        ->method('executerControleur')
        ->will($this->throwException(new NotFoundException()));

        $this->_dispatcher->setUriDemandee('/');
        $this->_dispatcher->setRouteMap($this->getDefaultRouteMap());
        $this->_dispatcher->setControllerExecutor($ctrlExecutor);
        $this->_dispatcher->setI18nActif(false);
        $this->_dispatcher->setViewFactory($viewFactory);

        $this->_dispatcher->executerActionRequise();
    }

    /**
     * @expectedException \AlaroxFramework\traitement\NotFoundException
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
                '\AlaroxFramework\cfg\route\Route',
                array('getUri', 'getController', 'getDefaultAction')
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

    public function testRechercheUriKeyCase()
    {
        $route =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route',
                array('getUri', 'getController', 'getDefaultAction')
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


        $this->setFakeInfos('/URIDEMANDEE', $routeMap);

        $this->assertEquals('THIS IS INDEX ACTION', $this->_dispatcher->executerActionRequise());
    }

    /**
     * @expectedException \AlaroxFramework\traitement\NotFoundException
     */
    public function testExecuterUriNonVideUriSansBaseVideMaisActionDefautNonSet()
    {
        $route =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route',
                array('getUri', 'getDefaultAction')
            );
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases', 'getRoutes'));

        $route->expects($this->once())
        ->method('getUri')
        ->will($this->returnValue('/uridemandee'));

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

    public function testExecuterUriNonVideUriSansBaseVideNePasConfondreSiCommencePareil()
    {
        $route =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route',
                array('getUri')
            );
        $route2 =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route',
                array('getUri', 'getController', 'getDefaultAction')
            );
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases', 'getRoutes'));

        $route->expects($this->once())
        ->method('getUri')
        ->will($this->returnValue('/uri'));

        $route2->expects($this->once())
        ->method('getUri')
        ->will($this->returnValue('/urilongue'));

        $route2->expects($this->once())
        ->method('getController')
        ->will($this->returnValue('testctrl'));

        $route2->expects($this->once())
        ->method('getDefaultAction')
        ->will($this->returnValue('indexAction'));

        $routeMap->expects($this->once())
        ->method('getRoutes')
        ->will($this->returnValue(array($route, $route2)));

        $routeMap->expects($this->once())
        ->method('getStaticAliases')
        ->will($this->returnValue(array()));


        $this->setFakeInfos('/urilongue', $routeMap);

        $this->assertEquals('THIS IS INDEX ACTION', $this->_dispatcher->executerActionRequise());
    }

    public function testExecuterUriNonVideUriSansBaseNonVideMappingStatiqueTrouve()
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


        $this->setFakeInfos('/monuri/unevariable', $routeMap, 'myFirst ACTION', 'myActionFirst');

        $this->assertEquals('myFirst ACTION', $this->_dispatcher->executerActionRequise());
    }

    public function testExecuterMethodKeyCase()
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
                        '/UNEvaRIAbLLe' => 'myActionFirst'
                    )
                )
            );


        $routeMap->expects($this->once())
        ->method('getRoutes')
        ->will($this->returnValue(array($route)));

        $routeMap->expects($this->once())
        ->method('getStaticAliases')
        ->will($this->returnValue(array()));


        $this->setFakeInfos('/monuri/unevariablle', $routeMap, 'myFirst ACTION', 'myActionFirst');

        $this->assertEquals('myFirst ACTION', $this->_dispatcher->executerActionRequise());
    }

    public function testExecuterUriNonVideUriSansBaseNonVideMappingGeneriqueTrouve()
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


        $this->setFakeInfos('/monuri/unevariable', $routeMap, 'mySecond ACTION', 'myActionSecond');

        $this->assertEquals('mySecond ACTION', $this->_dispatcher->executerActionRequise());
    }

    public function testExecuterUriNonVideUriSansBaseNonVideMappingStatiqueGeneriqueTrouve()
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


        $this->setFakeInfos('/monuri/uri/everything', $routeMap, 'mySecond ACTION', 'myActionSecond');

        $this->assertEquals('mySecond ACTION', $this->_dispatcher->executerActionRequise());
    }

    /**
     * @expectedException \AlaroxFramework\traitement\NotFoundException
     */
    public function testExecuterUriNonVideUriSansBaseNonVideMappingStatiqueGeneriqueNonTrouve()
    {
        $route =
            $this->getMock(
                '\AlaroxFramework\cfg\route\Route',
                array('getUri', 'getMapping')
            );
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases', 'getRoutes'));


        $route->expects($this->any())
        ->method('getUri')
        ->will($this->returnValue('/monuri'));

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

    public function testExecuterUriNonVideUriSansBaseNonVideMappingPlusLongQuePattern()
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
                        '/*/*/*' => 'myActionFirst'
                    )
                )
            );


        $routeMap->expects($this->once())
        ->method('getRoutes')
        ->will($this->returnValue(array($route)));

        $routeMap->expects($this->once())
        ->method('getStaticAliases')
        ->will($this->returnValue(array()));


        $this->setFakeInfos('/monuri/seg1/seg2/seg3', $routeMap, 'myFirst ACTION', 'myActionFirst');

        $this->assertEquals('myFirst ACTION', $this->_dispatcher->executerActionRequise());
    }

    public function testExecuterUriNonVideUriSansBaseNonVideRecupererVariableDepuisPatternDuMapping()
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
                        '/$first?/$second?/$third?' => 'myActionFirst'
                    )
                )
            );


        $routeMap->expects($this->once())
        ->method('getRoutes')
        ->will($this->returnValue(array($route)));

        $routeMap->expects($this->once())
        ->method('getStaticAliases')
        ->will($this->returnValue(array()));


        $this->setFakeInfos(
            '/monuri/seg1/seg2/seg3',
            $routeMap,
            'myFirst ACTION',
            'myActionFirst',
            array('first' => 'seg1', 'second' => 'seg2', 'third' => 'seg3')
        );

        $this->assertEquals('myFirst ACTION', $this->_dispatcher->executerActionRequise());
    }

    public function testMappingAvecCaracteresEtMajuscules()
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
                        '/$someVariable?' => 'myActionFirst'
                    )
                )
            );


        $routeMap->expects($this->once())
        ->method('getRoutes')
        ->will($this->returnValue(array($route)));

        $routeMap->expects($this->once())
        ->method('getStaticAliases')
        ->will($this->returnValue(array()));


        $this->setFakeInfos(
            '/monuri/hello-world_uep',
            $routeMap,
            'myFirst ACTION',
            'myActionFirst',
            array('someVariable' => 'hello-world_uep')
        );

        $this->assertEquals('myFirst ACTION', $this->_dispatcher->executerActionRequise());
    }

    public function testStatic()
    {
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases'));
        $viewFactory = $this->getMock('\AlaroxFramework\utils\view\ViewFactory', array('getView'));
        $templateView = $this->getMock('\AlaroxFramework\utils\view\TemplateView', array('renderView', 'getViewData'));

        $viewFactory->expects($this->once())->method('getView')->with('template')->will(
            $this->returnValue($templateView)
        );
        $templateView->expects($this->once())->method('renderView')->with('monDossierStatic/nothing/file.twig');
        $templateView->expects($this->once())->method('getViewData')->will(
            $this->returnValue('monDossierStatic/nothing/file.twig')
        );


        $routeMap->expects($this->once())
        ->method('getStaticAliases')
        ->will($this->returnValue(array('/pageStatique' => 'monDossierStatic')));


        $this->setFakeInfosForException('/pageStatique/nothing/file', $routeMap);
        $this->_dispatcher->setViewFactory($viewFactory);

        $this->assertInstanceOf(
            'AlaroxFramework\\utils\\view\\TemplateView',
            $view = $this->_dispatcher->executerActionRequise()
        );
        $this->assertEquals('monDossierStatic/nothing/file.twig', $view->getViewData());
    }

    /**
     * @expectedException \AlaroxFramework\traitement\NotFoundException
     */
    public function testStaticUriVide()
    {
        $routeMap = $this->getMock('\AlaroxFramework\cfg\route\RouteMap', array('getStaticAliases'));


        $routeMap->expects($this->once())
        ->method('getStaticAliases')
        ->will($this->returnValue(array('/pageStatique' => 'folder')));


        $this->setFakeInfosForException('/pageStatique', $routeMap);

        $this->_dispatcher->executerActionRequise();
    }

    public function testExecuterI18nActifUriNonVideUriSansBaseNonVideMappingStatiqueTrouve()
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


        $this->setFakeInfos(
            '/fr/monuri/unevariable',
            $routeMap,
            'myFirst ACTION',
            'myActionFirst',
            array(),
            'testctrl',
            true
        );

        $this->assertEquals('myFirst ACTION', $this->_dispatcher->executerActionRequise());
    }

    public function testExecuterI18nActifUriVideGoDefaultCtrl()
    {
        $route = $this->getMock('\AlaroxFramework\cfg\route\Route', array('getController', 'getDefaultAction'));
        $routeMap =
            $this->getMock(
                '\AlaroxFramework\cfg\route\RouteMap',
                array('getStaticAliases', 'getRouteParDefaut')
            );

        $route->expects($this->once())
        ->method('getController')
        ->will($this->returnValue('testctrl'));

        $route->expects($this->once())
        ->method('getDefaultAction')
        ->will($this->returnValue('indexAction'));

        $routeMap->expects($this->once())
        ->method('getRouteParDefaut')
        ->will($this->returnValue($route));

        $routeMap->expects($this->once())
        ->method('getStaticAliases')
        ->will($this->returnValue(array()));


        $this->setFakeInfos('/fr/', $routeMap, 'THIS IS INDEX ACTION', 'indexAction', array(), 'testctrl', true);

        $this->assertEquals('THIS IS INDEX ACTION', $this->_dispatcher->executerActionRequise());
    }
}