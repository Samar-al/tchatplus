<?php
//require_once('../../config/config.inc.php');
//require_once('../../init.php');
//require_once('./tchatplus.php');



class TchatPlusOfflinemailsModuleFrontController extends ModuleFrontController
{

    public function initContent()
    {
        
        $message = Tools::getValue('content');

        // Retrieve configuration values for activated fields
        $isNameActivated = Configuration::get('OFFLINE_FIELDS_NAME', false);
        $isFirstnameActivated = Configuration::get('OFFLINE_FIELDS_FIRSTNAME', false);
        $isPhoneActivated = Configuration::get('OFFLINE_FIELDS_PHONE', false);
        $isEmailActivated = Configuration::get('OFFLINE_FIELDS_EMAIL', false);
        $isMessageActivated = Configuration::get('OFFLINE_FIELDS_MESSAGE', false);

        // Split the message into an array of lines
        $lines = explode("\n", $message);
        
        // Initialize variables to store extracted information
        $name = '';
        $firstname = '';
        $phone = '';
        $email = '';
        $messageText = '';

        // Initialize error flag and error messages
        $hasError = false;
        $errorMessages = [];
        
        // Loop through each line to extract information
        foreach ($lines as $line) {
        // Split each line into "Key: Value" pairs
            $pair = explode(": ", $line, 2);

            if (count($pair) === 2) {
                $key = trim($pair[0]);
                $value = trim($pair[1]);

                switch ($key) {
                    case 'Name':
                        $name = $value;
                        break;
                    case 'First Name':
                        $firstname = $value;
                        break;
                    case 'Phone':
                        $phone = $value;
                        break;
                    case 'Email':
                        $email = $value;
                        break;
                    case 'Message':
                        $messageText = $value;
                        break;
                    default:
                        // Handle unexpected keys
                        break;
                }
            }
    }

        // Validate input based on activated fields
        if ($isNameActivated && empty($name)) {
            $hasError = true;
            $errorMessages[] = 'Name is required.';
        }
        if ($isFirstnameActivated && empty($firstname)) {
            $hasError = true;
            $errorMessages[] = 'First Name is required.';
        }
        if ($isPhoneActivated && empty($phone)) {
            $hasError = true;
            $errorMessages[] = 'Phone is required.';
        }
        if ($isEmailActivated && empty($email)) {
            $hasError = true;
            $errorMessages[] = 'Email is required.';
        }
        if ($isMessageActivated && empty($messageText)) {
            $hasError = true;
            $errorMessages[] = 'Message is required.';
        }

        // If any mandatory fields are missing, send error response
        if ($hasError) {
            $response = array(
                'success' => false,
                'message' => 'Please fill in all required fields.',
                'errors' => $errorMessages
            );
            echo json_encode($response);
            exit; // Terminate the script
        }

        echo "Name: $name<br>";
        echo "First Name: $firstname<br>";
        echo "Phone: $phone<br>";
        echo "Email: $email<br>";
        echo "Message: $messageText<br>";
        

        $emailContent = "
        Name: $name\n
        First Name: $firstname\n
        Phone: $phone\n
        Email: $email\n
        Message: $messageText
        ";



        Mail::Send(
            (int)(Configuration::get('PS_LANG_DEFAULT')), // defaut language id
            'contact', // email template file to be use
            ' Offline Message', // email subject
            array(
                '{email}' => Configuration::get('NOTIFICATION_EMAIL'), // sender email address
                '{message}' => $emailContent // email content
            ),
            Configuration::get('NOTIFICATION_EMAIL'), // receiver email address
            NULL, //receiver name
            NULL, //from email address
            NULL,  //from name
            NULL, //file attachment
            NULL, //mode smtp
            _PS_MODULE_DIR_ . 'tchatplus/mails' //custom template path
        );

    }
}
