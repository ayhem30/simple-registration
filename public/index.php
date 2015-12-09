<?php
/**
 * Created by Arem G. Aguinaldo
 * Date: 11/16/15
 */

define('DS', DIRECTORY_SEPARATOR);
define('ROOT', dirname(dirname(__FILE__)));

$included_path = array(
    ROOT,
    ROOT . DS . 'config',
    ROOT . DS . 'library',
    ROOT . DS . 'module',
    get_include_path(),
);

set_include_path(implode(';',$included_path));

/** Autoload any classes that are required **/
function fileLocator($className) {
    if (file_exists(ROOT . DS . 'library' . DS . str_replace('\\','/',ucwords($className)) . '.php') ||
        file_exists(ROOT . DS . 'module' . DS . str_replace('\\','/',ucwords($className)) . '.php')) {
        require_once str_replace('\\','/',ucwords($className)) . '.php';
    } else {
        throw new Exception('Application File Not Found [' . __FILE__ . ' line ' . __LINE__ . ']');
    }
}
spl_autoload_register('fileLocator');

\Application\MvcApplication::init(include_once 'config.php')->run();
