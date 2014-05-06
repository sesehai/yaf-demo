<?php
class MenuController extends Yaf_Controller_Abstract {

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
        $leftMenu = $logMenuCount->getLeftMenu1();
        

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
        $smarty = Yaf_Registry::get("smarty");

        $params = array();
        $result = $this->_getMenu($params);
        $smarty->assign("params", $params);
        $smarty->assign("result", $result);
        $smarty->assign("menu", $this->_menu);
        $smarty->display('Loganalysis/menu/list.phtml');
    }

    public function addAction() {
        $params = array();
        $request = $this->getRequest();
        if( $request->getMethod() == "POST" ){
            $params['name'] = $_POST['name'];
            $params['mod'] = $_POST['mod'];
            $params['ctl'] = $_POST['ctl'];
            $params['act'] = $_POST['act'];
            $params['type'] = $_POST['type'];
            $params['order'] = $_POST['order'];
            $params['parentid'] = $_POST['parentid'];
            $isAdd = $this->_addMenu($params);
            if($isAdd){
                $message = "添加成功，可继续添加！";
            }else{
                $message = "";
            }
        }else{
            $message = "";
        }
        $smarty = Yaf_Registry::get("smarty");

        $result = $this->_getMenu($params);
        $mainMenu = $this->_getMenuBytype($type = 0);
        $mainMenuAry[0] = '无所属主菜单';
        foreach($mainMenu as $menu){
            $mainMenuAry[$menu['id']] = $menu['name'];
        }

        $smarty->assign("mainMenuAry", $mainMenuAry);
        $smarty->assign("message", $message);
        $smarty->assign("params", $params);
        $smarty->assign("result", $result);
        $smarty->assign("menu", $this->_menu);
        $smarty->display('Loganalysis/menu/add.phtml');
    }

    public function editAction() {
        $params = array();
        $request = $this->getRequest();
        $params['id'] = $request->get('id', '');
        $request = $this->getRequest();
        if( $request->getMethod() == "POST" ){
            $params['name'] = $_POST['name'];
            $params['mod'] = $_POST['mod'];
            $params['ctl'] = $_POST['ctl'];
            $params['act'] = $_POST['act'];
            $params['type'] = $_POST['type'];
            $params['order'] = $_POST['order'];
            $params['parentid'] = $_POST['parentid'];
            $isOk = $this->_editMenu($params['id'], $params);
            if($isOk){
                $message = "修改成功！";
            }else{
                $message = "";
            }
        }else{
            $message = "";
        }
        $smarty = Yaf_Registry::get("smarty");

        $result = $this->_getMenuById($params);
        $mainMenu = $this->_getMenuBytype($type = 0);
        $typeAry = array(
            '0' => "主菜单",
            '1' => "子菜单",
        );
        $mainMenuAry[0] = '无所属主菜单';
        foreach($mainMenu as $menu){
            $mainMenuAry[$menu['id']] = $menu['name'];
        }

        $smarty->assign("typeAry", $typeAry);
        $smarty->assign("mainMenuAry", $mainMenuAry);
        $smarty->assign("message", $message);
        $smarty->assign("params", $params);
        $smarty->assign("result", $result);
        $smarty->assign("menu", $this->_menu);
        $smarty->display('Loganalysis/menu/edit.phtml');
    }

    public function delAction() {
        $params = array();
        $request = $this->getRequest();
        $id = $request->get('id', '');
        $logMenu = new LogMenuModel();
        $condition = " `id` = ?";
        $valueAry[] = $id;
        $result = $logMenu->del($condition, $valueAry);
        $this->redirect("/loganalysis/menu/list");
    }

    private function _getMenu($params){
        $result = array();
        $logMenu = new LogMenuModel();
        $colum = " * ";
        $order = " ORDER BY `order` ASC ";
        $limit = " LIMIT 10";
        $result = $logMenu->getAllByCondition($colum, $condition = '', $valueAry = array(), $group = '', $order, $limit);

        return $result;
    }

    private function _getMenuById($params){
        $result = array();
        $logMenu = new LogMenuModel();
        $colum = " * ";
        $condition = " `id` = ? ";
        $valueAry[] = $params['id'];
        $limit = " LIMIT 1";
        $result = $logMenu->getAllByCondition($colum, $condition, $valueAry, $group = '', $order = '', $limit);
        return isset($result[0]) ? $result[0] : array();
    }

    private function _getMenuBytype($type){
        $result = array();
        $logMenuCount = new LogMenuModel();
        $colum = " * ";
        $condition = " `type` = ? ";
        $valueAry[] = $type;
        $order = " ORDER BY `order` ASC ";
        $limit = " ";
        $result = $logMenuCount->getAllByCondition($colum, $condition, $valueAry, $group = '', $order, $limit);

        return $result;
    }

    private function _addMenu($params){
        $result = array();
        $logMenuCount = new LogMenuModel();
        $data = $params;
        $data['ctime'] = date("Y-m-d H:i:s", time());
        $data['url'] = "/{$params['mod']}/{$params['ctl']}/{$params['ctl']}";
        $result = $logMenuCount->add($data);

        return $result;
    }

    private function _editMenu($id, $params){
        $result = array();
        $logMenu = new LogMenuModel();
        $data = $params;
        $data['uptime'] = date("Y-m-d H:i:s", time());
        $data['url'] = "/{$params['mod']}/{$params['ctl']}/{$params['ctl']}";
        $condition = " `id` = ? ";
        $conditionValAry[] = $id;
        $result = $logMenu->edit($condition, $conditionValAry, $data);

        return $result;
    }

    public function testAction(){
        try {
            $dbh = new PDO($dsn, $user, $pwd, array(PDO::ATTR_PERSISTENT => true,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
        } catch (PDOException $e) {
            echo 'Connection failed: '. $e->getMessage();
        }

        try{
            $sth = $oPdo_vrs->prepare($sql);
            $sth->execute();
        }catch(Exception $e){
            $e->getMessage();
            echo '<br>';
            echo $e->getTraceAsString();
            exit;
        }
    }

}
