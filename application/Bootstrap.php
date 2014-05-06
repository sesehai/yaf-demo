<?php

/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * 这些方法, 都接受一个参数:Yaf_Dispatcher $dispatcher
 * 调用的次序, 和申明的次序相同
 */

class Bootstrap extends Yaf_Bootstrap_Abstract{

    private $config;
    //注册config文件
    public function _initConfig() {
        $this->config = Yaf_Application::app()->getConfig();
        Yaf_Registry::set("config", $this->config);
        $config = Yaf_Registry::get("config");
    }

    public function _initError() {
        if($this->config->application->showErrors){
            error_reporting (-1);
            ini_set('display_errors','On');
        }
    }

    public function _initBd() {
        //$config = Yaf_Registry::get("config");
        //$type     = $config->get('database')->type;
        //$host     = $config->get('database')->host;
        //$port     = $config->get('database')->port;
        //$username = $config->get('database')->username;
        //$password = $config->get('database')->password;
        //$databaseName = $config->get('database')->databaseName;
        //$dsn = $type . ':' . 'dbname=' . $databaseName . ';host=' . $host;
        //Yaf_Registry::set('connection', new Connection($dsn, $username, $password));
    }

    // public function _initTwig(Yaf_Dispatcher $dispatcher) {
    //     Yaf_Loader::import("Twig/Adapter.php");
    //     $twig = new Twig_Adapter(Yaf_Registry::get("config")->get('application')->viewPath, Yaf_Registry::get("config")->get('application')->viewCachePath);
    //     $dispatcher->setView($twig);
    // }

    public function _initSmarty(Yaf_Dispatcher $dispatcher) {
        Yaf_Loader::import("smarty/Adapter.php");
        $smarty = new Smarty_Adapter(null, Yaf_Registry::get("config")->get("smarty"));
        Yaf_Registry::set("smarty", $smarty);
        $dispatcher->setView($smarty);
    }

    public function _initViewParameters(Yaf_Dispatcher $dispatcher) {
        //$dispatcher->initView(APP_PATH . "/views/")->assign("webroot", APP_PATH);
        Yaf_Dispatcher::getInstance()->autoRender(FALSE);  // 关闭自动加载模板
    }

    public function _initSesson() {
        //$sesson = Yaf_Session::getInstance();
        //Yaf_Registry::set('sesson', $sesson);
    }


    /**
    *过滤全局变量$_GET, $_POST, $_COOKIE等
    *@return void
    *@param void
    */
    public function _initGlobalFilter() {


    }

    public function _initRoute(Yaf_Dispatcher $dispatcher) {
        $router = Yaf_Dispatcher::getInstance()->getRouter();
        /**
         * 添加配置中的路由
         */
        $router->addConfig(Yaf_Registry::get("config")->routes);
    }

    public function _initPlugin(Yaf_Dispatcher $dispatcher) {
        $user = new UserPlugin();
        $dispatcher->registerPlugin($user);
        $appinit = new AppinitPlugin();
        $dispatcher->registerPlugin($appinit);
    }

    public function _initDefaultName(Yaf_Dispatcher $dispatcher) {
        $dispatcher->setDefaultModule("Index")->setDefaultController("Index")->setDefaultAction("index");
    }

}