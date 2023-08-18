<?php
/**
 * AdminTchatPlusController.php
 *  @author    Samar Al khalil
 *  @copyright Copyright (c) Your Year
 *  @license   License (if applicable)
 *  @category  Controllers
 *  
 */
require_once(_PS_MODULE_DIR_.'tchatplus/classes/TchatPlusClass.php');


class AdminTchatplusController extends ModuleAdminController
{
    public function __construct()
    {
        
        $this->bootstrap = true;
        $this->table = TchatPlusClass::$definition['table'];
        $this->className = TchatPlusClass::class;
        $this->module = Module::getInstanceByName('tchatplus');
        $this->identifier = TchatPlusClass::$definition['primary'];
        $this->_orderBy = TchatPlusClass::$definition['primary'];
        $this->lang = false;
        $this->allow_export = true;
        $this->context = Context::getContext();
        $this->_select = '`a`.`from`, MAX(`a`.`created_at`) AS latest_message_time, `a`.`message`, `a`.`customer_id`, `ar`.`response` AS `admin_response`';
        $this->_join = 'LEFT JOIN `' . _DB_PREFIX_ . 'admin_responses` `ar` ON `a`.`from`=`ar`.`from_sender`';
        $this->_orderBy = 'latest_message_time';
        $this->_orderWay = 'DESC';

      
        

        parent::__construct();

        $this->token = Tools::getValue('token');
        
        
        // Define fields_list
        $this->fields_list = [
            'from' => [
                'title' => $this->l('Sender'),
                'filter_key' => 'a.`from`',
                'align' => 'center',
            ],
            'customer_id' => [
                'title' => $this->l('Sender\'s name'),
                'filter_key' => 'a.`customer_id`',
                'align' => 'center',
            ],
            'message' => [
                'title' => $this->l('Latest message'),
                'filter_key' => 'a.`message`',
            ],
            'admin_response' => [
                'title' => $this->l('Admin Response'),
                'filter_key' => 'ar.`response`',
            ],
            'latest_message_time' => [
                'title' => $this->l('Latest Message Time'),
                'filter_key' => 'latest_message_time',
                'align' => 'center',
            ],
        ];


        $this->addRowAction('view');
        $this->addRowAction('delete');

    }
   
    public function renderView()
    {
        $sql = new DbQuery();
        $sql->select('`from`, MAX(created_at) AS latest_message_time, message, customer_id')
            ->from($this->table)
            ->groupBy('`from`')
            ->orderBy('latest_message_time DESC');
        
        $sendersAndMessages = Db::getInstance()->executeS($sql);
        foreach ($sendersAndMessages as &$row) {
            if ($row['customer_id'] != 0) {
                $customer = new Customer($row['customer_id']);
                $row['customer_id'] = $customer->firstname . ' ' . $customer->lastname;
            }else{
                $row['customer_id'] = 'unknown';
            }
        }

        foreach ($sendersAndMessages as &$row) {
            $responses = $this->getAdminResponsesBySender($row['from']);
            $row['admin_responses'] = $responses;
        }
        
        $tplFile = _PS_MODULE_DIR_.'tchatplus/views/templates/admin/messageList.tpl';
        $tpl = $this->context->smarty->createTemplate($tplFile);
        $tpl->assign([
            'sendersAndMessages' => $sendersAndMessages,
            'backUrl' => $this->context->link->getAdminLink('AdminTchatPlus'),
        ]);
        return $tpl->fetch();
    }
    
    public function getAdminResponsesBySender($fromSender)
    {
        $responses = [];

        // Fetch administrator responses from the database for the given sender
        $sql = new DbQuery();
        $sql->select('*')
            ->from('admin_responses')
            ->where('from_sender = "'.pSQL($fromSender).'"')
            ->orderBy('created_at DESC')
            ->limit(1);
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

