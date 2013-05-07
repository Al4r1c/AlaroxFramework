<?php
namespace AlaroxFramework\reponse;

use AlaroxFramework\cfg\globals\GlobalVars;
use AlaroxFramework\utils\View;

class TemplateManager
{
    /**
     * @var \Twig_Environment
     */
    private $_twigEnv;

    /**
     * @var GlobalVars
     */
    private $_globalVar = array();

    /**
     * @param \Twig_Environment $twigEnv
     * @throws \InvalidArgumentException
     */
    public function setTwigEnv($twigEnv)
    {
        if (!$twigEnv instanceof \Twig_Environment) {
            throw new \InvalidArgumentException('Expected parameter 1 twigEnv to be instance of Twig_Environment.');
        }

        $this->_twigEnv = $twigEnv;
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

        if (is_null($this->_twigEnv)) {
            throw new \Exception('Twig is not instantiated.');
        }

        $this->_twigEnv->addExtension($extension);
    }

    /**
     * @param View $view
     * @throws \InvalidArgumentException
     * @throws \Exception
     * @return string
     */
    public function render($view)
    {

        if (!$view instanceof View) {
            throw new \InvalidArgumentException('Expected parameter 1 view to be instance of View.');
        }

        if (is_null($this->_twigEnv)) {
            throw new \Exception('Twig Environment is not set.');
        }

        $template = $this->_twigEnv->loadTemplate($view->getViewName());

        return $template->render(
            $view->getVariables() + $this->_globalVar->getStaticVars() + $this->_globalVar->getRemoteVarsExecutees()
        );
    }
}