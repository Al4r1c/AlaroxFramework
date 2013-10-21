<?php
namespace AlaroxFramework\reponse;

use AlaroxFramework\utils\HtmlReponse;
use AlaroxFramework\utils\view\AbstractView;

class ReponseManager
{
    /**
     * @var TemplateManager
     */
    private $_templateManager;

    /**
     * @var \Closure
     */
    private $_notFoundClosure;

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
     * @param \Closure $notFoundClosure
     */
    public function setNotFoundClosure($notFoundClosure)
    {
        $this->_notFoundClosure = $notFoundClosure;
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
    public function getNotFoundClosure($messageErreur)
    {
        $notFoundTemplate = call_user_func($this->_notFoundClosure);

        return new HtmlReponse(404, $this->_templateManager->render(
            $notFoundTemplate->withMap(
                array(
                    'errorCode' => 404,
                    'errorMessage' => $messageErreur
                )
            )
        ));
    }
}