<?php
Class UserModel extends BaseModel{

    private $table;
    public function __construct(){
        parent::__construct();
        $this->table = 'user';

    }

    public function getAllByCondition($colum, $condition, $valueAry, $group, $order){
        $sql = "SELECT {$colum} FROM {$this->table}";
        $where = " WHERE {$condition} ";
        $sql .= $where.$group.$order;
        $result = $this->query($sql, $valueAry, $dbname = 'loganalysis');
        return $result;
    }
}