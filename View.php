<?php

namespace Skvn\View;

use Skvn\Base\Traits\ConstructorConfig;
use Skvn\Base\Helpers\File;
use Skvn\Base\Traits\AppHolder;

class View
{
    use ConstructorConfig;
    use AppHolder;

    protected $resolved = null;
    protected $engine = null;
    protected $vars = [];

    protected function init()
    {
        if (empty($this->config['engines'])) {
            throw new Exceptions\ViewException('No View engines defined');
        }
    }

    function createNew()
    {
        return new self($this->config);
    }
    
    function make($filename)
    {
        return $this->createNew()->resolve($filename);
    }
    
    function renderTemplate($filename, $args = [])
    {
        $view = $this->make($filename);
        $view->set($args);
        return $view->render();
    }

    function resolve($filename)
    {
        if ($this->resolved) {
            throw new Exceptions\ViewException('Unable to resolve: ' . $filename . '. ' . $this->resolved . ' already resolved');
        }
        $ext = File :: getExtension($filename);
        if (array_key_exists($ext, $this->config['engines'])) {
            $class = $this->config['engines'][$ext]['class'];
            $args = $this->config['engines'][$ext];
        } else {
            $config = $this->config;
            $engine = array_shift($config['engines']);
            $class = $engine['class'];
            $args = $engine;
        }

        $this->engine = new $class($args);
        $this->engine->setTemplate($filename);
        $this->resolved = $filename;
        return $this;
    }

    function set($key, $value = null, $push = false)
    {
//        if ($this->resolved) {
//            return $this->engine->set($key, $value);
//        }
        if (is_array($key)) {
            foreach ($key as $k => $v) {
                $this->setVar($k, $v, $push);
            }
            return;
        }
        $this->setVar($key, $value, $push);
    }

    function setVar($key, $value, $push = false)
    {
        if ($this->resolved) {
            if ($this->engine->setVar($key, $value, $push) === true) {
                return;
            }
        }
        if ($push) {
            if (!isset($this->vars[$key])) {
                $this->vars[$key] = [];
                if (!empty($value)) {
                    $this->vars[$key][] = $value;
                }
            } else {
                $this->vars[$key][] = $value;
            }
        } else {
            $this->vars[$key] = $value;
        }
    }

    function setRaw($vars)
    {
        $this->vars = $vars;
    }

    function render()
    {
        if (!$this->resolved) {
            throw new Exceptions\ViewException('Template is not resolved');
        }
        $html = $this->engine->render($this->vars);
        if (!empty($this->config['compress_html'])) {
            $html = (new HtmlMinimizer())->compress($html);
        }
        return $html;
    }

    function resolved()
    {
        return $this->resolved;
    }

    function getEngineName()
    {
        return $this->resolved ? $this->engine->getEngineName() : false;
    }

    function getEngine()
    {
        if (!$this->resolved) {
            throw new Exceptions\ViewException('View not resolved');
        }
        return $this->engine;
    }

    function getNativeEngine()
    {
        if (!$this->resolved) {
            throw new Exceptions\ViewException('View not resolved');
        }
        return $this->engine->getEngine();
    }

    function getVars()
    {
        return $this->vars;
    }

}