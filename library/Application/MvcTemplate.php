<?php
/**
 * @author: Arem G. Aguinaldo
 * @date: 11/16/15
 * @email: ayhem30@gmail.com
 */
namespace Application;

class MvcTemplate
{
    protected $variables = array();
    protected $controller;
    protected $action;

    /** Set Variables **/

    public function set($name,$value) {
        $this->variables[$name] = $value;
    }

    /** Display Template **/
    public function render($doNotRenderHeader = 0) {


        if ($doNotRenderHeader == 0) {
            $controllerPath = explode('\\',$this->controller);
            $actionPath = strtolower($controllerPath[count($controllerPath) - 1]);
            unset($controllerPath[count($controllerPath) - 1]);
            $viewPath = implode(DS,$controllerPath);
            $html = new MvcHtml();
            extract($this->variables);

            if (file_exists(ROOT . DS . 'module' . DS . $viewPath . DS . '..' . DS . 'Views' . DS . 'layout' . DS . 'header.phtml')) {
                include (ROOT . DS . 'module' . DS . $viewPath . DS . '..' . DS . 'Views' . DS . 'layout' . DS . 'header.phtml');
            }

            if (file_exists(ROOT . DS . 'module' . DS . $viewPath . DS . '..' . DS . 'Views' . DS . $actionPath . DS . ((!isset($viewTemplate)) ? $this->action : $viewTemplate) .  '.phtml')) {
                include (ROOT . DS . 'module' . DS . $viewPath . DS . '..' . DS . 'Views' . DS . $actionPath . DS . ((!isset($viewTemplate)) ? $this->action : $viewTemplate) . '.phtml');
            }

            if (file_exists(ROOT . DS . 'module' . DS . $viewPath . DS . '..' . DS . 'Views' . DS . 'layout' . DS . 'footer.phtml')) {
                include (ROOT . DS . 'module' . DS . $viewPath . DS . '..' . DS . 'Views' . DS . 'layout' . DS . 'footer.phtml');
            }
        } else {
            echo json_encode($this->variables['jsonData']);
        }
    }
}
