<?php
namespace AlaroxFramework\utils\view;

class ViewFactory
{
    /**
     * @param $viewType
     * @return AbstractView
     * @throws \Exception
     */
    public function getView($viewType)
    {
        switch ($viewType) {
            case 'plain':
                return new PlainView();
                break;
            case 'template':
                return new TemplateView();
                break;
            default:
                throw new \Exception(sprintf('View type "%s" not found.', $viewType));
                break;
        }
    }
}