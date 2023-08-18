<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 */



 if(!defined('_PS_VERSION_')) {
    exit;
}
class TchatPlus extends Module
{
    public function __construct()
    {
        $this->name ='tchatplus';

        $this->tab = 'front_office_features';
        $this->version = '1.0.0';
        $this->author = 'samar Al khalil';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = [
            'min' => '1.6',
            'max' => _PS_VERSION_
        ];

        parent::__construct();
        
        $this->bootstrap = true;
        $this->displayName = $this->l('Tchat Plus');
        $this->description = $this->l('tchat permettant au visiteur d\'échanger avec le marchand');
        $this->confirmUninstall = $this->l('Êtes-vous sur de vouloir supprimer ce module');
    }

    public function install()
    {
        if (!parent::install() ||
            !$this->createTchatTable() ||
            !$this->createAdminResponsesTable() ||
            !$this->registerHook('displayFooterAfter') ||
            !$this->registerHook('header') ||
            !$this->registerHook('displayAdminEndContent') ||
            !$this->registerHook('actionAdminControllerSetMedia') ||
            !$this->installTab('AdminTchatPlus', 'Tchat', 'IMPROVE') ||
            !Configuration::updateValue('TCHAT_ACTIVATION', true) ||
            !Configuration::updateValue('HOURS_OPEN_FROM', '09:00') ||
            !Configuration::updateValue('HOURS_OPEN_TO', '18:00') ||
            !Configuration::updateValue('TIME_ZONE', 'Europe/Paris') ||
            !Configuration::updateValue('CHAT_POSITION', 'right') ||
            !Configuration::updateValue('HEADER_TEXT', 'Bienvenue dans le chat') ||
            !Configuration::updateValue('WELCOME_TEXT', 'Comment pouvons-nous vous aider aujourd\'hui ?') ||
            !Configuration::updateValue('MAIN_COLOR', '#99a9ff') ||
            !Configuration::updateValue('BACKGROUND_COLOR', '#ffffff') ||
            !Configuration::updateValue('ADVISOR_COLOR', '#1d92a6') ||
            !Configuration::updateValue('CLIENT_COLOR', '#555555') ||
            !Configuration::updateValue('OFFLINE_MODE', false) ||
            !Configuration::updateValue('NOTIFICATION_EMAIL', 'contact@example.com') ||
            !Configuration::updateValue('OFFLINE_HEADER_TEXT', 'Nous sommes actuellement hors ligne',
            ) ||
            !Configuration::updateValue('OFFLINE_WELCOME_TEXT', 'Veuillez laisser votre message et vos coordonnées.',
            ) ||
            !Configuration::updateValue('OFFLINE_HEADER_COLOR', '#00f51e') ||
            !Configuration::updateValue('OFFLINE_FIELDS_NAME', false) ||
            !Configuration::updateValue('OFFLINE_FIELDS_FIRSTNAME', false) ||
            !Configuration::updateValue('OFFLINE_FIELDS_PHONE', false) ||
            !Configuration::updateValue('OFFLINE_FIELDS_EMAIL', false) ||
            !Configuration::updateValue('OFFLINE_FIELDS_MESSAGE', false)
            
            
            
        ) {
            return false;
        }

        return true;
   
    }

    public function uninstall()
    {
        if (!parent::uninstall() ||
            !$this->removeTchatTable() ||
            !$this->revomeAdminResponsesTable() ||
            !$this->unregisterHook('displayFooterAfter') ||
            !$this->unregisterHook('header') || 
            !$this->unregisterHook('displayAdminEndContent') || 
            !$this->unregisterHook('actionAdminControllerSetMedia') || 
            !$this->uninstallTab('AdminTchatPlus') ||
            !Configuration::deleteByName('TCHAT_ACTIVATION') ||
            !Configuration::deleteByName('HOURS_OPEN_FROM') ||
            !Configuration::deleteByName('HOURS_OPEN_FROM') ||
            !Configuration::deleteByName('HOURS_OPEN_TO') ||
            !Configuration::deleteByName('TIME_ZONE') ||
            !Configuration::deleteByName('CHAT_POSITION') ||
            !Configuration::deleteByName('HEADER_TEXT') ||
            !Configuration::deleteByName('WELCOME_TEXT') ||
            !Configuration::deleteByName('MAIN_COLOR') ||
            !Configuration::deleteByName('BACKGROUND_COLOR') ||
            !Configuration::deleteByName('ADVISOR_COLOR') ||
            !Configuration::deleteByName('CLIENT_COLOR') ||
            !Configuration::deleteByName('OFFLINE_MODE') ||
            !Configuration::deleteByName('NOTIFICATION_EMAIL') ||
            !Configuration::deleteByName('OFFLINE_HEADER_TEXT') ||
            !Configuration::deleteByName('OFFLINE_WELCOME_TEXT') ||
            !Configuration::deleteByName('OFFLINE_HEADER_COLOR') ||
            !Configuration::deleteByName('OFFLINE_FIELDS_NAME') ||
            !Configuration::deleteByName('OFFLINE_FIELDS_FIRSTNAME') ||
            !Configuration::deleteByName('OFFLINE_FIELDS_EMAIL') ||
            !Configuration::deleteByName('OFFLINE_FIELDS_MESSAGE')
            
        ) {
            return false;
        }

        return true;
    }

