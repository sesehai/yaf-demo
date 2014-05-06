<?php
class IndexController extends Yaf_Controller_Abstract {

    /**
     * 构造函数
     * @param array $config
     */
    public function init()
    {
        $this->initParams();
        $this->_menu = $this->_initmenue();
    }

    /**
     * 初始化调用是的参数
     */
    public function initParams(){
        $request = $this->getRequest();
        $this->_menuid = $request->get('menuid', '');
    }

    private function _initmenue(){
        $result = array();
        $logMenuCount = new LogMenuModel();
        // active:0非选中，1选中
        // type: 0主菜单，1子菜单
        $leftMenu = $logMenuCount->getLeftMenu();

        foreach ($leftMenu as $menu) {
            if( $this->_menuid == $menu['id'] ){
                $menu['active'] = '1';
                $result['pathMenu'] = $menu;
            }
            $result['leftMenu'][$menu['order']] = $menu;
        }

        return $result;
    }

    public function indexAction() {
        $smarty = Yaf_Registry::get("smarty");

        $smarty->assign("menu", $this->_menu);
        $smarty->display('Loganalysis/index/index.phtml');
    }

}
