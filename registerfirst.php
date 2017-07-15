<?php

class registerfirst extends Module {

    public function __construct()
    {
        $this->name = 'registerfirst';
        $this->tab = 'front_office_features';
        $this->version = '0.1';
        $this->author = 'Manuel José Pulgar Anguita';
        $this->need_instance = 0;
        $this->ps_versions_compliancy = array('min' => '1.5');
    
        parent::__construct();
    
        $this->displayName = $this->l('Modulo de registrese antes de entrar');
        $this->description = $this->l('Módulo para importar los archivos extraídos de EXCEL con productos a la tienda Bilyfer');
    
        $this->confirmUninstall = $this->l('¿Seguro que lo quiere desinstalar?');
    }


    public function install() {
        $tab_installed = $this -> installTabs();
        $module_installed = parent::install();
        $hook_header_installed = $this->registerHook('displayHeader');
        $hook_user_registration_installed = $this->registerHook('actionCustomerAccountAdd');

        if ($tab_installed &&  $hook_header_installed 
                && $module_installed && $hook_user_registration_installed) {
            Configuration::updateValue('ALLOWED_CMS_REGISTERFIRST', '');
        }
        
        return  $tab_installed &&  $hook_header_installed 
                    && $module_installed && $hook_user_registration_installed;
    }


    public function uninstall() {
        Configuration::deleteByName('ALLOWED_CMS_REGISTERFIRST');
        return $this -> uninstallTabs() && $this->unregisterHook('displayHeader') && parent::uninstall();
    }
    private function installTabs() {


		// Install Tabs
		$parent_tab = new Tab();
		// Need a foreach for the language
        foreach (Language::getLanguages(true) as $lang)
		    $parent_tab->name[$lang['id_lang']] = $this->l('AdminUserManagement');
		$parent_tab->class_name = 'AdminUserManagement';
		$parent_tab->id_parent = 0; // Home tab
		$parent_tab->module = $this->name;
		$parent = $parent_tab->add();
		
		
		$tab = new Tab();		
		// Need a foreach for the language
        foreach (Language::getLanguages(true) as $lang)
		    $tab->name[$lang['id_lang']] = 'AdminUserManagement';
		$tab->class_name = 'AdminUserManagement';
		$tab->id_parent = $parent_tab->id;
		$tab->module = $this->name;
		$son = $tab->add();

        return $parent && $son;

    }


    private function uninstallTabs() {
        $result = true;
        $tab_list = Tab::getCollectionFromModule($this -> name);
        if (!empty($tab_list)) {
            foreach ($tab_list as $tab) {
                $result &= $tab -> delete();
            }
        }
        return $result;
    }
    

    private function isAllowedCMS() {
        // return false;

        $controller_name = Tools::getValue('controller');
        if ($controller_name == 'cms') {

            $passed_cms = Tools::getValue('id_cms');

            $cmsString = Configuration::get('ALLOWED_CMS_REGISTERFIRST');
            if (!empty($cmsString)){
                
                $cms_vector = explode(',', $cmsString);

                foreach ($cms_vector as $cms_id){
                    if ($cms_id == $passed_cms) {
                        return true;
                    }
                }

            }
        }
        return false;
        
    }

    private function isSignedUp() {
        return !empty($this->context->customer->id);
    }

    public function hookDisplayHeader() {
        // die ("holiiiii");
        $controller_name = Tools::getValue('controller');
        // die($controller_name);
        if ((!$this -> isSignedUp())
                && ($controller_name != 'authentication') 
                && ($controller_name != 'contact')
                && (!$this -> isAllowedCMS())) {

                    $link = $this->context->link;

                    $authenticationURL = $link -> getPageLink('authentication');
                    Tools::redirect($authenticationURL);
        }
    }

    public function hookActionCustomerAccountAdd($params){
        /*
            Hook::exec('actionCustomerAccountAdd', array(
                        '_POST' => $_POST,
                        'newCustomer' => $customer
                    ));
        */
        $customer = $params['newCustomer'];
        $customer -> active = false;
        $customer -> save();
    }


    public function renderForm()

	{
		
		$fields_form = array(

			'form' => array(

				'legend' => array(

					'title' => $this->l('Settings'),

					'icon' => 'icon-cogs'

				),

				'description' => $this->l('Please, enter the allowed CMS, so the customer can enter there without registering').'<br/><br/>'.

						$this->l('NOTE: the customer will also be allowed to enter the contact form without registering').'<br/>'.

						$this->l('The CMS id list must be coma separated.'),

				'input' => array(

					array(

						'type' => 'text',

						'label' => $this->l('Allowed CMS list'),

						'name' => 'cms_id_list',

                        'desc' => $this->l('Enter a coma separated id CMS list.'),

						'hint' => $this->l('Enter a coma separated id CMS list.'),

					),
					
				),

				'submit' => array(

					'title' => $this->l('Save'),

				)

			),

		);



		$helper = new HelperForm();

		$helper->show_toolbar = false;

		$helper->table =  $this->table;

		$lang = new Language((int)Configuration::get('PS_LANG_DEFAULT'));

		$helper->default_form_language = $lang->id;

		$helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;

		$this->fields_form = array();



		$helper->identifier = $this->identifier;

		$helper->submit_action = 'submitModule';

		$helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false).'&configure='.$this->name.'&tab_module='.$this->tab.'&module_name='.$this->name;

		$helper->token = Tools::getAdminTokenLite('AdminModules');

		
		$config_values = $this->getConfigFieldsValues();
		
		
		if (!Tools::isSubmit('submitModule'))
		
		{
			if (empty($config_values['callmeplease_admin_folder'])){
				$default_admin_folder =  _PS_ADMIN_DIR_;
				$last_backslash_position = strrpos ( $default_admin_folder , "/");
				$default_admin_folder = substr($default_admin_folder, $last_backslash_position+1);
				$config_values['callmeplease_admin_folder'] = $default_admin_folder;
			}
			
		}
		
		
		$helper->tpl_vars = array(

			'fields_value' => $config_values,

			'languages' => $this->context->controller->getLanguages(),

			'id_language' => $this->context->language->id

		);
		return $helper->generateForm(array($fields_form));
	}


}