<?php
Class LogstatModel extends BaseModel{

    private $table;
    public function __construct(){
        parent::__construct();
        $this->table = 'stat_app_m_logs';

    }

    public function get_list_by_daterange($startDate, $endDate, $ip){
    	$db = new Pdo_Db($this->dbConfig);
        $type = 1;
        $colum = " create_date ";
        $sql = "SELECT {$colum} FROM {$this->table}";
        $where = " WHERE ";
        $where .= " create_date>='{$startDate}' AND create_date <= '{$endDate}' ";
        $where .= " AND ip = '{$ip}' ";
        $group = " GROUP BY create_date";
        $sql .= $where.$group;
        $result = $db->query($sql);
        $db = null;
        return $result;
    }

    public function get_list_by_date($date, $ip){
    	$db = new Pdo_Db($this->dbConfig);
        $type = 1;
        $colum = " ctl,clt_count,ctl_ups_over_100ms_count,clt_avg_time,create_date ";
        $sql = "SELECT {$colum} FROM {$this->table}";
        $where = " WHERE ";
        $where .= " create_date='{$date}' ";
        $where .= " AND ip = '{$ip}' ";
        $limit = " ";
        $sql .= $where.$limit;
        $result = $db->query($sql);
        $db = null;
        return $result;
    }
}