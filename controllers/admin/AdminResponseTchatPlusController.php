<?php
/**
 * AdminResponseTchatPlusController.php
 *  @author    Samar Al khalil
 *  @copyright Copyright (c) Your Year
 *  @license   License (if applicable)
 *  @category  Controllers
 *  
 */
require_once(_PS_MODULE_DIR_.'tchatplus/classes/TchatPlusClass.php');

class AdminResponseTchatplusController extends ModuleAdminController
{
    public function __construct()
    {
        
        $this->bootstrap = true;
        $this->table = TchatPlusAdminClass::$definition['table'];
        $this->className = TchatPlusAdminClass::class;
        $this->module = Module::getInstanceByName('tchatplus');
        $this->identifier = TchatPlusAdminClass::$definition['primary'];
        $this->_orderBy = TchatPlusAdminClass::$definition['primary'];
        $this->lang = false;
        $this->allow_export = true;
        $this->context = Context::getContext();

        parent::__construct();

        $this->fields_list = [
            'id_tchat_plus' => [
                'title' => $this->l('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
            'message' => [
                'title' => $this->l('Titre'),
                'filter_key' => 'a!title',
            ],
            'from' => [
                'title' => $this->l('Description'),
                'filter_key' => 'a!description',
            ],
            'created_at' => [
                'title' => $this->l('Image'),
                'align' => 'center',
                'class' => 'fixed-width-xs',
            ],
        ];
        $this->addRowAction('view');
        $this->addRowAction('edit');
        $this->addRowAction('delete');

    }

}