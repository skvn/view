<?php

namespace Skvn\View;

use Skvn\Base\Traits\ConstructorConfig;

abstract class Engine
{
    const ENGINE_NAME = 'dummy';

    use ConstructorConfig;

    protected $template;

    abstract function render(array $vars):string;

    function getEngineName()
    {
        return static :: ENGINE_NAME;
    }

    function setTemplate($template)
    {
        $this->template = $template;
    }

    function getEngine()
    {
        return null;
    }

    function setVar($key, $value, $push = false)
    {
        return false;
    }



}