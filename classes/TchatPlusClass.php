<?php
/**
 * TchatPlusClass.php
 *
 * Represents the pending product comments class
 *
 * @author    Samar Al Khalil
 * @copyright Copyright (c)
 * @license   License (if applicable)
 * @category  Classes
 * @package  TchatPlus
 * @subpackage Classes
 */
class TchatPlusClass extends ObjectModel
{
    public $id_tchat_plus;
    public $message;
    public $from;
    public $customer_id;
    public $created_at;
   

    public static $definition = [
        'table' => 'tchat_plus',
        'primary' => 'id_tchat_plus',
        'multilang' => false,
        'fields' => [
            'message' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'size' => 4000, 'required' => true],
            'from' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 128,],
            'customer_id' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedInt', 'default' => null],
            'created_at' => ['type' => self::TYPE_STRING, 'validate' => 'isDate', 'required' => true],
            
        ],
    ];

    public function getMessagesBySender($sender) {
        $messages = []; // Initialize an array to store messages
    
        // Assuming you have a database connection $db, you can use it to fetch messages
        $sql = "SELECT * FROM "._DB_PREFIX_."tchat_plus WHERE `from` = '".pSQL($sender)."'";
        $result = Db::getInstance()->executeS($sql);
    
        // Loop through the result and add messages to the array
        foreach ($result as $row) {
            $message = [
                'message' => $row['message'],
                'created_at' => $row['created_at']
            ];
             // Check if customer_id is 0, then set it to from
            if ($row['customer_id'] == 0) {
                $message['from'] = $row['from'];
            } else {
                $customer = new CustomerCore($row['customer_id']);
                $firstName = $customer->firstname;
                $lastName = $customer->lastname;
                $fullName = $firstName . ' ' . $lastName;
                $message['from'] = $fullName;
            }

            $messages[] = $message;
        }
    
        return $messages;
    }

    
}