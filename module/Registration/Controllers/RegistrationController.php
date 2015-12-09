<?php
/**
 * @author: Arem G. Aguinaldo
 * @date: 11/16/15
 * @email: ayhem30@gmail.com
 */
namespace Registration\Controllers;

use Application\MvcController;
use Application\MvcRequest;
use Application\MvcFileHandler;
use Application\MvcMailer;
use Registration\Models\Users;
use Registration\Models\UserPhones;
use Registration\Validators\UsersValidator;

class RegistrationController extends MvcController
{
    public function viewAction()
    {
        $this->render();
    }

    public function saveRegistrationAction()
    {
        $jsonData = array(
            'hasError'     => 1,
            'errorMessage' => 'Oops. Something went wrong. Please try again.'
        );

        try {

            // get file request data
            $postData = MvcRequest::get()->postData();
            $filesData = MvcRequest::get()->fileData();

            if (!empty($postData)) {

                // parse post data
                parse_str(urldecode($postData['data']),$parameters);

                // validate parameters
                if(UsersValidator::init()->validate($parameters)) {

                   // save image
                   if (isset($filesData['profile_picture']) && !empty($filesData['profile_picture'])) {
                       $pictureResponse = MvcFileHandler::init()->saveFile($filesData['profile_picture']);

                       // check if saving image has error
                       if (!$pictureResponse['hasError']) {
                           $parameters['profile_picture'] = $pictureResponse['fileName'];
                       } else {
                           $jsonData['errorMessage'] .= "<br/>" . $pictureResponse['errorMessage'];
                       }
                   }
                    // insert to users table.
                    $id = Users::init()->insertUser($parameters);

                    if ($id) {
                        // insert phone
                        UserPhones::init()->insertUserPhones($parameters,$id);

                        // send email to user
                        $emailResponse = MvcMailer::init()->addEmailTo($parameters['email'])
                                                          ->addEmailSubject('Activation of Registration.')
                                                          ->composeBody($parameters)
                                                          ->send();

                        if ($emailResponse) {
                            $jsonData['hasError'] = 0;
                            $jsonData['errorMessage'] = 'Successfully Registered. Please check your email.';
                        }
                    }
                } else {
                   $jsonData['errorMessage'] = implode('<br/>',UsersValidator::init()->getErrorMessages());
                }
            }
        } catch (\Exception $e) {
            $jsonData['errorMessage'] = $e->getMessage();
        }

        $this->set('jsonData',$jsonData);
        $this->render(1);
    }

    public function activateAccountAction($parameters)
    {
        $this->set('successActivation', false);

        try {
            if (isset($parameters[2])) {
                $affected = Users::init()->activateUser(array('email' => $parameters[2]));
                if ($affected) {
                    $this->set('successActivation', true);
                }
            }
        }  catch (\Exception $e) {

        }

        $this->set('viewTemplate','success');
        $this->render();
    }
}
