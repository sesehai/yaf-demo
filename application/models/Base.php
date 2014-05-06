<?php
Class BaseModel{

    public function __construct(){

    }

    public function getDb($dbname = ''){
        $config = Yaf_Registry::get("config");

        $dbConfig = array(
            'db_type' => $config->get('database'."_".$dbname)->type,
            'dbconfig' => array (
                'host' => $config->get('database'."_".$dbname)->host,
                'port' => $config->get('database'."_".$dbname)->port,
                'dbname' => $config->get('database'."_".$dbname)->databaseName,
                'username' => $config->get('database'."_".$dbname)->username,
                'password' => $config->get('database'."_".$dbname)->password,
                'driver_options' => array(
                    PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
                    PDO::ATTR_EMULATE_PREPARES => true,
                    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8';",
                ),
            ),
        );

        return Pdo_Db::instance($dbConfig);
    }

    public function insert($data, $table, $dbname){
        $result = '';
        $db = $this->getDb($dbname);
        $db->connect();
        $fieldStr = '';
        $fieldValStr = '';
        $valueAry = array();
        if( isset($data) && !empty($data) ){
            foreach($data as $key=>$value){
                $fieldStr .= " `{$key}`,";
                $fieldValStr .= " ?,";
                $valueAry[] = $value;
            }
            $fieldStr = substr($fieldStr,0,-1);
            $fieldValStr = substr($fieldValStr,0,-1);
        }
        $sql = "INSERT INTO `{$table}`({$fieldStr}) VALUES({$fieldValStr})";
        $db->prepare($sql);
        $result = $db->execute($valueAry);
        $db->disConnect();
        return $result;
    }

    public function query($sql, $valueAry, $dbname){
        $db = $this->getDb($dbname);
        $db->connect();
        $db->prepare($sql);
        $result = $db->execute($valueAry);
        $row = $db->fetchAll();
        $db->disConnect();
        return $row;
    }

    public function getOne($sql, $valueAry, $dbname){
        $db = $this->getDb($dbname);
        $db->connect();
        $db->prepare($sql);
        $result = $db->execute($valueAry);
        $row = $db->fetchOne();
        $db->disConnect();
        return $row;
    }

    public function update($condition, $conditionValAry, $data, $table, $dbname){
        $result = '';
        $db = $this->getDb($dbname);
        $db->connect();
        $fieldValStr = '';
        $valueAry = array();
        if( isset($data) && !empty($data) ){
            foreach($data as $key=>$value){
                $fieldValStr .= " `{$key}` = ?,";
                $valueAry[] = $value;
            }
        }
        $fieldValStr = substr($fieldValStr,0,-1);
        foreach ($conditionValAry as $value) {
            $valueAry[] = $value;
        }
        $sql = "UPDATE `{$table}` SET {$fieldValStr}  WHERE {$condition} ";
        $db->prepare($sql);
        $result = $db->execute($valueAry);
        $db->disConnect();
        return $result;
    }

    public function delete($condition, $valueAry, $table, $dbname){
        $db = $this->getDb($dbname);
        $db->connect();
        $sql = "DELETE  FROM `{$table}` WHERE {$condition} ";
        $db->prepare($sql);
        $result = $db->execute($valueAry);
        $db->disConnect();
        return $result;
    }

}