<?php
/**
 * @author: Arem G. Aguinaldo
 * @date: 11/16/15
 * @email: ayhem30@gmail.com
 */
namespace Application;

use Application\MvcApplication;

class MvcHtml
{
    public function includeJs($fileName) {
        $path = (MvcApplication::get()) ? MvcApplication::get()->configuration['app']['path'] : '';
        $data = '<script type="text/javascript" src="'.$path.'/js/'.$fileName.'"></script>';
        return $data;
    }

    public function includeCss($fileName) {
        $path = (MvcApplication::get()) ? MvcApplication::get()->configuration['app']['path'] : '';
        $data = '<link rel="stylesheet" href="'.$path.'/css/'.$fileName.'" media="screen" type="text/css"/>';
        return $data;
    }

    public function getBaseUrl() {
        return (MvcApplication::get()) ? MvcApplication::get()->configuration['app']['path'] : '';
    }
}
