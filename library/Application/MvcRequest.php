<?php
/**
 * @author: Arem G. Aguinaldo
 * @date: 11/16/15
 * @email: ayhem30@gmail.com
 */
namespace Application;

class MvcRequest
{
    protected static $MvcRequest;

    public static function set()
    {
        if (!self::$MvcRequest instanceof MvcRequest) {
           self::$MvcRequest = new MvcRequest();
        }
        return self::$MvcRequest;
    }

    public static function get()
    {
        if (!self::$MvcRequest instanceof MvcRequest) {
            self::$MvcRequest = new MvcRequest();
        }
        return self::$MvcRequest;
    }

    public function postData()
    {
       return $_POST;
    }

    public function getData()
    {
        return $_GET;
    }

    public function fileData()
    {
        return $_FILES;
    }
}
