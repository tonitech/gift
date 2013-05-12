<?php
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->title = 'Gift分享';
        $homepageObj = new Business_Manage_Homepage();
        $sliderTable = $homepageObj->getHomepageSliderTableInfo();
        $slider = $homepageObj->getHomepageSlider();
        $modulesTable = $homepageObj->getHomepageModulesTableInfo();
        $modules = $homepageObj->getHomepageModules();
        
        $this->view->slider = $slider;
        $this->view->modules = $modules;
        $this->view->sliderTable = $sliderTable;
        $this->view->modulesTable = $modulesTable;
    }
}