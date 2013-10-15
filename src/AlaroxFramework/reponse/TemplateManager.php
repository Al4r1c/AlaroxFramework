<?php
namespace AlaroxFramework\reponse;

use AlaroxFramework\cfg\globals\GlobalVars;
use AlaroxFramework\utils\twig\TwigEnvFactory;
use AlaroxFramework\utils\view\AbstractView;

class TemplateManager
{
    /**
     * @var TwigEnvFactory
     */
    private $_twigEnvFactory;

    /**
     * @var GlobalVars
     */
    private $_globalVar;

    /**
     * @var \Twig_ExtensionInterface[]
     */
    private $_listeExtension = array();

    /**
     * @var \Twig_SimpleFilter[]
     */
    private $_listeFilters = array();

    /**
     * @var \Twig_SimpleFunction[]
     */
    private $_listeFunctions = array();

    /**
     * @param TwigEnvFactory $twigEnvFactory
     * @throws \InvalidArgumentException
     */
    public function setTwigEnvFactory($twigEnvFactory)
    {
        if (!$twigEnvFactory instanceof TwigEnvFactory) {
            throw new \InvalidArgumentException('Expected parameter 1 twigEnvFactory to be instance of TwigEnvFactory.');
        }

        $this->_twigEnvFactory = $twigEnvFactory;
    }

    /**
     * @param GlobalVars $globalVars
     */
    public function setGlobalVar($globalVars)
    {
        $this->_globalVar = $globalVars;
    }

    /**
     * @param \Twig_ExtensionInterface $extension
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function addExtension($extension)
    {
        if (!$extension instanceof \Twig_ExtensionInterface) {
            throw new \InvalidArgumentException('Expected parameter 1 extension to be instance of Twig_ExtensionInterface.');
        }

        $this->_listeExtension[] = $extension;
    }

    /**
     * @param \Twig_SimpleFilter $filtre
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function addFilter($filtre)
    {
        if (!$filtre instanceof \Twig_SimpleFilter) {
            throw new \InvalidArgumentException('Expected parameter 1 extension to be instance of Twig_SimpleFilter.');
        }

        $this->_listeFilters[] = $filtre;
    }

    /**
     * @param \Twig_SimpleFunction $fonction
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function addFunction($fonction)
    {
        if (!$fonction instanceof \Twig_SimpleFunction) {
            throw new \InvalidArgumentException('Expected parameter 1 extension to be instance of Twig_SimpleFunction.');
        }

        $this->_listeFunctions[] = $fonction;
    }

    /**
     * @param AbstractView $view
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return string
     */
    public function render($view)
    {
        if (!$view instanceof AbstractView) {
            throw new \InvalidArgumentException('Expected parameter 1 view to be instance of AbstractView.');
        }

        $twigEnv = $this->_twigEnvFactory->getTwigEnv(substr(get_class($view), strrpos(get_class($view), '\\') + 1));

        foreach ($this->_listeExtension as $uneExtention) {
            $twigEnv->addExtension($uneExtention);
        }

        foreach ($this->_listeFilters as $unFiltre) {
            $twigEnv->addFilter($unFiltre);
        }

        foreach ($this->_listeFunctions as $uneFonction) {
            $twigEnv->addFunction($uneFonction);
        }

        return $twigEnv->render(
            $view->getViewData(),
            $view->getVariables() + $this->_globalVar->getStaticVars() + $this->_globalVar->getRemoteVarsExecutees()
        );
    }
}