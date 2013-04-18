<?php
namespace AlaroxFramework\reponse;

use AlaroxFramework\utils\HtmlReponse;
use AlaroxFramework\utils\View;

class ReponseManager
{
    /**
     * @var TemplateManager
     */
    private $_templateManager;

    /**
     * @param TemplateManager $templateManager
     * @throws \InvalidArgumentException
     */
    public function setTemplateManager($templateManager)
    {
        if (!$templateManager instanceof TemplateManager) {
            throw new \InvalidArgumentException('Expected parameter 1 templateManager to be instance of TemplateManager.');
        }

        $this->_templateManager = $templateManager;
    }

    /**
     * @param string|View $dataResponse
     * @throws \Exception
     * @return HtmlReponse
     */
    public function getHtmlResponse($dataResponse)
    {
        if ($dataResponse instanceof View) {
            if (is_null($this->_templateManager)) {
                throw new \Exception('TemplateManager is not set.');
            }

            return new HtmlReponse(200, $this->_templateManager->render($dataResponse));
        } else {
            return new HtmlReponse(200, $dataResponse);
        }
    }
}