    public function enable($force_all = false)
        {
            return parent::enable($force_all)
                && $this->installTab('AdminTchatPlus', 'Tchat', 'IMPROVE');
               
            
        }

        public function disable($force_all = false){
            return parent::disable($force_all)
                && $this->uninstallTab('AdminTchatPlus');
            
        }

    public function getContent(){
        $output = $this->renderForm();
        $output .= $this->postProcess();
        return $output;
    }

    public function createTchatTable()
    {
        return Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'tchat_plus` (
            `id_tchat_plus` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `message` TEXT NOT NULL,
            `from` VARCHAR(255) NOT NULL,
            `customer_id` INT UNSIGNED DEFAULT NULL,  -- New column
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_tchat_plus`)
        )');
    }

    public function createAdminResponsesTable()
    {
        return Db::getInstance()->execute('CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'admin_responses` (
            `id_admin_response` INT UNSIGNED NOT NULL AUTO_INCREMENT,
            `from_sender` VARCHAR(255) NOT NULL,
            `response` TEXT NOT NULL,
            `created_at` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (`id_admin_response`)
        )');
    }

    
    public function removeTchatTable(){
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'tchat_plus`');
    }

    public function revomeAdminResponsesTable(){
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'admin_responses`');
    }
    private function installTab($className, $tabName, $tabParentName){
        $tabId = (int) Tab::getIdFromClassName($className);
        if (!$tabId) {
            $tabId = null;
        }

        $tab = new Tab($tabId);
        $tab->active = 1;
        $tab->class_name = $className;
        // Only since 1.7.7, you can define a route name
        $tab->name = array();
        foreach (Language::getLanguages() as $lang) {
            $tab->name[$lang['id_lang']] = $this->trans($tabName, array(), 'Modules.MyModule.Admin', $lang['locale']);
        }
        $tab->id_parent = (int) Tab::getIdFromClassName($tabParentName);
        $tab->module = $this->name;

        return $tab->save();
    }

    private function uninstallTab($className){
        $tabId = (int) Tab::getIdFromClassName($className);
        if (!$tabId) {
            return true;
        }

        $tab = new Tab($tabId);

        return $tab->delete();
    }

    private function generateTimeOptions() {
        $options = [];
        
        for ($hour = 0; $hour <= 23; $hour++) {
            for ($minute = 0; $minute <= 45; $minute += 15) {
                $time = sprintf('%02d:%02d', $hour, $minute);
                $options[] = ['value' => $time, 'label' => $time];
            }
        }
        
        return $options;
    }
    
    private function generateTimezoneOptions() {
        $options = [];
    
        foreach (DateTimeZone::listIdentifiers() as $timezone) {
            $options[] = ['timezone' => $timezone];
        }
    
        return $options;
    }
    
    

    protected function renderForm()
    {
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $fields_form = [
            'form' => [
                'legend' => [
                    'title' => $this->l('TchatPlus Configuration'),
                    'icon' => 'icon-cogs',
                ],
                'input' => [
                    [
                        'type' => 'switch',
                        'label' => $this->l('Enable Tchat'),
                        'name' => 'TCHAT_ACTIVATION',
                        'is_bool' => true,
                        'desc' => $this->l('Enable or disable the tchat feature.'),
                        'values' => [
                            [
                                'id' => 'active_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ],
                            [
                                'id' => 'active_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            ]
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Timezone'),
                        'name' => 'TIME_ZONE',
                        'desc' => $this->l('Select the timezone for the chat.'),
                        'options' => [
                            'query' => $this->generateTimezoneOptions(),
                            'id' => 'timezone',
                            'name' => 'timezone',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Open From'),
                        'name' => 'HOURS_OPEN_FROM',
                        'desc' => $this->l('Select the opening time for the chat.'),
                        'options' => [
                            'query' => $this->generateTimeOptions(),
                            'id' => 'value',
                            'name' => 'label',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Open To'),
                        'name' => 'HOURS_OPEN_TO',
                        'desc' => $this->l('Enter the closing time for the chat.'),
                        'options' => [
                            'query' => $this->generateTimeOptions(),
                            'id' => 'value',
                            'name' => 'label',
                        ],
                    ],
                    [
                        'type' => 'select',
                        'label' => $this->l('Chat Position'),
                        'name' => 'CHAT_POSITION',
                        'desc' => $this->l('Select the chat position.'),
                        'options' => [
                            'query' => [
                                [
                                    'id' => 'left',
                                    'value' => 'left',
                                    'label' => $this->l('Left')
                                ],
                                [
                                    'id' => 'right',
                                    'value' => 'right',
                                    'label' => $this->l('Right')
                                ]
                            ],
                            'id' => 'id',
                            'name' => 'label'
                        ],
                    ],       
                    [
                        'type' => 'text',
                        'label' => $this->l('Header Text'),
                        'name' => 'HEADER_TEXT',
                        'desc' => $this->l('Enter the header text.'),
                        'autoload_rte' =>true,
                        'lang' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Welcome Text'),
                        'name' => 'WELCOME_TEXT',
                        'desc' => $this->l('Enter the welcome text.'),
                        'lang' => true,
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Main Color'),
                        'name' => 'MAIN_COLOR',
                        'desc' => $this->l('Select the main color.'),
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Background Color'),
                        'name' => 'BACKGROUND_COLOR',
                        'desc' => $this->l('Select the background color.'),
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Advisor Color'),
                        'name' => 'ADVISOR_COLOR',
                        'desc' => $this->l('Select the advisor color.'),
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Client Color'),
                        'name' => 'CLIENT_COLOR',
                        'desc' => $this->l('Select the client color.'),
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Offline Mode'),
                        'name' => 'OFFLINE_MODE',
                        'is_bool' => true,
                        'desc' => $this->l('Enable or disable offline mode.'),
                        'values' => [
                            [
                                'id' => 'offline_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ],
                            [
                                'id' => 'offline_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            ]
                        ],
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Notification Email'),
                        'name' => 'NOTIFICATION_EMAIL',
                        'desc' => $this->l('Enter the notification email address.'),
                        'autoload_rte' =>true,
                      
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Offline Header Text'),
                        'name' => 'OFFLINE_HEADER_TEXT',
                        'desc' => $this->l('Enter the offline header text.'),
                        'lang' => true,
                    ],
                    [
                        'type' => 'text',
                        'label' => $this->l('Offline Welcome Text'),
                        'name' => 'OFFLINE_WELCOME_TEXT',
                        'desc' => $this->l('Enter the offline welcome text.'),
                        'lang' => true,
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Offline Fields: Name'),
                        'name' => 'OFFLINE_FIELDS_NAME',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'name_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ],
                            [
                                'id' => 'name_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            ]
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Offline Fields: First Name'),
                        'name' => 'OFFLINE_FIELDS_FIRSTNAME',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'firstname_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ],
                            [
                                'id' => 'firstname_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            ]
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Offline Fields: Phone'),
                        'name' => 'OFFLINE_FIELDS_PHONE',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'phone_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ],
                            [
                                'id' => 'phone_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            ]
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Offline Fields: Email'),
                        'name' => 'OFFLINE_FIELDS_EMAIL',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'email_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ],
                            [
                                'id' => 'email_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            ]
                        ],
                    ],
                    [
                        'type' => 'switch',
                        'label' => $this->l('Offline Fields: Message'),
                        'name' => 'OFFLINE_FIELDS_MESSAGE',
                        'is_bool' => true,
                        'values' => [
                            [
                                'id' => 'message_on',
                                'value' => 1,
                                'label' => $this->l('Enabled')
                            ],
                            [
                                'id' => 'message_off',
                                'value' => 0,
                                'label' => $this->l('Disabled')
                            ]
                        ],
                    ],
                    [
                        'type' => 'color',
                        'label' => $this->l('Offline Header Color'),
                        'name' => 'OFFLINE_HEADER_COLOR',
                        'desc' => $this->l('Select the offline header color.'),
                    ],
                    
                ],
                'submit' => [
                    'title' => $this->l('Save'),
                    'class' => 'btn btn-primary',
                    'name' => 'Save',
                ],
                'enctype' => 'multipart/form-data',
            ],
        ];
        

        $helper = new HelperForm();
        $helper->module = $this;
        $helper->name_controller = $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        foreach (Language::getLanguages(false) as $lang) {
            $helper->languages[] = [
                'id_lang' => $lang['id_lang'],
                'iso_code' => $lang['iso_code'],
                'name' => $lang['name'],
                'is_default' => ($default_lang == $lang['id_lang'] ? 1 : 0),
            ];
        }
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->default_form_language = (int)Configuration::get('PS_LANG_DEFAULT');
        $helper->allow_employee_form_lang = (int)Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->title = $this->displayName;
        $helper->show_toolbar = true;
        $helper->toolbar_scroll = true;
        $helper->submit_action = 'submit'.$this->name;
       
        // Load current values for the form fields
       
        foreach ($fields_form['form']['input'] as &$field) {
            if (isset($field['lang']) && $field['lang']) {
                foreach (Language::getLanguages(true) as $lang) {
                  
                $helper->fields_value[$field['name']][$lang['id_lang']] = Configuration::get($field['name'].'_'.$lang['id_lang'], null, $lang['id_lang']);
                }
            } else { 
                
                $helper->fields_value[$field['name']]  = Configuration::get($field['name'], $default_lang);
            }
        }

        return $helper->generateForm([$fields_form]);
    }

    public function postProcess(){
        if(Tools::isSubmit('Save')){
           /*  if (empty(Tools::getValue('HEADER_TEXT_'.Context::getContext()->language->id)) || 
                empty(Tools::getValue('WELCOME_TEXT_'.Context::getContext()->language->id)) || 
                empty(Tools::getValue('OFFLINE_HEADER_TEXT'.Context::getContext()->language->id)) || 
                empty(Tools::getValue('OFFLINE_WELCOME_TEXT_'.Context::getContext()->language->id))){

                return $this->displayError('Une valeur est vide');
            }else{ */

                foreach(Language::getLanguages(false) as $lang){
                    
                   
                    Configuration::updateValue('HEADER_TEXT_'.$lang['id_lang'], Tools::getValue('HEADER_TEXT_'.$lang['id_lang']), true);
                    Configuration::updateValue('WELCOME_TEXT_'.$lang['id_lang'], Tools::getValue('WELCOME_TEXT_'.$lang['id_lang']));
                    Configuration::updateValue('OFFLINE_HEADER_TEXT_'.$lang['id_lang'], Tools::getValue('OFFLINE_HEADER_TEXT_'.$lang['id_lang']));
                    Configuration::updateValue('OFFLINE_WELCOME_TEXT_'.$lang['id_lang'], Tools::getValue('OFFLINE_WELCOME_TEXT_'.$lang['id_lang']));
                    
                }

                $selectedTimezone = Tools::getValue('TIME_ZONE');
                $openFrom = Tools::getValue('HOURS_OPEN_FROM');
                $openTo = Tools::getValue('HOURS_OPEN_TO');

                // Convert opening and closing times to server's default timezone
                $serverTimezone = new DateTimeZone(date_default_timezone_get());
                $selectedTimezoneObj = new DateTimeZone($selectedTimezone);

                $dateTimeOpenFrom = new DateTime("today $openFrom", $selectedTimezoneObj);
                $dateTimeOpenFrom->setTimezone($serverTimezone);
                $openFromConverted = $dateTimeOpenFrom->format('H:i');

                $dateTimeOpenTo = new DateTime("today $openTo", $selectedTimezoneObj);
                $dateTimeOpenTo->setTimezone($serverTimezone);
                $openToConverted = $dateTimeOpenTo->format('H:i');


                Configuration::updateValue('TCHAT_ACTIVATION', Tools::getValue('TCHAT_ACTIVATION'));
                Configuration::updateValue('HOURS_OPEN_FROM', $openFromConverted);
                Configuration::updateValue('HOURS_OPEN_TO', $openToConverted);
                Configuration::updateValue('TIME_ZONE', Tools::getValue('TIME_ZONE'));
                Configuration::updateValue('CHAT_POSITION', Tools::getValue('CHAT_POSITION'));
                Configuration::updateValue('MAIN_COLOR', Tools::getValue('MAIN_COLOR'));
                Configuration::updateValue('BACKGROUND_COLOR', Tools::getValue('BACKGROUND_COLOR'));
                Configuration::updateValue('ADVISOR_COLOR', Tools::getValue('ADVISOR_COLOR'));
                Configuration::updateValue('CLIENT_COLOR', Tools::getValue('CLIENT_COLOR'));
                Configuration::updateValue('OFFLINE_MODE', Tools::getValue('OFFLINE_MODE'));
                Configuration::updateValue('NOTIFICATION_EMAIL', Tools::getValue('NOTIFICATION_EMAIL'));
                Configuration::updateValue('OFFLINE_FIELDS_NAME', Tools::getValue('OFFLINE_FIELDS_NAME'));
                Configuration::updateValue('OFFLINE_FIELDS_FIRSTNAME', Tools::getValue('OFFLINE_FIELDS_FIRSTNAME'));
                Configuration::updateValue('OFFLINE_FIELDS_PHONE', Tools::getValue('OFFLINE_FIELDS_PHONE'));
                Configuration::updateValue('OFFLINE_FIELDS_EMAIL', Tools::getValue('OFFLINE_FIELDS_EMAIL'));
                Configuration::updateValue('OFFLINE_FIELDS_MESSAGE', Tools::getValue('OFFLINE_FIELDS_MESSAGE'));
                Configuration::updateValue('OFFLINE_HEADER_COLOR', Tools::getValue('OFFLINE_HEADER_COLOR'));
                return $this->displayConfirmation('Sauvegarde réussi');
          //}
        }
    } 

   

    public function hookDisplayFooterAfter() {
       
        if(Configuration::get('TCHAT_ACTIVATION')== 1){

            if (Configuration::get('OFFLINE_MODE')==0) {
                $this->context->smarty->assign([
                    'offline' => Configuration::get('OFFLINE_MODE'),
                    'main_color' => Configuration::get('MAIN_COLOR'),
                    'background_color' => Configuration::get('BACKGROUND_COLOR'),
                    'advisor_color' => Configuration::get('ADVISOR_COLOR'),
                    'client_color' => Configuration::get('CLIENT_COLOR'),
                    'header_text' =>Configuration::get('HEADER_TEXT_'.Context::getContext()->language->id),
                    'welcome_text' => Configuration::get('WELCOME_TEXT_'.Context::getContext()->language->id),
                ]);
            }else{
                $this->context->smarty->assign([
                    'offline' => Configuration::get('OFFLINE_MODE'),
                    'main_color' => Configuration::get('OFFLINE_HEADER_COLOR'),
                    'background_color' => Configuration::get('BACKGROUND_COLOR'),
                    'header_text' => Configuration::get('OFFLINE_HEADER_TEXT_'.Context::getContext()->language->id),
                    'welcome_text' => Configuration::get('OFFLINE_WELCOME_TEXT_'.Context::getContext()->language->id),
                    'email_notification' => Configuration::get('NOTIFICATION_EMAIL'),
                    'name' => Configuration::get('OFFLINE_FIELDS_NAME'),
                    'firstname' => Configuration::get('OFFLINE_FIELDS_FIRSTNAME'),
                    'phone' => Configuration::get('OFFLINE_FIELDS_PHONE'),
                    'email' => Configuration::get('OFFLINE_FIELDS_EMAIL'),
                    'message' => Configuration::get('OFFLINE_FIELDS_MESSAGE'),
                    'client_color' => Configuration::get('CLIENT_COLOR'),    
                ]);
            }
            $this->context->smarty->assign([
                'visitor' => Tools::getRemoteAddr(),
                'client_color' => Configuration::get('CLIENT_COLOR'),
                'opening_hours' => Configuration::get('HOURS_OPEN_FROM'),
                'advisor_color' => Configuration::get('ADVISOR_COLOR'),
                'closing_hours' => Configuration::get('HOURS_OPEN_TO'),
                'time_zone' => Configuration::get('TIME_ZONE'),
                'chat_position' => Configuration::get('CHAT_POSITION'),
                'isBackOffice' => (Tools::getValue('controller') == 'AdminTchatplus'),
            ]);
            
            return $this->display(__FILE__, 'views/templates/hooks/chat.tpl');
        }
        return "";
       
    }

    public function hookHeader(){
        if (!$this->context->controller instanceof AdminController) {
            Media::addJsDef([
                'front_ajax'=> $this->_path.'/chat.php',
                'back_ajax' => $this->_path.'/response.php'
            ]);
            $this->context->controller->addCSS($this->_path . 'views/css/chat.css');
            $this->context->controller->addJS($this->_path . 'views/js/chat.js');
        }
    }

   

    public function hookDisplayAdminEndContent() {
        $currentController = Tools::getValue('controller');
        $currentView = Tools::getValue('viewtchat_plus');
        if ($currentController === 'AdminTchatPlus' && $currentView === '') {
            if(Configuration::get('TCHAT_ACTIVATION')== 1){

                if (Configuration::get('OFFLINE_MODE')==0) {
                    $this->context->smarty->assign([
                        'offline' => Configuration::get('OFFLINE_MODE'),
                        'main_color' => Configuration::get('MAIN_COLOR'),
                        'background_color' => Configuration::get('BACKGROUND_COLOR'),
                        'advisor_color' => Configuration::get('ADVISOR_COLOR'),
                        'client_color' => Configuration::get('CLIENT_COLOR'),
                        'header_text' =>Configuration::get('HEADER_TEXT_'.Context::getContext()->language->id),
                        'welcome_text' => Configuration::get('WELCOME_TEXT_'.Context::getContext()->language->id),
                    ]);
                }else{
                    $this->context->smarty->assign([
                        'offline' => Configuration::get('OFFLINE_MODE'),
                        'main_color' => Configuration::get('OFFLINE_HEADER_COLOR'),
                        'background_color' => Configuration::get('BACKGROUND_COLOR'),
                        'header_text' => Configuration::get('OFFLINE_HEADER_TEXT_'.Context::getContext()->language->id),
                        'welcome_text' => Configuration::get('OFFLINE_WELCOME_TEXT_'.Context::getContext()->language->id),
                        'email_notification' => Configuration::get('NOTIFICATION_EMAIL'),
                        'name' => Configuration::get('OFFLINE_FIELDS_NAME'),
                        'firstname' => Configuration::get('OFFLINE_FIELDS_FIRSTNAME'),
                        'phone' => Configuration::get('OFFLINE_FIELDS_PHONE'),
                        'email' => Configuration::get('OFFLINE_FIELDS_EMAIL'),
                        'message' => Configuration::get('OFFLINE_FIELDS_MESSAGE'),
                        'advisor_color' => Configuration::get('ADVISOR_COLOR'),
                        'client_color' => Configuration::get('CLIENT_COLOR'),    
                    ]);
                }
                $this->context->smarty->assign([
                    'email_notification' => Configuration::get('NOTIFICATION_EMAIL'),
                    'opening_hours' => Configuration::get('HOURS_OPEN_FROM'),
                    'closing_hours' => Configuration::get('HOURS_OPEN_TO'),
                    'time_zone' => Configuration::get('TIME_ZONE'),
                    'chat_position' => Configuration::get('CHAT_POSITION'),
                    'isBackOffice' => $this->context->controller instanceof AdminController,
                    'advisor_color' => Configuration::get('ADVISOR_COLOR'),
                    'client_color' => Configuration::get('CLIENT_COLOR'),
                    
                ]);
                
                return $this->display(__FILE__, 'views/templates/hooks/chat.tpl');
            }
            return "";
        }
    }

    public function hookActionAdminControllerSetMedia($params) {
        // Get the current controller and view names from URL parameters
        $currentController = Tools::getValue('controller');
        $currentView = Tools::getValue('viewtchat_plus');
    
        // Check if the current controller and view match your target
        if ($currentController === 'AdminTchatPlus' && $currentView === '') {
            // Your hook's code here
            Media::addJsDef([
                'front_ajax' => $this->_path . '/chat.php',
                'back_ajax' => $this->_path . '/response.php'
            ]);
            $this->context->controller->addCSS($this->_path . 'views/css/chat.css');
            $this->context->controller->addJS($this->_path . 'views/js/chat.js');
        }
    }
    
}