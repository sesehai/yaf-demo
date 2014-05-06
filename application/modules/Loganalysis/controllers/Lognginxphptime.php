<?php
class LognginxphptimeController extends Yaf_Controller_Abstract {

    /**
     * 构造函数
     * @param array $config
     */
    public function init()
    {
        $this->initParams();
        $this->_menu = $this->_initmenue();
    }

    /**
     * 初始化调用是的参数
     */
    public function initParams(){
        $request = $this->getRequest();
        $this->_menuid = $request->get('menuid', '');
    }

    private function _initmenue(){
        $result = array();
        $logMenuCount = new LogMenuModel();
        // active:0非选中，1选中
        // type: 0主菜单，1子菜单
        $leftMenu = $logMenuCount->getLeftMenu();

        foreach ($leftMenu as $menu) {
            if( $this->_menuid == $menu['id'] ){
                $menu['active'] = '1';
                $result['pathMenu'] = $menu;
            }
            $result['leftMenu'][$menu['order']] = $menu;
        }

        return $result;
    }
    
    public function lognginxphptimecountpieAction() {
        $typeList = array(
            '1' => 'nginx',
            '2' => 'php',
        );
        $domainList = $this->_getdomainList();
        $domainAll = array('all'=>'全部');
        $domainList = array_merge($domainAll, $domainList);

        $time_rangeAry = array();
        for ($i=0.000; $i < 0.050; $i+=0.010) { 
            $val = sprintf('%.3f', $i);
            $time_rangeAry[$val] = $val;
        }
        for ($i=0.100; $i < 1.000; $i+=0.100) { 
            $val = sprintf('%.3f', $i);
            $time_rangeAry[$val] = $val;
        }
        for ($i=2; $i <= 10; $i+=1) { 
            $val = sprintf('%.3f', $i);
            $time_rangeAry[$val] = $val;
        }

        $serverIp = new ServerIpModel();
        $ipList = $serverIp->getIp();
        $ipAll = array('all'=>'全部');
        $ipList = array_merge($ipAll, $ipList);

        $params['date'] = isset($_POST['date']) ? $_POST['date'] : date("Y-m-d", (time() - 60*60*24));
        $params['ip'] = isset($_POST['ip']) ? $_POST['ip'] : 'all';
        $params['type'] = isset($_POST['type']) ? $_POST['type'] : '1';
        $params['domain'] = isset($_POST['domain']) ? $_POST['domain'] : 'all';
        $params['time_range_start'] = isset($_POST['time_range_start']) ? $_POST['time_range_start'] : '0.000';
        $params['time_range_end'] = isset($_POST['time_range_end']) ? $_POST['time_range_end'] : '10.000';

        $smarty = Yaf_Registry::get("smarty");
        $rows = $this->_loadData($params);
        $result = $this->_filterData($rows, $params);

        $smarty->assign("domainList", $domainList);
        $smarty->assign("time_rangeAry", $time_rangeAry);
        $smarty->assign("ipList", $ipList);
        $smarty->assign("typeList", $typeList);
        $smarty->assign("domainList", $domainList);
        $smarty->assign("params", $params);
        $smarty->assign("result", $result);
        $smarty->assign("menu", $this->_menu);
        $smarty->display('Loganalysis/lognginxphptime/logcountpie.phtml');
    }

    /**
     * 根据参数返回数据
     * @param array $params 参数
     * @return array 
     */
    private function _loadData($params){
        $logNginxPhpTimeCount = new LogNginxPhpTimeCountModel();
        $colum = " `time_range`,  SUM(`count`) AS `total` ";
        $condition = '';
        if( $params['ip'] != 'all' ){
            $condition .= "AND `ip` = ? ";
            $valueAry[] = $params['ip'];
        }
        if( $params['domain'] != 'all' ){
            $condition .= "AND `domain` = ? ";
            $valueAry[] = $params['domain'];
        }
        $condition .= "AND `logdate` = ? ";
        $valueAry[] = $params['date'];
        $condition .= " AND `time_range` >= ? ";
        $valueAry[] = $params['time_range_start'];
        $condition .= " AND `time_range` <= ? ";
        $valueAry[] = $params['time_range_end'];
        $condition .= " AND `type` = ? ";
        $valueAry[] = $params['type'];
        $condition = trim($condition, 'AND');
        $group = " GROUP BY `time_range` ";
        $order = " ORDER BY `time_range` ASC ";
        $rows = $logNginxPhpTimeCount->getAllByCondition($colum, $condition, $valueAry, $group, $order);

        return $rows;
    }

    /**
     * 取得modlist
     * @return array;
     */
    private function _getdomainList(){
        $modList = array(
            'dynamic' => 'dynamic',
            'static' => 'static',
            'app' => 'app',
        );
        return $modList;
    }

    /**
     * 过滤数据
     * @param array $rows 记录列表
     * @param array $params 参数列表
     * @return array
     */
    private function _filterData($rows, $params){
        $result = array(
            'title_text' => '相应时间访问量',
            'subtitle_text' => '一天访问量',
            'yAxis_title_text' => '数量',
            'valueSuffix' => '次',
            'series' => '',
            'xAxis_categories' => '',
        );
        
        if( !empty($rows) ){
            $seriesData = '';
            foreach ($rows as $row) {
                $xAxisAry[$row['time_range']] = $row['time_range'];
                $seriesData .= "['{$row['time_range']}',   {$row['total']}],";
            }
            
            $result['series'] = trim($seriesData, ",");
        }

        return $result;
    }

}
