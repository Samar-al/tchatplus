<?php
try {
    require_once('../../config/config.inc.php');
    require_once('../../init.php');
    require_once('./tchatplus.php');
    require_once(_PS_MODULE_DIR_.'tchatplus/classes/TchatPlusAdminClass.php');

    
    $input = file_get_contents('php://input');
    
    $data = [];
    parse_str($input, $data);
    
   
    $response = $data['response'];
    $sender = $data['sender'];
    
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

   
    if(!empty($response) && !empty($sender) && !empty($created_at)){
       
       $tchatplusAdmin = new TchatPlusAdminClass();
       
       $tchatplusAdmin->response = $response;
       $tchatplusAdmin->from_sender = $sender;
       $tchatplusAdmin->created_at = $created_at;
       $tchatplusAdmin->add();
    }


} catch (Exception $e) { 
    // Handle the exception
    header('HTTP/1.1 500 Internal Server Error');
   // echo json_encode(['error' => $e->getMessage()]);
}
