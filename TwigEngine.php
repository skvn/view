<?php

namespace Skvn\View;

class TwigEngine extends Engine
{
    const ENGINE_NAME = 'twig';

    private $twig;


    function init()
    {
        $loader = new \Twig_Loader_Filesystem($this->config['templates_path']);
//        $args = array();
//        $conf = \App :: config('view.twig');
//        if (!empty($conf['compile_tpl']))
//        {
//            $args['cache'] = \App :: getPath('@var/twig');
//        }
//        if (!empty($conf['strict_mode']))
//        {
//            $args['strict_variables'] = true;
//        }

        $this->twig = new \Twig_Environment($loader, $this->config);
        //$twig->addExtension(new Twig_Extension_Timer());
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