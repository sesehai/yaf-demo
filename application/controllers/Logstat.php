<?php
class LogstatController extends Yaf_Controller_Abstract {
    public function indexAction() {//默认Action
    	$startDate = "2013-07-25";
    	$endDate = "2013-07-31";
    	$ip = "115.182.93.109";
        $logstat = new LogstatModel();
        $list = $logstat->get_list_by_daterange($startDate, $endDate, $ip);
        $this->getView()->assign("list", $list);
        $this->getView()->assign("content", "logstat");
    }
}