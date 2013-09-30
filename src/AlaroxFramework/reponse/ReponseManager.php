<?php
namespace AlaroxFramework\reponse;

use AlaroxFramework\utils\HtmlReponse;
use AlaroxFramework\utils\view\AbstractView;
use AlaroxFramework\utils\view\PlainView;

class ReponseManager
{
    /**
     * @var TemplateManager
     */
    private $_templateManager;

    /**
     * @var PlainView
     */
    private $_notFoundTemplate;

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
     * @param PlainView $notFoundTemplate
     */
    public function setNotFoundTemplate($notFoundTemplate)
    {
        $this->_notFoundTemplate = $notFoundTemplate;
    }

    /**
     * @param string|AbstractView $dataResponse
     * @throws \Exception
     * @return HtmlReponse
     */
    public function getHtmlResponse($dataResponse)
    {
        if ($dataResponse instanceof AbstractView) {
            if (is_null($this->_templateManager)) {
                throw new \Exception('TemplateManager is not set.');
            }

            return new HtmlReponse(200, $this->_templateManager->render($dataResponse));
        } else {
            return new HtmlReponse(200, $dataResponse);
        }
    }

    /**
     * @param string $messageErreur
     * @return HtmlReponse
     */
    public function getNotFoundTemplate($messageErreur)
    {
        return new HtmlReponse(404, $this->_templateManager->render(
            $this->_notFoundTemplate->withMap(
                array(
                    'errorCode' => 404,
                    'errorMessage' => $messageErreur
                )
            )
        ));
    }
}