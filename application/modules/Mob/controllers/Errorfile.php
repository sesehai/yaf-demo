<?php
class ErrorfileController extends Yaf_Controller_Abstract {
    /**
     * 构造函数
     * @param array $config
     */
    public function init()
    {
        $this->_checkUser();
        $this->initParams();
        $this->_menu = $this->_initmenue();
    }

    /**
     * 初始化调用是的参数
     */
    public function initParams(){
        $request = $this->getRequest();
        $this->_menuid = $request->get('menuid', '');
        $this->_type = $request->get('type', '');
    }

    private function _initmenue(){
        $result = array();
        $logMenuCount = new LogMenuModel();
        // active:0非选中，1选中
        // type: 0主菜单，1子菜单
        $leftMenu = $logMenuCount->getLeftMenuMobErrorFile();

        foreach ($leftMenu as $menu) {
            if( $this->_menuid == $menu['id'] ){
                $menu['active'] = '1';
                $result['pathMenu'] = $menu;
            }
            $result['leftMenu'][$menu['order']] = $menu;
        }

        return $result;
    }

    public function listAction() {
        $offset = 10;
        $params['mobile'] = isset($_GET['mobile']) ? $_GET['mobile'] : '';
        $params['page'] = isset($_GET['page']) ? $_GET['page'] : 1;

        $smarty = Yaf_Registry::get("smarty");
        $mclientErrorFile = new MclientErrorFileModel();

        $condition = "";
        if(!empty($params['mobile'])){
            $condition = " `mobile` = '".$params['mobile']."' ";
        }

        $count = $mclientErrorFile->getAllCount($condition);
        $rows = $mclientErrorFile->getAllByPage($params['page'], $offset, $condition);
        
        foreach ($rows as $k=>$row) {
            $url = explode(" ",$row['line']);
            $len = count($url);
            $rows[$k]['url'] = "http://".trim($url[$len - 2],'"').$url[6];
        }

        $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['PATH_INFO']."?&date=".$params['date'];
        if(!empty($condition)){
            $url .= "&mobile=".urlencode($params["mobile"]);
        }
        $pagecount = $count/$offset;
        $pagenav = Util::paginate($url, $params['page'], ceil($pagecount));

        $smarty->assign("result", $rows);
        $smarty->assign("start", ($params['page'] -1)*$offset );
        $smarty->assign("end", $params['page']*$offset );
        $smarty->assign("count", $count);
        $smarty->assign("params", $params);
        $smarty->assign("menu", $this->_menu);
        $smarty->assign("pagenav",$pagenav);
        $smarty->display('Mob/errorfile/list.phtml');
    }

    public function viewAction() {
        $result = '';
        $params['id'] = isset($_GET['id']) ? $_GET['id'] : '';

        $smarty = Yaf_Registry::get("smarty");
        $mclientErrorFile = new MclientErrorFileModel();
        $colum = " *  ";
        $condition = '';
        $condition .= "AND `id` = ? ";
        $valueAry[] = $params['id'];

        $condition = trim($condition, 'AND');
        $group = " ";
        $order = " ";

        $rows = $mclientErrorFile->getAllByCondition($colum, $condition, $valueAry, $group, $order);
        $row = isset($rows[0]) ? $rows[0] : array();
        if( $row['filepath'] != '' ){
            $filepath = "/letv/error/".$row['filepath'];
            if( file_exists("/letv/error/".$row['filepath']) ){
                $result = file_get_contents($filepath);
                $result = str_replace("\n", "<br>", $result);
            }
        }
        
        $smarty->assign("result", $result);
        $smarty->assign("params", $params);
        $smarty->assign("menu", $this->_menu);
        $smarty->assign("pagenav",$pagenav);
        $smarty->display('Mob/errorfile/view.phtml');
    }

    /**
     * 用户验证
     */
    private function  _checkUser(){
        /* 验证用户 */
        if ( $_SERVER['PHP_AUTH_USER'] != "Mobilelog" && $_SERVER['PHP_AUTH_PW'] != "error123" ) {
            Header("WWW-Authenticate: Basic realm=\"API Document Login\"");
            Header("HTTP/1.0 401 Unauthorized");
            $str = "<big>您未经授权，^_^!,请使用您的帐号登录!</big>";

            $smarty = Yaf_Registry::get("smarty");
            $smarty->assign("result", $str);
            $smarty->assign("menu", $this->_menu);
            $smarty->display('Mob/errorfile/error.phtml');
            exit();
        }
    }

}
