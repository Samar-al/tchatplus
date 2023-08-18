<?php
/**
 * TchatPlusAdminClass.php
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
class TchatPlusAdminClass extends ObjectModel
{
    public $id_admin_response;
    public $response;
    public $from_sender;
    public $created_at;
   

    public static $definition = [
        'table' => 'admin_responses',
        'primary' => 'id_admin_response',
        'multilang' => false,
        'fields' => [
            'from_sender' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 128],
            'response' => ['type' => self::TYPE_STRING, 'validate' => 'isCleanHtml', 'required' => true, 'size' => 4000],
            'created_at' => ['type' => self::TYPE_DATE, 'validate' => 'isDate', 'required' => true],
        ],
    ];

    public function getAdminResponsesBySender($fromSender)
    {
        $responses = [];

        // Fetch administrator responses from the database for the given sender
        $sql = new DbQuery();
        $sql->select('*')
            ->from('admin_responses')
            ->where('from_sender = "'.pSQL($fromSender).'"')
            ->orderBy('created_at ASC');
        $responseRows = Db::getInstance()->executeS($sql);
        
        foreach ($responseRows as $row) {
            $responses[] = [
                'response' => $row['response'],
                'created_at' => $row['created_at'],
            ];
        }
        return $responses;
    }
}