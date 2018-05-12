<?php
class commondate{
    public $dbHost_Jitai = "localhost";
    public $dbHost_Server = "localhost";
    public $uid_Jitai = "";
    public $uid_Server = "";
    public $pwd_Jitai = "";
    public $pwd_Server = "";
    public $dbName_Jitai = 'DZVS';
    public $dbName_Server = 'AnalyzedData';
    public $conn_Jitai;
    public $conn_Server;
    public $charset = 'utf-8';
    public $currentdate;
    public $connectionInfo_Jitai;
    public $connectionInfo_Server;
    public $params = array();
    public $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    public function  __construct()
    {
        $this->currentdate = date("Y-m-d", strtotime("-1 day"));                                          //获取前一天日期
        $this->connectionInfo_Jitai = array("UID" => $this->uid_Jitai, "PWD" => $this->pwd_Jitai, "Database" => $this->dbName_Jitai, 'CharacterSet' => $this->charset);
        $this->connectionInfo_Server = array("UID" => $this->uid_Server, "PWD" => $this->pwd_Server, "Database" => $this->dbName_Server, 'CharacterSet' => $this->charset);
        $this->conn_Jitai=sqlsrv_connect($this->dbHost_Jitai,$this->connectionInfo_Jitai);
        $this->conn_Server=sqlsrv_connect($this->dbHost_Server,$this->connectionInfo_Server);
        if($this->conn_Jitai == false)
        {
            sqlsrv_close($this->conn_Jitai);
            exit;
        }
        if($this->conn_Server == false)
        {
            sqlsrv_close($this->conn_Jitai);
            exit;
        }
    }
    public function findlastday()                                                                                     //查找最近的一个工作日
    {
        $count=0;
        $lastday=$this->currentdate;
        while(1)
        {
            $sql_searchlastday = "select count(1) as count from dbo.Indextable
where convert(varchar(10),Createtime,120) = '" . $lastday . "'";
            $query_searchlastday = sqlsrv_query($this->conn_Jitai, $sql_searchlastday, $this->params, $this->options);
            while ($row_lastday = sqlsrv_fetch_array($query_searchlastday)) {
                if ($row_lastday['count'] == 0) {
                $lastday = date("Y-m-d", (strtotime($lastday) - 3600 * 24));
            }
            else if($row_lastday['count']!=0)
                $count++;
            }
        if($count>0)
            break;
        }
        return $lastday;
    }
}



