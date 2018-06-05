<?php
namespace MiniPHP;

use MiniPHP\AbstractFramework;
use MiniPHP\Route;
use MiniPHP\Request;
use MiniPHP\View;

const ENV_DEV = 0;
const ENV_PROD = 1;
const APP_NAME = '';

class App extends AbstractFramework
{
	protected $db;
	protected $request;
	protected $routes = array();
	
	public function __construct($prod = false)
	{
		parent::__construct();

		define('APP_DIR', $this->getRootDir() .'/../'); //if your project is in src/ like in documentation, if not correct this
        define('VIEW_DIR', APP_DIR .'views/');
        define('CONTROLLER_DIR', APP_DIR .'controllers/');
        define('VIEWS_ROUTE', APP_DIR .'views/');//deprecated since 0.4
        define('CONTROLLERS_ROUTE', APP_DIR .'controllers/');//deprecated since 0.4

        $this->setEnvironment($prod);
	}

	public function getRootDir()
    {
        return __DIR__;
    }

	public function  setEnvironment($prod = false){
        $this->prod = $prod ? ENV_PROD : ENV_DEV;
    }
	
	public function get($uri, callable $callbacak)
	{
		$this->routes['GET'][] = new Route($uri , $callback);
	}
	
	public function post($uri, callable $callback)
	{
		$this->routes['POST'][] = new Route($uri, $callback);
	}
	
	public function put($uri, callable $callback)
	{
		$this->routes['PUT'][] = new Route($uri, $callback);
	}
	
	public function delete($uri, callable $callback)
	{
		$this->routes['DELETE'][] = new Route($uri, $callback);
	}
	
	public function respond(callable $callback)
	{
		$this->routes['respond'] = new Route('', $callback);
	}
	
	public function listen()
	{
		$slugs = array();
		$run = $this->traverseRoutes($this->request->getMethod(), $this->routes, $slugs);
		
		if(!$run && (!isset($this->routes['respond']) || empty($this->routes['respond']))){
			return $this->error("Route path not found: {$this->request->getRequestUri()} with method: {$this->request->getMethod()}" , true);
		}
		else if(!$run){
			$callback = $this->routes['respond']->function();
            $callback();
		}
		return true;
	}
	
	public function getRoutes()
	{
		return $this->routes;
	}

	public function generateRoute($uri){
        return (APP_NAME != '') ?('/'.APP_NAME.$uri) : $uri;
    }

	public function getRoute($uri){//deprecated since 0.5
        return $this->generateRoute($uri);
    }

	public function getEnvironment(){
        return $this->prod ? ENV_PROD : ENV_DEV;
    }
	
	public function getRequest()
	{
		return $this->request;
	}
	
	public function Redirect($url)
	{
		echo header('Location: '.$url);
	}
	
	public function Response($filename = '', array $vars = array(), $status = 200, array $headers = array(),$asText = 0){
        $this->setStatusCode($status);

        if (count($headers)){//add extra headers
            $this->addCustomHeaders($headers);
        }
        //pass to the view
        if (!$asText){
            $view = new View(VIEWS_ROUTE.$filename, $vars, $this);
            $view->load();
        }
        else echo $filename;
    }

	public function ResponseHTML($html = '', $status = 200, array $headers = array()){
        return $this->Response($html, array(), $status, $headers, true);
    }

	public function JsonResponse($data = null, $status = 200, array $headers = array() ){
        //$this->setStatusCode($status);

        header('Content-Type: application/json');//set content type to Json
        if (count($headers) > 0 ){//add extra headers
            $this->addCustomHeaders($headers);
        }

        echo json_encode($data);
    }

	private function addCustomHeaders(array $headers = array()){
        foreach($headers as $key=>$header){
            header($key.': '.$header);
        }
    }

	public function error($msg = '', $number = 0){
        $status = 500;
        switch ($number){
            case 1://not found
                $status = $this->setStatusCode(404);
                break;
            default://internal server error code
                $this->setStatusCode(500);
                break;
        }

        if ($this->getEnvironment() == ENV_PROD){
            echo "<div style=\"padding-top: 10px; padding-left: 10px;\">
                      <h2>:( </h2>
                      <h3>Huston, We have a problem with this request.</h3>
                      <p>There is status code: <strong>$status</strong></p>
                      <p>Please try later or contact us.</p>
                 </div> ";
        }
        else if ($this->getEnvironment() == ENV_DEV){//debug enable
            echo" <div style=\"padding-top: 10px; padding-left: 10px;\">
                      <h2>Error</h2>
					  <h4>: (</h4>
                      <p>The server return <strong>$status</strong> status code.</p>
                      <p>$msg</p>";

            switch($number){
                case 1:
                    echo "
                    <p> <b>Note</b>: Routes begin always with '/' character.</p>";
                    break;
                default:
                    $this->setStatusCode(500);
                    break;
            }

            echo "  <h4>Exception:</h4>";
            throw new \Exception($msg);
            echo "</div>";
        }
        else{
            // Here your custom environments
        }
        return false;
    }
	
}