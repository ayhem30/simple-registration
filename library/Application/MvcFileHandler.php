<?php
/**
 * @author: Arem G. Aguinaldo
 * @date: 11/16/15
 * @email: ayhem30@gmail.com
 */
namespace Application;

class MvcFileHandler
{
   public static $MvcFileHandler;

   public static function init()
   {
        if (!self::$MvcFileHandler instanceof MvcFileHandler) {
           self::$MvcFileHandler = new MvcFileHandler();
        }
        return self::$MvcFileHandler;
   }

   public function saveFile($fileData)
   {
       try {

           // Undefined | Multiple Files | $_FILES Corruption Attack
           // If this request falls under any of them, treat it invalid.
           if (
               !isset($fileData['error']) ||
               is_array($fileData['error'])
           ) {
               throw new \RuntimeException('Invalid parameters.');
           }

           // Check ['error'] value.
           switch ($fileData['error']) {
               case UPLOAD_ERR_OK:
                   break;
               case UPLOAD_ERR_NO_FILE:
                   throw new \RuntimeException('No file sent.');
               case UPLOAD_ERR_INI_SIZE:
               case UPLOAD_ERR_FORM_SIZE:
                   throw new \RuntimeException('Exceeded filesize limit.');
               default:
                   throw new \RuntimeException('Unknown errors.');
           }

           // check filesize.
           if ($fileData['size'] > 1000000) {
               throw new \RuntimeException('Exceeded filesize limit.');
           }

           // Check MIME Type by yourself.
           switch($fileData['type']) {
               case 'image/jpeg' :
                   $ext = 'jpg';
                   break;
               case 'image/png' :
                   $ext = 'png';
                   break;
               case 'image/gif' :
                   $ext = 'gif';
                   break;
               default:
                   throw new \RuntimeException('Invalid file format.');
           }

           // moving files with unique name
           $fileName = sprintf(ROOT.DS.'data'.DS.'uploads'.DS.'%s.%s',
               sha1_file($fileData['tmp_name']),
               $ext
           );

           if (!move_uploaded_file(
               $fileData['tmp_name'],
               $fileName
           )) {
               throw new \RuntimeException('Failed to move uploaded file.');
           }

           return array('hasError' => 0 , 'fileName' => $fileName);

       } catch (\RuntimeException $e) {

           return array('hasError' => 1, 'errorMessage' => $e->getMessage());

       }
   }
}
