<?php
Class BaseModel{

    protected  $dbConfig;
    public function __construct(){
        $config = Yaf_Registry::get("config");
        $this->dbConfig = array(
            'db_type' => $config->get('database')->type,
            'dbconfig' => array (
                'host' => $config->get('database')->host,
                'port' => $config->get('database')->port,
                'dbname' => $config->get('database')->databaseName,
                'username' => $config->get('database')->username,
                'password' => $config->get('database')->password,
                'driver_options' => array(
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                    PDO::ATTR_EMULATE_PREPARES => true,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';",
                ),
            ),
        );


    }

}