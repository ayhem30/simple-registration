<?php
/**
 * @author: Arem G. Aguinaldo
 * @date: 11/16/15
 * @email: ayhem30@gmail.com
 */
namespace Registration\Models;

use Application\MvcDb;

class Users
{
    public static $Users;

    public static function init()
    {
        if (!self::$Users instanceof Users) {
           self::$Users = new Users();
        }
        return self::$Users;
    }

    public function insertUser(&$data)
    {
        if (!is_array($data)) {
            throw new \Exception('Data parameter should be an array');
        }

        if (empty($data)) {
            throw new \Exception('Data parameter should not be empty');
        }

        $values = array();
        $parameters = array();

        foreach ($data as $key => $value) {
            $key = strtolower(str_replace(array('-','form'),'',$key));

            switch($key) {
                case 'gender' :
                    $key = 'sex';
                    break;
                case 'password' :
                    $parameters['unhashed'] = $value;
                    $value = \md5($value);
                    break;
            }

            if (!in_array($key,array('confirmpassword','birth','phonenumber','age'))) {
                $values[$key] = $value;
            } else {
                $parameters[$key] = $value;
            }
        }

        $data = array_merge($values,$parameters);

        $id = MvcDb::init()->insert(array_keys($values),'users',array_values($values))
                           ->execute()
                           ->getLastInsertId();

        if (!$id) {
            throw new \Exception(MvcDb::init()->getError());
        }

        return $id;

    }

    public function activateUser($data)
    {
        if (!is_array($data)) {
            throw new \Exception('Data parameter should be an array');
        }

        if (empty($data) && empty($data['email'])) {
            throw new \Exception('Data parameter should not be empty');
        }

        $where = array('email' => strip_tags(trim(base64_decode($data['email']))));

        $affected = MvcDb::init()->update(array('is_active' => 1),'users')
                                 ->where($where)
                                 ->execute()
                                 ->getAffectedRows();

        return $affected;

    }
}
