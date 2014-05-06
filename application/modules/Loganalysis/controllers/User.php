<?php
class UserController extends Yaf_Controller_Abstract {

    /**
     * 构造函数
     * @param array $config
     */
    public function init()
    {
        $this->initParams();
    }

    /**
     * 初始化调用是的参数
     */
    public function initParams(){
        $request = $this->getRequest();
    }

    public function loginAction() {//默认Action
        $request = $this->getRequest();
        if( $request->getMethod() == "POST" ){
            $params['username'] = isset($_POST['username']) ? $_POST['username'] : '';
            $params['password'] = isset($_POST['password']) ? $_POST['password'] : '';
            if( !empty($params['username']) && !empty($params['password']) ){
                $isLogin = $this->_checkuser($params);
                if( $isLogin ){
                    $this->redirect("/loganalysis/index/index");
                }else{
                    $smarty = Yaf_Registry::get("smarty");
                    $smarty->display('Loganalysis/user/login.phtml');
                }
            }else{
                $smarty = Yaf_Registry::get("smarty");
                $smarty->display('Loganalysis/user/login.phtml');
            }
        }else{
            $smarty = Yaf_Registry::get("smarty");
            $smarty->display('Loganalysis/user/login.phtml');
        }

    }

    public function logoutAction() {//默认Action
        $session = Yaf_Session::getInstance();
        $userInfo = array();
        $session->set("userInfo",$userInfo);
        $this->redirect("/loganalysis/index/index");
    }

    private function _checkuser($params){
        $result = false;
        $user = new UserModel();
        $colum = " * ";
        $condition = '';
        $condition .= "AND `username` = ? ";
        $valueAry[] = $params['username'];
        $condition .= "AND `password` = ? ";
        $condition = trim($condition, 'AND');
        $valueAry[] = md5($params['password']);
        $group = " ";
        $order = " ";
        $rows = $user->getAllByCondition($colum, $condition, $valueAry, $group, $order);
        if( isset($rows[0]) && !empty($rows[0]) && $rows[0]['status'] == '1' ){
            $userInfo = array(
                'username' =>$rows[0]['username'],
                'realname' =>$rows[0]['realname'],
                'privs' =>$rows[0]['privs'],
                'status' =>$rows[0]['status'],
                'last_ip' =>$rows[0]['last_ip'],
                'lastlogin' =>$rows[0]['lastlogin'],
            );
            $session = Yaf_Session::getInstance();
            $session->set("userInfo",$userInfo);
            $result = true;
        }

        return $result;
    }
}
