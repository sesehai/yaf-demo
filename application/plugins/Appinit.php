<?php
class AppinitPlugin extends Yaf_Plugin_Abstract {

    public function routerStartup(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {

    }

    public function routerShutdown(Yaf_Request_Abstract $request, Yaf_Response_Abstract $response) {
        $menuid = $request->get('menuid', '');
        $this->initTopMenu($menuid);
    }

    /**
     *  初始化导航菜单
     */
    private function initTopMenu($menuid){
        $topMenu = array(
            array(
                'id' =>'1',
                'name' => '首页',
                'url' => '/loganalysis/index/index',
                'active' => '0',
                'order' => '1',
            ),
            array(
                'id' =>'2',
                'name' => '实时监控',
                'url' => '/loganalysis/dynamic/index',
                'active' => '0',
                'order' => '2',
            ),
            array(
                'id' =>'3',
                'name' => '客户端日志',
                'url' => '/mob/errorfile/list',
                'active' => '0',
                'order' => '3',
            ),
        );
        foreach ($topMenu as $key=>$menu) {
            if( $menuid == $menu['id'] ){
                $menu['active'] = '1';
                $topMenu[$key] = $menu;
            }
        }

        $smarty = Yaf_Registry::get("smarty");
        $smarty->assign("topMenu", $topMenu);
    }

}