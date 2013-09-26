<?php
namespace AlaroxFramework\utils\view;

use AlaroxFramework\utils\view\AbstractView;

class PlainView extends AbstractView
{
    /**
     * @param string $viewName
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function renderView($viewName)
    {
        $this->_viewData = $viewName;

        return $this;
    }
}