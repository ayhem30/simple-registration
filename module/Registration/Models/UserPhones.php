<?php
/**
 * @author: Arem G. Aguinaldo
 * @date: 11/18/15
 * @email: ayhem30@gmail.com
 */
namespace Registration\Models;

use Application\MvcDb;

class UserPhones
{
    public static $UsersPhones;

    public static function init()
    {
        if (!self::$UsersPhones instanceof UserPhones) {
            self::$UsersPhones = new UserPhones();
        }
        return self::$UsersPhones;
    }

    public function insertUserPhones(&$data,$id)
    {
        if (empty($data)) {
            throw new \Exception('Data parameter should not be empty');
        }

        if (!is_array($data['phonenumber'])) {
            throw new \Exception('Phone Number should be an array');
        }

        $phone_id = $values = array();

        foreach ($data['phonenumber'] as $value) {
            if (is_numeric($value)) {
                $phone_id[] = MvcDb::init()->insert(array('user_id','phone_number'),'phone_numbers',array($id,$value))
                                           ->execute()
                                           ->getLastInsertId();
            }

        }

        $data['phonenumber'] = implode(',',$data['phonenumber']);

        return count($phone_id);

    }
}
