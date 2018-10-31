<?php 

namespace Couts;

use Rain\Tpl;

class Page
{
    private $tpl;

    private $options;

    private $defaults = [
        'data' => [],
        'header' => true,
        'footer' => true
    ];

    public function __construct($opts = [], $tpl_dir = "/views/")
    {
        //$this->defaults["data"]["session"] = $_SESSION;
        $this->options = array_merge($this->defaults, $opts);

        $config = array(
            "tpl_dir" => $_SERVER["DOCUMENT_ROOT"] . $tpl_dir,
            "cache_dir" => $_SERVER["DOCUMENT_ROOT"] . "/views_cache/",
            "debug" => false // set to false to improve the speed
        );

        Tpl::configure($config);

        $this->tpl = new Tpl;

        $this->setData($this->options['data']);

        if ($this->options['header'] == true) {
            $this->tpl->draw("header");
        }
    }

    private function setData($data = [])
    {
        foreach ($data as $key => $value) {
            $this->tpl->assign($key, $value);
        }
    }

    public function setTpl($name, $data = [], $returnHtml = false)
    {
        $this->setData($data);

        return $this->tpl->draw($name, $returnHtml);
    }

    public function __destruct()
    {
        if ($this->options['footer'] == true) {
            $this->tpl->draw("footer");
        }
    }
}