<?php
/**
 *
 * PDO 管理类
 * @author luq
 * @version 1.0
 *
 */
class Pdo_Db{
    private $connection = null;
    
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
    public function __construct($configAry){
        if ($this->connection === null) {
            if( $configAry['db_type'] == "mysql" ){
                if( isset($configAry['dbconfig']['dbname']) ){
                    $dsn = "mysql:dbname={$configAry['dbconfig']['dbname']};";
                }else{
                    $dsn = "mysql:dbname=;";
                }
                if( isset($configAry['dbconfig']['host']) ){
                    $dsn .= "host={$configAry['dbconfig']['host']};";
                }else {
                    $dsn .= "host=";
                }
                if( isset($configAry['dbconfig']['port']) ){
                    $dsn .= "port={$configAry['dbconfig']['port']}";
                }else {
                    $dsn .= "port=";
                }
            }else{
                throw new Exception("Params error!");
            }

            try {
                if( isset($configAry['dbconfig']['username']) ){
                    $user = $configAry['dbconfig']['username'];
                }else {
                    $user = '';
                }
                if( isset($configAry['dbconfig']['password']) ){
                    $pwd = $configAry['dbconfig']['password'];
                }else{
                    $pwd = '';
                }
                if( isset($configAry['dbconfig']['driver_options']) ){
                    $options = $configAry['dbconfig']['driver_options'];
                }else{
                    $options = array(PDO::ATTR_PERSISTENT => true,PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");
                }

                $dbh = new PDO($dsn, $user, $pwd, $options);
                $this->connection = $dbh;
            } catch (PDOException $e) {
                echo 'PDO Connection failed: '. $e->getMessage();
            }
        }

        return $this->connection;
    }


    /**
     * @return PDOStatement
     */
    public  function query($query) {
        return $this->connection->query($query)->fetchAll();
    }


}
