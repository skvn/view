<?php

namespace Skvn\View;

class TwigEngine extends Engine
{
    const ENGINE_NAME = 'twig';

    private $twig;


    function init()
    {
        $paths = [$this->config['templates_path']];
        foreach ($this->config['path_finders'] ?? [] as $finder) {
            $path = (new $finder)->getPath();
            if (!empty($path)) {
                array_unshift($paths, $path);
            }
        }

        $loader = new \Twig\Loader\FilesystemLoader($paths);
        $this->twig = new \Twig\Environment($loader, $this->config);
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