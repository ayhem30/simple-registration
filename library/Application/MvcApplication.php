<?php
/**
 * @author: Arem G. Aguinaldo
 * @date: 11/16/15
 * @email: ayhem30@gmail.com
 */
namespace Application;

class MvcApplication
{
    private static $application;
    public $configuration;

    public static function init($config)
    {
        if (!self::$application instanceof MvcApplication) {
            self::$application = new MvcApplication($config);
        }
        return self::$application;
    }

    public static function get()
    {
        if (!self::$application instanceof MvcApplication) {
            return false;
        }
        return self::$application;
    }

    public function __construct($config)
    {
        $this->setConfiguration($config);
    }

    public function run()
    {
        $this->setErrorReporting();
        $this->removeMagicQuotes();
        $this->unregisterGlobals();
        $this->dispatch();
    }

    private function setErrorReporting()
    {
        if ($this->configuration['app']['environment'] == 'development') {
            error_reporting(E_ALL);
            ini_set('display_errors','On');
        } else {
            error_reporting(E_ALL);
            ini_set('display_errors','Off');
            ini_set('log_errors', 'On');
            ini_set('error_log', ROOT.DS.'data'.DS.'logs'.DS.'error.log');
        }
    }

    private function stripSlashesDeep($value)
    {
        $value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
        return $value;
    }

    private function removeMagicQuotes() {
        if ( get_magic_quotes_gpc() ) {
            $_GET    = $this->stripSlashesDeep($_GET   );
            $_POST   = $this->stripSlashesDeep($_POST  );
            $_COOKIE = $this->stripSlashesDeep($_COOKIE);
        }
    }

    private function unregisterGlobals() {
        if (ini_get('register_globals')) {
            $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
            foreach ($array as $value) {
                foreach ($GLOBALS[$value] as $key => $var) {
                    if ($var === $GLOBALS[$key]) {
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }

    private function performAction($controller,$action,$queryString = null,$render = 0) {

        $controllerName = ucfirst($controller).'Controller';
        $dispatch = new $controllerName($controller,$action);
        $dispatch->render = $render;
        return call_user_func_array(array($dispatch,$action),$queryString);
    }

    private function routeURL($url) {
        foreach ( $this->configuration['routes'] as $pattern => $result ) {
            if ( $pattern != 'default' && preg_match( $pattern, $url ) ) {
                $parameters = explode('/',$url);
                unset($parameters[0]);
                unset($parameters[1]);
                return array_merge($result,array('parameters' => $parameters));
            }
        }
        throw new \Exception('Route Not Found. [' . __FILE__ . ' line ' . __LINE__ . ']');
    }

    private function dispatch() {
        $queryString = array();

        if (!isset($_GET['url'])) {
            $controller = $this->configuration['routes']['default']['controller'];
            $action     = $this->configuration['routes']['default']['action'];
        } else {
            $route = $this->routeURL(@$_GET['url']);
            if ( is_array(($route)) ) {
                $controller = $route['controller'];
                $action     = $route['action'];
                $queryString = $route['parameters'];
            } else {
                throw new \Exception('Route Not Found. [' . __FILE__ . ' line ' . __LINE__ . ']');
            }
        }

        $controllerName = ucfirst($controller).'Controller';
        $actionName = $action . 'Action';

        $dispatch = new $controllerName();
        if ((int)method_exists($controllerName, $actionName)) {
            call_user_func_array(array($dispatch,'setController'),array($controller));
            call_user_func_array(array($dispatch,'setAction'),array($action));
            call_user_func_array(array($dispatch,$actionName),array($queryString));
        } else {
            throw new \Exception('Method Not Found. [' . __FILE__ . ' line ' . __LINE__ . ']');
        }
    }

    public function setConfiguration($config)
    {
        $this->configuration = $config;
    }

    public function getConfiguration()
    {
        return $this->configuration;
    }
}
