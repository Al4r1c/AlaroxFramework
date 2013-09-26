<?php
namespace AlaroxFramework\utils\twig;

use AlaroxFramework\cfg\configs\TemplateConfig;

class TwigEnvFactory
{
    /**
     * @var TemplateConfig
     */
    private $_templateConfig;

    /**
     * @param TemplateConfig $templateConfig
     */
    public function setTemplateConfig($templateConfig)
    {
        $this->_templateConfig = $templateConfig;
    }

    /**
     * @param string $typeLoader
     * @throws \Exception
     * @return \Twig_Environment
     */
    public function getTwigEnv($typeLoader)
    {
        switch ($typeLoader) {
            case 'PlainView':
                $loader = new \Twig_Loader_String();

                return new \Twig_Environment($loader);
                break;
            case 'TemplateView':
                $loader = new \Twig_Loader_Filesystem(array($directory = $this->_templateConfig->getTemplateDirectory()));

                $options = array(
                    'cache' => false,
                    'charset' => $this->_templateConfig->getCharset(),
                    'autoescape' => 'html',
                    'strict_variables' => false,
                    'optimizations' => -1
                );

                if ($this->_templateConfig->isCacheEnabled() === true) {
                    $options = array(
                            'cache' => $directory . '/cacheTemplate/',
                            'auto_reload' => true
                        ) + $options;
                }

                return new \Twig_Environment($loader, $options);
                break;
            default:
                throw new \Exception(sprintf('Loader type "%s" not found.', $typeLoader));
                break;
        }
    }
}