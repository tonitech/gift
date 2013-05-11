<?php
class IndexController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
    }

    public function indexAction()
    {
        $this->view->title = 'Teaing.com';
    }
    
    public function testAction()
    {
        $this->view->title = 'Test';
    }
    
    public function testresultAction()
    {
        $symptomList = $this->_request->getParam('symptom');
        $type = $this->_request->getParam('type');
        $symptomList = explode(',', $symptomList);
        if (isset($symptomList) && $type == 'symptom') {
            $recommendObj = Business_Recommend::factory($type);
            $max = 1;
            $symptom = '';
            $symptomMap = Zend_Registry::get('config')->symptom;
            foreach ($symptomList as $key => $value) {
                if ($value > $max) {
                    $max = $value;
                    $which = 's'.($key + 1);
                    $symptom = $symptomMap->$which;
                }
            }
            $recommendObj->setSymptom($symptom);
            $result = $recommendObj->getGoodsList();
            if ($result == null) {
                $result = array(
                    'errorcode' => -1,
                    'errormsg' => 'no recommendation'
                );
            } else {
                $result = array(
                    'errorcode' => 0,
                    'errormsg' => 'success',
                    'result' => $result
                );
            }
        } else {
            $result = array(
                'errorcode' => -1,
                'errormsg' => 'no type'
            );
        }
        $this->view->result = $result;
    }
}