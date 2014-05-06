<?php
Class LogMenuModel extends BaseModel{

    private $table;
    public function __construct(){
        parent::__construct();
        $this->table = 'menu';

    }

    public function add($data, $dbname = 'loganalysis_rw'){
        $result = $this->insert($data, $this->table, $dbname);
        return $result;
    }

    public function edit($condition, $conditionValAry, $data, $dbname = 'loganalysis_rw'){
        $result = $this->update($condition, $conditionValAry, $data, $this->table, $dbname);
        return $result;
    }

    public function del($condition, $valueAry, $dbname = 'loganalysis_rw'){
        $result = $this->delete($condition, $valueAry, $this->table, $dbname);
        return $result;
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

    public function getAllByCondition($colum, $condition = "", $valueAry = array(), $group = '', $order = '',$limit = ''){
        $sql = "SELECT {$colum} FROM {$this->table}";
        if( $condition !== '' ){
            $where = " WHERE {$condition} ";
        }else{
            $where = '';
        }
        $sql .= $where.$group.$order.$limit;
        $result = $this->query($sql, $valueAry, $dbname = 'loganalysis');
        return $result;
    }

    public function getLeftMenu1(){
        $leftMenu = array();

        $order = " ORDER BY `order` ASC ";
        $subMenu = $this->getAllByCondition($colum = " * ", $condition = " `type` = ? "
            , $valueAry = array('1'), $group = '', $order);

        foreach ($subMenu as $row) {
            $row['active'] = '0';
            $subMenu[$row['parentid']][] = $row;
        }

        $order = " ORDER BY `order` ASC ";
        $mainMenu = $this->getAllByCondition($colum = " * ", $condition = " `type` = ? "
            , $valueAry = array('0'), $group = '', $order);

        foreach ($mainMenu as $row) {
            $row['active'] = '0';
            $leftMenu[] = $row;
            if( isset($subMenu[$row['id']]) ){
                $leftMenu = array_merge($leftMenu, $subMenu[$row['id']]);
            }
        }

        return $leftMenu;
    }

    public function getLeftMenu(){
        // active:0非选中，1选中
        // type: 0主菜单，1子菜单
        $leftMenu = array(
            array(
                'id' =>'1',
                'name' => '访问量分析',
                'url' => '',
                'active' => '0',
                'type' => '0',
                'order' => '1',
            ),
            array(
                'id' =>'2',
                'name' => '耗时访问量',
                'url' => '/loganalysis/lognginxphptime/lognginxphptimecountpie',
                'active' => '0',
                'type' => '1',
                'order' => '2',
            ),
            array(
                'id' =>'3',
                'name' => '时段访问量',
                'url' => '/loganalysis/logtime/logtimecountline',
                'active' => '0',
                'type' => '1',
                'order' => '3',
            ),
            array(
                'id' =>'4',
                'name' => '状态访问量',
                'url' => '/loganalysis/logstatus/logstatuscountpie',
                'active' => '0',
                'type' => '1',
                'order' => '4',
            ),
            array(
                'id' =>'5',
                'name' => 'url访问量',
                'url' => '/loganalysis/logurl/logurlcountline',
                'active' => '0',
                'type' => '1',
                'order' => '5',
            ),
            array(
                'id' =>'6',
                'name' => '版本访问量',
                'url' => '/loganalysis/logversion/logversioncountpie',
                'active' => '0',
                'type' => '1',
                'order' => '6',
            ),
            array(
                'id' =>'7',
                'name' => '平台访问量',
                'url' => '/loganalysis/logpcode/logpcodecountpie',
                'active' => '0',
                'type' => '1',
                'order' => '7',
            ),
            array(
                'id' =>'8',
                'name' => 'url耗时区间访问量',
                'url' => '/loganalysis/lognginxphptimeurl/lognginxphptimeurlline',
                'active' => '0',
                'type' => '1',
                'order' => '8',
            ),
            array(
                'id' =>'9',
                'name' => 'url耗时排行榜',
                'url' => '/loganalysis/lognginxphptimeurlpercent/lognginxphptimeurlpercentpie',
                'active' => '0',
                'type' => '1',
                'order' => '9',
            ),
            array(
                'id' =>'10',
                'name' => '访问量地区分布',
                'url' => '/loganalysis/loglocation/loglocationcountpie',
                'active' => '0',
                'type' => '1',
                'order' => '10',
            ),
            array(
                'id' =>'11',
                'name' => '日志处理统计',
                'url' => '',
                'active' => '0',
                'type' => '0',
                'order' => '11',
            ),
            array(
                'id' =>'12',
                'name' => '日志处理时间(IP)',
                'url' => '/loganalysis/logdeal/logdealcountline/type/time',
                'active' => '0',
                'type' => '1',
                'order' => '12',
            ),
            array(
                'id' =>'13',
                'name' => '日志处理总数(IP)',
                'url' => '/loganalysis/logdeal/logdealcountline/type/ip',
                'active' => '0',
                'type' => '1',
                'order' => '13',
            ),
            array(
                'id' =>'14',
                'name' => '日志处理总数',
                'url' => '/loganalysis/logdeal/logdealcountline/type/all',
                'active' => '0',
                'type' => '1',
                'order' => '14',
            ),
            array(
                'id' =>'15',
                'name' => '日志处理域名',
                'url' => '/loganalysis/logdomain/logdomaincount',
                'active' => '0',
                'type' => '1',
                'order' => '15',
            ),
            array(
                'id' =>'16',
                'name' => '错误日志',
                'url' => '',
                'active' => '0',
                'type' => '0',
                'order' => '16',
            ),
            array(
                'id' =>'17',
                'name' => 'nginx accesslog',
                'url' => '/loganalysis/logerror/logaccesscount',
                'active' => '0',
                'type' => '1',
                'order' => '17',
            ),
            array(
                'id' =>'18',
                'name' => 'nginx errorlog',
                'url' => '/loganalysis/logerror/lognginxerrorcount',
                'active' => '0',
                'type' => '1',
                'order' => '18',
            ),
            array(
                'id' =>'19',
                'name' => 'php errorlog',
                'url' => '/loganalysis/logerror/logphperrorcount',
                'active' => '0',
                'type' => '1',
                'order' => '19',
            ),
        );
        return $leftMenu;
    }

    public function getLeftMenuDynamic(){
        // active:0非选中，1选中
        // type: 0主菜单，1子菜单
        $leftMenu = array(
            array(
                'id' =>'1',
                'name' => '实时监控',
                'url' => '',
                'active' => '0',
                'type' => '0',
                'order' => '1',
            ),
            array(
                'id' =>'2',
                'name' => '单机实时数据',
                'url' => '/loganalysis/dynamic/index',
                'active' => '0',
                'type' => '1',
                'order' => '2',
            ),
            array(
                'id' =>'3',
                'name' => '分类实时数据',
                'url' => '/loganalysis/dynamic/category',
                'active' => '0',
                'type' => '1',
                'order' => '3',
            ),
            array(
                'id' =>'4',
                'name' => 'Android实时数据',
                'url' => '/loganalysis/dynamic/android',
                'active' => '0',
                'type' => '1',
                'order' => '4',
            ),
        );
        return $leftMenu;
    }

    public function getLeftMenuMobErrorFile(){
        // active:0非选中，1选中
        // type: 0主菜单，1子菜单
        $leftMenu = array(
            array(
                'id' =>'1',
                'name' => '客户端日志',
                'url' => '',
                'active' => '0',
                'type' => '0',
                'order' => '1',
            ),
            array(
                'id' =>'2',
                'name' => '错误日志',
                'url' => '/mob/errorfile/list',
                'active' => '0',
                'type' => '1',
                'order' => '2',
            ),
        );
        return $leftMenu;
    }

}