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
        return $this -> installTabs() && parent::install();
    }


    public function uninstall() {
        return $this -> uninstallTabs() && parent::uninstall();
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


}