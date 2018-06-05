<?php
namespace MiniPHP;

use MiniPHP\App;

class View
{
	protected $data;
    protected $framework;
    protected $src;

    /**
     * @param $src Source file to load
     * @param array $vars Associative key , values
     * @param null $framework isntance
     */
    public function __construct($src, array $vars = array(), App $framework = null){
        $this->data = $vars;
        $this->framework = $framework;
        $this->src = $src;
    }

    /**
     * Renders a view
     * @throws Exception if View not found
     */
    public function load(){
        $app = $this->framework;
        $data = $this->data; //deprecated, vars are passed directly since version 0.0.4
        extract($this->data, EXTR_OVERWRITE);//set global all variables to the view

        if (file_exists($this->src))
            include_once($this->src); //scoped to this class
        else{
            if($this->framework ){
                if($app->getEnvironment() == ENV_DEV)
                    return $app->error("View filename '{$this->src}' NOT found in '". VIEWS_ROUTE."'.<br/>
                     Maybe you need to change the App::APP_DIR or App::VIEW_DIR Constant to your current folder structure.",2);
                else
                    return $app->error('',2);
            }
        }
    }
}