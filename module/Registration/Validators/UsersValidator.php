<?php
/**
 * @author: Arem G. Aguinaldo
 * @date: 11/16/15
 * @email: ayhem30@gmail.com
 */
namespace Registration\Validators;

class UsersValidator
{
    public static $UsersValidator;
    private $errorMessage;

    public static function init()
    {
        if (!self::$UsersValidator instanceof UsersValidator) {
            self::$UsersValidator = new UsersValidator();
        }
        return self::$UsersValidator;
    }

    public function __construct()
    {
        $this->errorMessage = array();
    }

    public function validate($data)
    {
        if (strlen(trim($data['form-email'])) > 255) {
            $this->setErrorMessage('Email exceeds the character limit of 255');
            return false;
        }

        if (!ctype_alpha(trim($data['form-first-name']))) {
            $this->setErrorMessage('First Name should only contain Alphabet');
            return false;
        }

        if (strlen(trim($data['form-first-name'])) > 30) {
            $this->setErrorMessage('First Name exceeds the character limit of 30');
            return false;
        }

        if (!ctype_alpha(trim($data['form-last-name']))) {
            $this->setErrorMessage('Last Name should only contain Alphabet');
            return false;
        }

        if (strlen(trim($data['form-last-name'])) > 30) {
            $this->setErrorMessage('Last Name exceeds the character limit of 30');
            return false;
        }

        if (strlen(trim($data['form-middle-name'])) > 30) {
            $this->setErrorMessage('Middle Name exceeds the character limit of 30');
            return false;
        }

        if (!ctype_alnum(trim($data['form-password'])) && !stristr($data['form-password'],'_')) {
            $this->setErrorMessage('Password should only contain Alpha Numeric');
            return false;
        }

        if (strlen(trim($data['form-password'])) < 8) {
            $this->setErrorMessage('Your password must be at least 8 characters long');
            return false;
        }

        if (strlen(trim($data['form-password'])) > 24) {
            $this->setErrorMessage('Password exceeds the character limit of 24');
            return false;
        }

        if (trim($data['form-password']) != trim($data['form-confirm-password'])) {
            $this->setErrorMessage('Passwords does not match');
            return false;
        }

        if (!ctype_digit(trim($data['form-age'])) || trim($data['form-age']) < 18) {
            $this->setErrorMessage('Your age should be 18 years above');
            return false;
        }


        if (!empty($data['form-phone-number']) && is_array($data['form-phone-number'])) {
            foreach ($data['form-phone-number'] as $value) {
                if (!empty($value)) {
                    if (!ctype_digit(trim($value))) {
                        $this->setErrorMessage('Phone Number should be digits only.');
                        return false;
                    }
                }
            }
        }

        return true;
    }

    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage[] = $errorMessage;
    }

    public function getErrorMessages()
    {
        return $this->errorMessage;
    }
}