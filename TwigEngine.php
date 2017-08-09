<?php

namespace Skvn\View;

class TwigEngine extends Engine
{
    const ENGINE_NAME = 'twig';

    private $twig;


    function init()
    {
        $loader = new \Twig_Loader_Filesystem($this->config['templates_path']);
        $this->twig = new \Twig_Environment($loader, $this->config);
        foreach ($this->config['extensions'] ?? [] as $ext) {
            $this->twig->addExtension(new $ext());
        }
    }

    function render(array $vars):string
    {
        return $this->twig->render($this->template, $vars);
    }

    function getEngine()
    {
        return $this->twig;
    }
}