<?php
/*
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
*
*  @author Manuel José Pulgar Anguita <contact@prestashop.com>
*  @copyright  2017 Manuel José Pulgar Anguita
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*/

/**
 * @since 1.6.0
 */
class AdminUserManagementController extends ModuleAdminController
{

    public function __construct()
    {
        $this->bootstrap = true;
        $this->display = 'list';
        $this->meta_title = $this->l('Listado de usuarios');
        parent::__construct();
        if (!$this->module->active)
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminHome'));
        
        
    }

    public function renderList(){

        $this->toolbar_title[] = $this->l('Listado de usuarios');
        
        $this -> fields_listfields_list = array(
            'orderId' => array(
                'title' => $this->l('Id'),
                //'width' => 140,
                'type' => 'text'
            ),
            'status' => array(
                'title' => $this->l('Nombre de usuario'),
                //'width' => 140,
                'type' => 'int'
            ),
            'estimatedTime' => array(
                'title' => $this->l('Email'),
                //'width' => 140,
                'type' => 'text'
            ),
            'pickuptime' => array(
                'title' => $this->l('Estado'),
                //'width' => 140,
                'type' => 'text'
            ),
            'updated' => array(
                'title' => $this->l('Otros? Borrar?'),
                //'width' => 140,
                'type' => 'text'
            ),
        );
        
        $helper = new HelperList();
        
        $helper->shopLinkType = '';

        $helper->simple_header = false;
        
        // Actions to be displayed in the "Actions" column
        $helper->actions = array('edit', 'delete', 'view');

        $helper->identifier = 'id';
        $helper->show_toolbar = true;
        $helper->title = $this->l('All jobs');
        $helper->specificConfirmDelete = true;
        
  
        $helper->token = $this->context->controller->token;

        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->module->name;

        return parent::renderList();
    }

}
