<?php
class UserPlugin extends Yaf_Plugin_Abstract {

    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {

    }

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        $module = $request->getModuleName();
        // $controller = $request->getControllerName();
        // $action = $request->getActionName();
        if( $module == 'Mob' ){
            $smarty = Yaf_Registry::get("smarty");
        }else{
            //检测登陆
            $session = Yaf_Session::getInstance();
            $userInfo = $session->get('userInfo');
            if( empty($userInfo) || empty($userInfo['username'])){
                $request->setModuleName("Loganalysis");
                $request->setControllerName("User");
                $request->setActionName("login");
            }
            $smarty = Yaf_Registry::get("smarty");
            $smarty->assign("userInfo", $userInfo);
        }

    }

}