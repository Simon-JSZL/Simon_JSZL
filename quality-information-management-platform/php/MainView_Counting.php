<?php
class Totalcount                                                                                                       //公共的数据链接、sql执行和日期查找封包
{
    public $dbHost = "localhost";
    public $wangon;
    public $startdate;
    public $conn;
    public $params = array();
    public $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    public $connectionInfo = array("UID" =>"", "PWD" => "", "Database" => "AnalyzedData", 'CharacterSet' => "utf-8");
    public $runningdate;
    function __construct(){
        $this->wangon = $_GET['wangon'];
        $this->startdate=date("Y-m-d",strtotime("-1 day"));
        $this->conn = sqlsrv_connect($this->dbHost, $this->connectionInfo);
    if ($this->conn == false) {
        echo "连接至服务器数据库失败";
        die(print_r(sqlsrv_errors(), true));}
}
public function find5days()                                                                                           //查找最近的五个工作日的日期
{
    $i = 0;
    $back5days=0;
    $currentdate=$this->startdate;
    $wangon=$this->wangon;
    $runningdate=$this->runningdate;
    while (1)
    {
        $sql_searchdate = "select top 1 count(1) as count from dbo.Kexin_WY where convert(varchar(10),Createtime,120) = '" . $currentdate . "' and MachineId = '" . $wangon . "'
         group by [Id]";
        $query_searchdate = sqlsrv_query($this->conn, $sql_searchdate, $this->params, $this->options);
        while ($row_date = sqlsrv_fetch_array($query_searchdate)) {
            if ($row_date['count'] != 0) {
                $back5days++;
                $runningdate[$i++] = $currentdate;
            }
         }
    $currentdate= date("Y-m-d", (strtotime($currentdate) - 3600 * 24));
    if (($back5days >= 5)||($currentdate == '2018-04-10'))
    break;
    }
    return $runningdate;//倒序:[arr0 => 2018-04-25,arr1=>2018-04-24...]
}}
function sqlexec($sql, $key){                                                                                           //通用的sql执行方法
    $totalcount= new Totalcount();
    $query = sqlsrv_query($totalcount->conn, $sql, $totalcount->params, $totalcount->options);
        while ($row_eachline = sqlsrv_fetch_array($query)) {
            return $row_eachline[$key];
    }
return(0);
}
function runsql_singleday($currentdate,$wangon){                                                                       //一日作废统计
    $sql_MaxK = "select top 1 Maxk as maxk ,COUNT(1) as count
from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) = '" . $currentdate . "' and MachineId = '" . $wangon . "'
group by MaxK
order by count DESC";                                                                                                 //一日报错最多K位

    $sql_MaxM = "select top 1 MaxM as maxM ,COUNT(1) as count
from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) = '" . $currentdate . "' and MachineId = '" . $wangon . "'
group by MaxM
order by count DESC";                                                                                                 //一日报错最多区域

    $sql_AVGTotal = "select AVG(Totalfail) as AVGTotal from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) = '" . $currentdate . "' and MachineId = '" . $wangon . "'";          //一日报错总数的平均数

    $sql_AVGSer = "select AVG(Serfail) as AVGSer from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) = '" . $currentdate . "' and MachineId = '" . $wangon . "'";          //一日严重废总数的平均数

    $sql_AVGPsn = "select AVG(Psnnum) as AVGPsn from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) = '" . $currentdate . "' and MachineId = '" . $wangon . "'";          //一日三仓总数的平均数

    $singdayresult = array('maxk'=>sqlexec($sql_MaxK, 'maxk'),
        'maxK_count'=>sqlexec($sql_MaxK, 'count'),
        'maxM'=>sqlexec($sql_MaxM, 'maxM'),
        'maxM_count'=>sqlexec($sql_MaxM, 'count'),
        'AVGTotal'=>sqlexec($sql_AVGTotal, 'AVGTotal'),
        'AVGSer'=>sqlexec($sql_AVGSer, 'AVGSer'),
        'AVGPsn'=>sqlexec($sql_AVGPsn, 'AVGPsn'),
        'CurrentDate'=>$currentdate);
    return $singdayresult;
}

