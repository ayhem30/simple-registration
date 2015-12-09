<?php
/**
 * @author: Arem G. Aguinaldo
 * @date: 11/16/15
 * @email: ayhem30@gmail.com
 */
return array(
    // Database Configuration
    'db' => array(
        'host'     => 'localhost',    // database host
        'username' => 'root',         // database username
        'password' => 'pass',         // database password
        'schema'   => 'registration', // schema/database name
    ),

    // Application url
    'app' => array(
        'environment' => 'development', // environment [development,staging,production] for Error Reporting
        'path'        => 'http://localhost:8080/registration', // application base url
    ),

    // Routing
    'routes' => array(
        'default' => array(
            'controller' => 'Registration\Controllers\Registration',
            'action'     => 'view',
        ),
        '/registration\/save\//' => array(
             'controller' => 'Registration\Controllers\Registration',
             'action'     => 'saveRegistration',
        ),
        '/registration\/activate\/(.*?)/' => array(
             'controller' => 'Registration\Controllers\Registration',
             'action'     => 'activateAccount',
        )
    ),
);