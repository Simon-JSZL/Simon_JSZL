<?php
class Totalcount                                                                                                       //公共的数据链接、sql执行和日期查找封包
{
    public $dbHost = "localhost";
    public $machineId;
    public $startdate;
    public $conn;
    public $params = array();
    public $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    public $connectionInfo = array("UID" =>"", "PWD" => "", "Database" => "AnalyzedData", 'CharacterSet' => "utf-8");
    public $runningdate;
    function __construct(){
        $this->machineId = $_GET['machineId'];
        $this->startdate=date("Y-m-d",strtotime("-1 day"));
        $this->conn = sqlsrv_connect($this->dbHost, $this->connectionInfo);
    if ($this->conn == false) {
        echo "连接至服务器数据库失败";
        die(print_r(sqlsrv_errors(), true));}
}
public function find5days()                                                                                           //查找最近的五个工作日的日期
{
    $back5days=0;
    $currentdate=$this->startdate;
    $machineId=$this->machineId;
    $runningdate=$this->runningdate;
    while (1)
    {
        $sql_searchdate = "select top 1 count(1) as count from dbo.GeneralFail_".$machineId." where convert(varchar(10),Createtime,120) = '" . $currentdate . "'";
        $query_searchdate = sqlsrv_query($this->conn, $sql_searchdate, $this->params, $this->options);
        if (sqlsrv_fetch_array($query_searchdate)['count'] != 0) {
            $back5days++;
            $runningdate[] = $currentdate;
        }
    $currentdate= date("Y-m-d", (strtotime($currentdate."-1 day")));
    if (($back5days >= 5)||($currentdate == '2014-04-10'))
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
function runsql_singleday($currentdate,$machineId){
    $TableName='dbo.GeneralFail_'.$machineId;
    $sql_MaxK = "select top 1 Maxk as maxk ,COUNT(1) as count
from ".$TableName."
where convert(varchar(10),Createtime,120) = '" . $currentdate . "'
group by MaxK
order by count DESC";                                                                                                 //一日报错最多K位

    $sql_MaxM = "select top 1 MaxM as maxM ,COUNT(1) as count
from ".$TableName."
where convert(varchar(10),Createtime,120) = '" . $currentdate . "'
group by MaxM
order by count DESC";                                                                                                 //一日报错最多区域

    $sql_AVGTotal = "select AVG(Totalfail) as AVGTotal from ".$TableName."
where convert(varchar(10),Createtime,120) = '" . $currentdate . "'";          //一日报错总数的平均数

    $sql_AVGSer = "select AVG(Serfail) as AVGSer from ".$TableName."
where convert(varchar(10),Createtime,120) = '" . $currentdate . "'";          //一日严重废总数的平均数

    $sql_AVGPsn = "select AVG(Psnnum) as AVGPsn from ".$TableName."
where convert(varchar(10),Createtime,120) = '" . $currentdate . "'";          //一日三仓总数的平均数

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

function runsql_total($begindate,$enddate,$machineId){//一段时间内总体的作废统计
    $TableName='dbo.GeneralFail_'.$machineId;
    $sql_MaxK = "select top 1 Maxk as maxk ,COUNT(1) as count
from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "'
group by MaxK
order by count DESC";                                                                                                 //五个工作日内报错最多K位

    $sql_MaxM = "select top 1 MaxM as maxM ,COUNT(1) as count
from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "'
group by MaxM
order by count DESC";                                                                                                 //五个工作日内报错最多区域

    $sql_AVGTotal = "select AVG(Totalfail) as AVGTotal from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "'";//五个工作日内报错总数的平均数

    $sql_AVGSer = "select AVG(Serfail) as AVGSer from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "'";//五个工作日内严重废总数的平均数

    $sql_AVGPsn = "select AVG(Psnnum) as AVGPsn from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "'";//五个工作日内三仓总数的平均数

    $totalresult = array('maxk_total'=>sqlexec($sql_MaxK, 'maxk'),
    'maxK_count_total'=>sqlexec($sql_MaxK, 'count'),
    'maxM_total'=>sqlexec($sql_MaxM, 'maxM'),
    'maxM_count_total'=>sqlexec($sql_MaxM, 'count'),
    'AVGTotal_total'=>sqlexec($sql_AVGTotal, 'AVGTotal'),
    'AVGSer_total'=>sqlexec($sql_AVGSer, 'AVGSer'),
    'AVGPsn_total'=>sqlexec($sql_AVGPsn, 'AVGPsn'));
    return $totalresult;
}

function runsql_eachwangon($begindate,$enddate,$machineId,$conn,$params,$options){
    $j=0;
    $arr=array();
    $TableName='dbo.GeneralFail_'.$machineId;
    $sql="select WangonName as wangon,Totalfail as totalfail,Serfail as serfail,Psnnum as psnnum,convert(varchar(20),Createtime,120) as createtime from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "'
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
    $machineId = $totalcount->machineId;
    $totaldetail=runsql_total($begindate,$enddate,$machineId);
    return $totaldetail;
}

function fivedaycount_singleday(){
    $totalcount= new Totalcount();
    $fivedaydetail=array();
    $fiveday=$totalcount->find5days();
    $arraylength=count($fiveday)-1;
    $machineId=$totalcount->machineId;
    for ($i=$arraylength;$i>=0;$i--)
    {
        @ $current=$fiveday[$i];
        $j=$arraylength-$i;
        $fivedaydetail[$j]=runsql_singleday($current,$machineId);
    }
    return $fivedaydetail;//正序:[arr0=>2018-04-24,arr1=>2018-04-25..]
}
function fivedaycount_eachwangon(){
    $totalcount=new Totalcount();
    @ $begindate = $totalcount->find5days()[4];
    $enddate = $totalcount->find5days()[0];
    $machineId= $totalcount->machineId;
    $conn =$totalcount->conn;
    $params= $totalcount->params;
    $options= $totalcount->options;
    $eachwangondetail=runsql_eachwangon($begindate,$enddate,$machineId,$conn,$params,$options);
    return $eachwangondetail;
}
$totalresult=fivedaycount_total();
$singleresult=fivedaycount_singleday();
$eachwangonresult=fivedaycount_eachwangon();
$result=array("TotalResult"=>$totalresult) + array("SingleResult"=>$singleresult)+array("EachWangonResult"=>$eachwangonresult);
echo json_encode($result);