function runsql_total($begindate,$enddate,$wangon){                                                                    //一段时间内总体的作废统计
    $sql_MaxK = "select top 1 Maxk as maxk ,COUNT(1) as count
from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "' and MachineId = '" . $wangon . "'
group by MaxK
order by count DESC";                                                                                                 //五个工作日内报错最多K位

    $sql_MaxM = "select top 1 MaxM as maxM ,COUNT(1) as count
from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "' and MachineId = '" . $wangon. "'
group by MaxM
order by count DESC";                                                                                                 //五个工作日内报错最多区域

    $sql_AVGTotal = "select AVG(Totalfail) as AVGTotal from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "' and MachineId = '" . $wangon . "'";//五个工作日内报错总数的平均数

    $sql_AVGSer = "select AVG(Serfail) as AVGSer from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "' and MachineId = '" . $wangon  . "'";//五个工作日内严重废总数的平均数

    $sql_AVGPsn = "select AVG(Psnnum) as AVGPsn from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "' and MachineId = '" . $wangon  . "'";//五个工作日内三仓总数的平均数

    $totalresult = array('maxk_total'=>sqlexec($sql_MaxK, 'maxk'),
    'maxK_count_totak'=>sqlexec($sql_MaxK, 'count'),
    'maxM_total'=>sqlexec($sql_MaxM, 'maxM'),
    'maxM_count_total'=>sqlexec($sql_MaxM, 'count'),
    'AVGTotal_total'=>sqlexec($sql_AVGTotal, 'AVGTotal'),
    'AVGSer_total'=>sqlexec($sql_AVGSer, 'AVGSer'),
    'AVGPsn_total'=>sqlexec($sql_AVGPsn, 'AVGPsn'));
    return $totalresult;
}

function runsql_eachwangon($begindate,$enddate,$wangon,$conn,$params,$options){
    $j=0;
    $sql="select WangonName as wangon,Totalfail as totalfail,Serfail as serfail,Psnnum as psnnum,convert(varchar(20),Createtime,120) as createtime from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "' and MachineId = '" . $wangon . "'
order by Createtime";
    $query = sqlsrv_query($conn, $sql, $params, $options);
    while ($row_eachline = sqlsrv_fetch_array($query)) {
    $arr[$j] = array('crtime_wangon'=>$row_eachline['createtime'],
        'tablename'=>$row_eachline['wangon'],
        'totalfail_wangon'=>$row_eachline['totalfail'],
        'serfail_wangon'=>$row_eachline['serfail'],
        'psnnum_wangon'=>$row_eachline['psnnum']);
    $j++;
}
return $arr;
}

function fivedaycount_total(){
    $totalcount = new Totalcount();
    @ $begindate = $totalcount->find5days()[4];
    $enddate = $totalcount->find5days()[0];
    $wangon = $totalcount->wangon;
    $totaldetail=runsql_total($begindate,$enddate,$wangon);
    return $totaldetail;
}

function fivedaycount_singleday(){
    $totalcount= new Totalcount();
    $fiveday=$totalcount->find5days();
    $arraylength=count($fiveday)-1;
    $wangon=$totalcount->wangon;
    for ($i=$arraylength;$i>=0;$i--)
    {
        @ $current=$fiveday[$i];
        $j=$arraylength-$i;
        $fivedaydetail[$j]=runsql_singleday($current,$wangon);
    }
    return $fivedaydetail;//正序:[arr0=>2018-04-24,arr1=>2018-04-25..]
}
function fivedaycount_eachwangon(){
    $totalcount=new Totalcount();
    @ $begindate = $totalcount->find5days()[4];
    $enddate = $totalcount->find5days()[0];
    $wangon = $totalcount->wangon;
    $conn =$totalcount->conn;
    $params= $totalcount->params;
    $options= $totalcount->options;
    $eachwangondetail=runsql_eachwangon($begindate,$enddate,$wangon,$conn,$params,$options);
    return $eachwangondetail;
}
$totalresult=fivedaycount_total();
$singleresult=fivedaycount_singleday();
$eachwangonresult=fivedaycount_eachwangon();
$result=array("TotalResult"=>$totalresult) + array("SingleResult"=>$singleresult)+array("EachWangonResult"=>$eachwangonresult);
echo json_encode($result);
?>
