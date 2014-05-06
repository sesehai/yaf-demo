<?php
/**
 *
 * 创建pdo连接
 * @param string $db_type - mysql
 * @param array $config
 * @return PDO connection
 * 使用说明，例如：
 * <code>
 * <?php
 * $config = array(
 *     'db_type' => 'mysql',
 *     'dbconfig' => array (
 *          'host' =>'localhost',
 *          'port' => '3306',
 *          'dbname' => 'testdb',
 *          'username' => 'uname',
 *          'password' => 'pwd',
 *          'driver_options' => array(
 *              PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
 *              PDO::ATTR_EMULATE_PREPARES => true,
 *              PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';",
 *          ),
 *        ),
 *  );
 * $pdoDb = new Pdo_Db($config);
 * ?>
 * </code>
 *
 */

class Pdo_Db
{
    /**
     * PDO实例
     * @var PDO
     */
    protected $DB;
    /**
     * PDO准备语句
     * @var PDOStatement
     */
    protected $Stmt;
    /**
     * 最后的SQL语句
     * @var string
     */
    protected $Sql;
    /**
     * $config = array(
     *     'db_type' => 'mysql',
     *     'dbconfig' => array (
     *          'host' =>'localhost',
     *          'port' => '3306',
     *          'dbname' => 'testdb',
     *          'username' => 'uname',
     *          'password' => 'pwd',
     *          'driver_options' => array(
     *              PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
     *              PDO::ATTR_EMULATE_PREPARES => true,
     *              PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';",
     *          ),
     *        ),
     *  );
     * @var array
     */
    protected $Config;

    /**
     * 构造函数
     * @param array $config
     */
    public function __construct($config)
    {
        $this->Config = $config;
    }


    /**
     * 连接数据库
     * @return void
     */
    public function connect()
    {
        if ($this->DB === null) {
            if( $this->Config['db_type'] == "mysql" ){
                if( isset($this->Config['dbconfig']['dbname']) ){
                    $dsn = "mysql:dbname={$this->Config['dbconfig']['dbname']};";
                }else{
                    $dsn = "mysql:dbname=;";
                }
                if( isset($this->Config['dbconfig']['host']) ){
                    $dsn .= "host={$this->Config['dbconfig']['host']};";
                }else {
                    $dsn .= "host=";
                }
                if( isset($this->Config['dbconfig']['port']) ){
                    $dsn .= "port={$this->Config['dbconfig']['port']}";
                }else {
                    $dsn .= "port=";
                }
            }else{
                throw new Exception("Params error!");
            }

            try {
                if( isset($this->Config['dbconfig']['username']) ){
                    $user = $this->Config['dbconfig']['username'];
                }else {
                    $user = '';
                }
                if( isset($this->Config['dbconfig']['password']) ){
                    $pwd = $this->Config['dbconfig']['password'];
                }else{
                    $pwd = '';
                }
                if( isset($this->Config['dbconfig']['driver_options']) ){
                    $options = $this->Config['dbconfig']['driver_options'];
                }else{
                    $options = array(PDO::ATTR_PERSISTENT => true,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
                }

                $dbh = new PDO($dsn, $user, $pwd, $options);
                $this->DB = $dbh;
            } catch (PDOException $e) {
                echo 'PDO Connection failed: '. $e->getMessage();
            }
        }

        //默认把结果序列化成stdClass
        //$this->DB->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
        //自己写代码捕获Exception
        //$this->DB->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_SILENT);
    }

    /**
     * 断开连接
     * @return void
     */
    public function disConnect()
    {
        $this->DB = null;
        $this->Stmt = null;
    }

    /**
     * 执行sql，返回新加入的id
     * @param string $statement
     * @return string
     */
    public function exec($statement)
    {
        if ($this->DB->exec($statement)) {
            $this->Sql = $statement;
            return $this->lastId();
        }
        $this->errorMessage();
    }

    /**
     * 查询sql
     * @param string $statement
     * @return PdoDb
     */
    public function query($statement)
    {
        $res = $this->DB->query($statement);
        if ($res) {
            $this->Stmt = $res;
            $this->Sql = $statement;
            return $this;
        }
        $this->errorMessage();
    }

    /**
     * 序列化一次数据
     * @return mixed
     */
    public function fetchOne($fetch_style = PDO::FETCH_ASSOC)
    {
        return $this->Stmt->fetch($fetch_style);
    }

    /**
     * 序列化所有数据
     * @return array
     */
    public function fetchAll($fetch_style = PDO::FETCH_ASSOC)
    {
        return $this->Stmt->fetchAll($fetch_style);
    }

    /**
     * 最后添加的id
     * @return string
     */
    public function lastId()
    {
        return $this->DB->lastInsertId();
    }

    /**
     * 影响的行数
     * @return int
     */
    public function affectRows()
    {
        return $this->Stmt->rowCount();
    }

    /**
     * 预备语句
     * @param string $statement
     * @return PdoDb
     */
    public function prepare($statement)
    {
        $res = $this->DB->prepare($statement);
        if ($res) {
            $this->Stmt = $res;
            $this->Sql = $statement;
            return $this;
        }
        $this->errorMessage();
    }

    /**
     * 绑定数据
     * @param array $array
     * @return PdoDb
     */
    public function bindArray($array)
    {
        foreach ($array as $k => $v) {
            if (is_array($v)) {
                //array的有效结构 array('value'=>xxx,'type'=>PDO::PARAM_XXX)
                $this->Stmt->bindValue($k + 1, $v['value'], $v['type']);
            } else {
                $this->Stmt->bindValue($k + 1, $v, PDO::PARAM_STR);
            }
        }
        return $this;
    }

    /**
     * 执行预备语句
     * @return bool
     */
    public function execute($input_parameters = array())
    {
        if ($this->Stmt->execute($input_parameters)) {
            return true;
        }
        $this->errorMessage();
    }

    /**
     * 开启事务
     * @return bool
     */
    public function beginTransaction()
    {
        return $this->DB->beginTransaction();
    }

    /**
     * 执行事务
     * @return bool
     */
    public function commitTransaction()
    {
        return $this->DB->commit();
    }

    /**
     * 回滚事务
     * @return bool
     */
    public function rollbackTransaction()
    {
        return $this->DB->rollBack();
    }

    /**
     * 抛出错误
     * @throws Error
     * @return void
     */
    public function errorMessage()
    {
        $msg = $this->DB->errorInfo();
        throw new Exception('数据库错误：' . $msg[2]);
    }

    //---------------------
    /**
     * 单例实例
     * @var PdoDb
     */
    protected static $_instance;

    /**
     * 默认数据库
     * @static
     * @param array $config
     * @return PdoDb
     */
    public static function instance($config)
    {
        if (!self::$_instance instanceof Pdo_Db) {
            self::$_instance = new Pdo_Db($config);
            self::$_instance->connect();
        }
        return self::$_instance;
    }

    //----------------------

    /**
     * 获取PDO支持的数据库
     * @static
     * @return array
     */
    public static function getSupportDriver(){
        return PDO::getAvailableDrivers();
    }
    /**
     * 获取数据库的版本信息
     * @return array
     */
    public function getDriverVersion(){
        $name = $this->DB->getAttribute(PDO::ATTR_DRIVER_NAME);
        return array($name=>$this->DB->getAttribute(PDO::ATTR_CLIENT_VERSION));
    }

}