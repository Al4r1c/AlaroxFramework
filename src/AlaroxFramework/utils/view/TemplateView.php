<?php
namespace AlaroxFramework\utils\view;

use AlaroxFramework\utils\view\AbstractView;

class TemplateView extends AbstractView
{
    /**
     * @param string $viewName
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function renderView($viewName)
    {
        $pathInfo = pathinfo($viewName);

        if (!isset($pathInfo['extension']) || strcmp($pathInfo['extension'], 'twig') != 0) {
            throw new \InvalidArgumentException('Expected twig template.');
        }

        $this->_viewData = $viewName;

        return $this;
    }
}