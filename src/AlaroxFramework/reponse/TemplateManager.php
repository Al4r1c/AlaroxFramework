<?php
namespace AlaroxFramework\reponse;

use AlaroxFramework\utils\View;

class TemplateManager
{
    /**
     * @var \Twig_Environment
     */
    private $_twigEnv;

    /**
     * @var array
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
     * @param array $generalVar
     * @throws \InvalidArgumentException
     */
    public function setGlobalVar($generalVar)
    {
        if (!is_array($generalVar)) {
            throw new \InvalidArgumentException('Expected parameter 1 generalVar to be array.');
        }

        $this->_globalVar = $generalVar;
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

        return $template->render($view->getVariables() + $this->_globalVar);
    }
}