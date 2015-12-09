<?php
/**
 * @author: Arem G. Aguinaldo
 * @date: 11/16/15
 * @email: ayhem30@gmail.com
 */
namespace Application;

use Application\MvcApplication;

class MvcMailer
{
    public static $MvcMailer;
    private $emailTo;
    private $subject;
    private $body;

    public static function init()
    {
        if (!self::$MvcMailer instanceof MvcMailer) {
            self::$MvcMailer = new MvcMailer();
        }
        return self::$MvcMailer;
    }

    public function addEmailTo($email)
    {
        $pattern = "/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/i";
        if (preg_match($pattern, trim(strip_tags($email)))) {
            $this->emailTo = trim(strip_tags($email));
        } else {
            throw new \Exception('Invalid Email Address.');
        }

        return $this;
    }

    public function addEmailSubject($subject)
    {
        $this->subject = trim($subject);

        return $this;
    }

    public function composeBody($data)
    {
        $configuration = MvcApplication::get()->getConfiguration();

        $this->body = '<html><body>';
        $this->body .= '<table rules="all" style="border-color: #666;" cellpadding="10">';
        $this->body .= "<tr style='background: #ff0000;'><td colspan='2'><strong>This email is for user verification. Please click the Account Activation Link below for activation of your account</strong> </td></tr>";
        $this->body .= "<tr style='background: #eee;'><td><strong>Full Name:</strong> </td><td>" . strip_tags($data['firstname']. ' ' . $data['middlename'] . ' '. $data['firstname']) . "</td></tr>";
        $this->body .= "<tr><td><strong>Email:</strong> </td><td>" . strip_tags($data['email']) . "</td></tr>";
        $this->body .= "<tr><td><strong>Password:</strong> </td><td>" . strip_tags($data['unhashed']) . "</td></tr>";
        $this->body .= "<tr><td><strong>Phone Number:</strong> </td><td>" . strip_tags($data['phonenumber']) . "</td></tr>";
        $this->body .= "<tr><td><strong>Link for Account Activation:</strong> </td><td>" .
            strip_tags($configuration['app']['path'] . '/registration/activation/') . base64_encode($data['email']) . "</td></tr>";
        $this->body .= "</table>";
        $this->body .= "</body></html>";

        return $this;
    }

    public function send()
    {

        if (empty($this->emailTo)) {
            throw new \Exception('Email To is required.');
        }

        if (empty($this->subject)) {
            throw new \Exception('Subject is required.');
        }

        if (empty($this->body)) {
            throw new \Exception('Body is required.');
        }

        $headers = "From: <no-reply@aremaguinaldosimpleregistration.com>\r\n";
        $headers .= "Reply-To: <no-reply@aremaguinaldosimpleregistration.com>\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

        if (mail($this->emailTo, $this->subject, $this->body, $headers)) {
            return true;
        }

        return false;
    }
}