function extractFail()
{
   $extractdate=new commondate();
   $lastday=$extractdate->findlastday();
   $conn_Jitai=$extractdate->conn_Jitai;
   $conn_Server=$extractdate->conn_Server;
   $params=$extractdate->params;
   $options=$extractdate->options;
   $sql_searchindex = "select tablename,CreateTime from dbo.Indextable
where convert(varchar(10),Createtime,120) = '" . $lastday . "'";
   $query_searchindex = sqlsrv_query($conn_Jitai, $sql_searchindex, $params, $options);
    @ $row_count = sqlsrv_num_rows($query_searchindex);
    if ($row_count === false) {
        exit;
        sqlsrv_close($conn_Jitai);
    } else {
        $i = 0;
        while ($row_eachwagon = sqlsrv_fetch_array($query_searchindex)) {
            $tablename_short = substr($row_eachwagon['tablename'], 1, 7);                                 //车号
            $faillist[$i][] = $tablename_short;

            $PrintData = $row_eachwagon['CreateTime']->format('Y/m/d H:i:s');                                           //印刷时间
            $faillist[$i][] = $PrintData;

            $sql_total = "select COUNT(1) as count from dbo." . $row_eachwagon['tablename'];                          //报废总数
            $query_total = sqlsrv_query($conn_Jitai, $sql_total, $params, $options);
            while ($row_total = sqlsrv_fetch_array($query_total)) {
                $faillist[$i][] = $row_total['count'];
            }

            $sql_ser = "select COUNT(1) as count from dbo." . $row_eachwagon['tablename'] . " where Reserve2=2";     //报出严重废总数
            $query_ser = sqlsrv_query($conn_Jitai, $sql_ser, $params, $options);
            while ($row_ser = sqlsrv_fetch_array($query_ser)) {
                $faillist[$i][] = $row_ser['count'];
            }

            $sql_psn = "select count(distinct PSN) as psnnum from dbo." . $row_eachwagon['tablename'] . " where Reserve2=2";//打仓数
            $query_psn = sqlsrv_query($conn_Jitai, $sql_psn, $params, $options);
            while ($row_psn = sqlsrv_fetch_array($query_psn)) {
                $faillist[$i][] = $row_psn['psnnum'];
            }

            $sql_maxk = "select top 1 FormatPos,COUNT(*) as count from dbo." . $row_eachwagon['tablename'] . "       
                  group by FormatPos
                  order by count DESC";//作废最多K位
            $query_maxk = sqlsrv_query($conn_Jitai, $sql_maxk, $params, $options);
            while ($row_maxk = sqlsrv_fetch_array($query_maxk)) {
                $faillist[$i][] = $row_maxk['FormatPos'];
            }

            $sql_maxM = "select top 1 MacroTitle from dbo.ModelMacroLog_339
                  where MacroID=(select top 1 MacroIndex from dbo." . $row_eachwagon['tablename'] . "
                  group by MacroIndex
                  order by count(*) DESC)";//作废最多区域
            $query_maxM = sqlsrv_query($conn_Jitai, $sql_maxM, $params, $options);
            while ($row_maxM = sqlsrv_fetch_array($query_maxM)) {
                $faillist[$i][] = $row_maxM['MacroTitle'];
            }
            $faillist[$i][] = 'J5';
            $i = $i + 1;
        }
    }
    for ($temp = 0; $temp < $i; $temp++) {
        $sql_checkifexist = "select count(1) as checksum from dbo.Kexin_WY where Tablename ='" . $faillist[$temp][0] . "'";
        $quer_checkifexist = sqlsrv_query($conn_Server, $sql_checkifexist, $params, $options);                          //检查服务器数据库中是否已存在该车号
        while ($checkifexist = sqlsrv_fetch_array($quer_checkifexist)) {
            if ($checkifexist['checksum'] == 0) {                                                                       //计数结果为0表示不存在该车号，执行插入操作
                $sql_inserindex = "insert into dbo.Kexin_WY([Tablename],[Createtime],[Totalfail],[Serfail],[Psnnum],[MaxK],[MaxM],[Wangonname])
            values('" . $faillist[$temp][0] . "','" . $faillist[$temp][1] . "','" . $faillist[$temp][2] . "','" . $faillist[$temp][3] . "','" . $faillist[$temp][4] . "','" . $faillist[$temp][5] . "','" . $faillist[$temp][6] . "','" . $faillist[$temp][7] . "')";
                $query_instoser = sqlsrv_query($conn_Server, $sql_inserindex, $params, $options);
            }
        }
    }
}
//extractFail();
function extractCon(){
    $extractCon=new commondate();
    $lastday=$extractCon->findlastday();
    $conn_Jitai=$extractCon->conn_Jitai;
    $conn_Server=$extractCon->conn_Server;
    $params=$extractCon->params;
    $options=$extractCon->options;
    $confail_all[][]=array();
    $sql_searchindex = "select tablename,CreateTime from dbo.Indextable
where convert(varchar(10),Createtime,120) = '" . $lastday . "'";
    $query_searchindex = sqlsrv_query($conn_Jitai, $sql_searchindex, $params, $options);
    @ $row_count = sqlsrv_num_rows($query_searchindex);
    if ($row_count === false) {
        sqlsrv_close($conn_Jitai);
        exit;
    } else {
        while ($row_eachwagon = sqlsrv_fetch_array($query_searchindex)) {
            unset($confail);
            $confail[0]=array(0,0,0);
            $i=0;
            $j=0;
            $count=0;
            $sql_confail = "select PSN as psn,FormatPos as pos,Reserve3 as area from dbo." . $row_eachwagon['tablename'] ." order by [index]";
            $query_confail = sqlsrv_query($conn_Jitai, $sql_confail, $params, $options);
            while($row_confail = sqlsrv_fetch_array($query_confail))
            {
                if($confail[$i][0]==0&&$confail[$i][1]==0&&$confail[$i][2]==0)
                {
                    $confail[$i][0]=$row_confail['psn'];
                    $confail[$i][1]=$row_confail['pos'];
                    $confail[$i][2]=$row_confail['area'];
                    $confail[$i][3]=$row_eachwagon['tablename'];
                }
                else if($confail[$i][0]==$row_confail['psn'])
                {
                    continue;
                }
                else if((@ $confail[$i][0]+3>=$row_confail['psn'])){
                    if($confail[$i][1]==$row_confail['pos']){
                    $count++;
                    $i++;
                    $confail[$i][0]=$row_confail['psn'];
                    $confail[$i][1]=$row_confail['pos'];
                    $confail[$i][2]=$row_confail['area'];
                    $confail[$i][3]=$row_eachwagon['tablename'];
                    }
                    else if($confail[$i][1]!=$row_confail['pos']){
                        continue;
                    }

                }
                else if(@ $confail[$i][0]+3<$row_confail['psn']){
                    if($count<5){
                        unset($confail);
                        $i=0;
                        $count=0;
                        $confail[$i][0]=$row_confail['psn'];
                        $confail[$i][1]=$row_confail['pos'];
                        $confail[$i][2]=$row_confail['area'];
                        $confail[$i][3]=$row_eachwagon['tablename'];
                    }
                    else if($count>=5){
                        $confail_all[$j]=$confail;
                        $j++;
                        unset($confail);
                        $i=0;
                        $count=0;
                        $confail[$i][0]=$row_confail['psn'];
                        $confail[$i][1]=$row_confail['pos'];
                        $confail[$i][2]=$row_confail['area'];
                        $confail[$i][3]=$row_eachwagon['tablename'];
                    }
            }
            }
        }
        print_r($confail_all);
    }
}
extractCon();
?>