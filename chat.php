<?php
try {
    require_once('../../config/config.inc.php');
    require_once('../../init.php');
    require_once('./tchatplus.php');
    require_once(_PS_MODULE_DIR_.'tchatplus/classes/TchatPlusClass.php');
    require_once(_PS_MODULE_DIR_.'tchatplus/classes/TchatPlusAdminClass.php');


    $message = file_get_contents('php://input');
    $from = Tools::getRemoteAddr();
    
    $isLogged = Context::getContext()->customer->isLogged();
    
    if ($isLogged) {
        $customerId = Context::getContext()->customer->id;
    } 
    
    function formatTime(){
        $selectedTimezone = Configuration::get('TIME_ZONE');

        // Get the current timestamp
        $currentTimestamp = time();

        // Create a DateTime object with the current timestamp and server's timezone
        $dateTime = new DateTime('@' . $currentTimestamp);

        // Set the desired timezone
        $dateTime->setTimezone(new DateTimeZone($selectedTimezone));

        $time =  $dateTime->format('Y-m-d H:i:s');
        return $time;
    }
   
    // Format the datetime in your desired format
    $created_at = formatTime();


    if(!empty($message) && !empty($from) && !empty($created_at)){

       $tchatplus = new TchatPlusClass();
       $tchatplus->message = $message;
       $tchatplus->from = $from;
       $tchatplus->created_at = $created_at;
       $tchatplus->customer_id = $customerId;
       $tchatplus->add();
    }


    // Fetch messages from the database
   
    $tchatplus = new TchatPlusClass();
    $messages = $tchatplus->getMessagesBySender($from);

    // Get the sender information from the query parameter
    $sender = Tools::getValue('sender');

    if ($sender !== null) {
        // Fetch messages from the database using $sender
        $tchatplusAdmin = new TchatPlusAdminClass();
        $adminResponse = $tchatplusAdmin->getAdminResponsesBySender($sender);
        $allMessages = array_merge($messages, $adminResponse);
        // Send the response
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/JSON');
        echo json_encode($allMessages);
    } else {
        header('HTTP/1.1 400 Bad Request');
        echo json_encode(['error' => 'Sender information missing']);
    }
   
    
        

} catch (Exception $e) { 
    // Handle the exception
    header('HTTP/1.1 500 Internal Server Error');
   // echo json_encode(['error' => $e->getMessage()]);
}
