<?php
/**
  * @desc
  */
class IndexController extends View_Helper
{
    public function indexAction()
    {
        $this->view->title = 'Gift分享';
        $this->getLoginUserInfoView();
        
        $homepageObj = new Business_Manage_Homepage();
        $sliderTable = $homepageObj->getHomepageSliderTableInfo();
        $slider = $homepageObj->getHomepageSlider();
        
        $this->view->slider = $slider;
        $this->view->sliderTable = $sliderTable;
    }
}
