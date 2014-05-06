<?php
Class LogNginxPhpTimeCountModel extends BaseModel{

    private $table;
    public function __construct(){
        parent::__construct();
        $this->table = 'log_nginx_php_time_count';

    }

    public function getAll(){
        $colum = " * ";
        $sql = "SELECT {$colum} FROM {$this->table}";
        $where = " WHERE ";
        $where .= " 1 ";
        $group = " ";
        $sql .= $where.$group;
        $result = $this->query($sql, $valueAry = array(), $dbname = 'loganalysis');
        return $result;
    }

    public function getAllByCondition($colum, $condition, $valueAry, $group, $order){
        $sql = "SELECT {$colum} FROM {$this->table}";
        $where = " WHERE {$condition} ";
        $sql .= $where.$group.$order;
        $result = $this->query($sql, $valueAry, $dbname = 'loganalysis');
        return $result;
    }
}