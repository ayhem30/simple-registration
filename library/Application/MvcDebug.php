<?php
/**
 * @author: Arem G. Aguinaldo
 * @date: 11/16/15
 * @email: ayhem30@gmail.com
 */
namespace Application;

class MvcDebug
{
    public static function dump($data)
    {
        echo '<pre>';
        print_r($data);
        echo '</pre>';
    }
}
