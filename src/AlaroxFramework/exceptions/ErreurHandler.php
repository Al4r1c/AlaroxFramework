<?php
namespace AlaroxFramework\exceptions;

class ErreurHandler
{
    /**
     * @codeCoverageIgnore
     */
    public function setHandler()
    {
        set_error_handler(array($this, 'errorHandler'));
    }

    /**
     * @param int $codeErreur
     * @param string $messageErreur
     * @param string $fichierErreur
     * @param int $ligneErreur
     * @throws \ErrorException
     * @return bool
     */
    public function errorHandler($codeErreur, $messageErreur, $fichierErreur, $ligneErreur)
    {
        if (0 === error_reporting()) {
            return false;
        }

        throw new \ErrorException($messageErreur, 0, $codeErreur, $fichierErreur, $ligneErreur);
    }
}