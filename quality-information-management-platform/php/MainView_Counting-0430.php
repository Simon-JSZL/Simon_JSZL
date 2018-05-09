<?php
$arr = array('name' => 'zhangsan' ,'age'=>'20','id'=>1965456 );
echo json_encode($arr) ;
?>
/*class Totalcount                                                                                                       //公共的数据链接、sql执行和日期查找封包
{
    public $dbHost = "localhost";                                                                                      //数据库地址
    public $wangon;                                                                                             //机台名称，通过chosentable获得
    //public $startdate = '2018-04-11';                                                                                  //默认的日期
    public $startdate;
    public $conn;
    public $params = array();
    public $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    public $connectionInfo = array("UID" =>"", "PWD" => "", "Database" => "AnalyzedData", 'CharacterSet' => "utf-8");
    public $runningdate;                                                                                               //一个保存前五个工作日日期的数组
    function __construct()
    {
        $this->wangon=$_POST["subwangon"];
        $this->startdate=date("Y-m-d",strtotime("-1 day"));
        $this->conn = sqlsrv_connect($this->dbHost, $this->connectionInfo);                                             //连接数据库的构造函数
        if ($this->conn == false) {
            echo "连接至服务器数据库失败";
            die(print_r(sqlsrv_errors(), true));}
    }
    public function findlastday()                                                                                             //查找最近的一个工作日
    {
        $lastday=$this->startdate;
        $wangon=$this->wangon;
        $sql_searchlastday = "select count(1) as count from dbo.Kexin_WY where convert(varchar(10),Createtime,120) = '" . $lastday . "' and Wangonname = '" . $wangon . "'";
        $query_searchlastday = sqlsrv_query($this->conn, $sql_searchlastday, $this->params, $this->options);
        while ($row_lastday = sqlsrv_fetch_array($query_searchlastday)) {
            if ($row_lastday['count'] == 0) {
                $lastday = date("Y-m-d", (strtotime($lastday) - 3600 * 24));
            }
            else if($row_lastday['count']!=0)
                break;
        }
        return $lastday;
    }
    public function find5days()                                                                                       //查找最近的五个工作日的日期
    {
        $i = 0;
        $j = 0;
        $back5days=0;
        $currentdate=$this->findlastday();
        $wangon=$this->wangon;
        $runningdate=$this->runningdate;
        while (1)
        {
            $sql_searchdate = "select top 1 [Id] as ID,count(1) as count from dbo.Kexin_WY where convert(varchar(10),Createtime,120) = '" . $currentdate . "' and Wangonname = '" . $wangon . "'
            group by [Id]";
            $query_searchdate = sqlsrv_query($this->conn, $sql_searchdate, $this->params, $this->options);
            while ($row_date = sqlsrv_fetch_array($query_searchdate)) {
                if ($row_date['count'] != 0) {
                    $back5days++;                                                                                       //查询不到车号表示非工作日，继续向前查找
                    $runningdate[$i++] = $currentdate;                                                                  //五个工作日的日期保存在一个数组中
                }                                                                                                       //当数据库中的作废数据不足5日时因数组中不足5个下标，会报错
                $j=$row_date['ID'];
            }
            $currentdate= date("Y-m-d", (strtotime($currentdate) - 3600 * 24));
            if (($back5days >= 5)||($j<=181))
             break;
        }
    return $runningdate;
    }
}
function sqlexec($sql, $key)                                                                               //通用的sql执行方法
{
    $totalcount= new Totalcount();
    $query = sqlsrv_query($totalcount->conn, $sql, $totalcount->params, $totalcount->options);
    while ($row_eachline = sqlsrv_fetch_array($query)) {
        return $row_eachline[$key];
    }

}
function runsql_singleday($currentdate,$wangon){                                                                                           //前一日作废统计
	$begindate_single = $currentdate;
	$wangon = $wangon;
    $sql_MaxK = "select top 1 Maxk as maxk ,COUNT(1) as count
from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) = '" . $begindate_single . "' and Wangonname = '" . $wangon . "'
group by MaxK
order by count DESC";                                                                                                 //前一日报错最多K位

    $sql_MaxM = "select top 1 MaxM as maxM ,COUNT(1) as count
from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) = '" . $begindate_single . "' and Wangonname = '" . $wangon . "'
group by MaxM
order by count DESC";                                                                                                 //前一日报错最多区域

    $sql_AVGTotal = "select AVG(Totalfail) as AVGTotal from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) = '" . $begindate_single . "' and Wangonname = '" . $wangon . "'";          //前一日报错总数的平均数

    $sql_AVGSer = "select AVG(Serfail) as AVGSer from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) = '" . $begindate_single . "' and Wangonname = '" . $wangon . "'";          //前一日严重废总数的平均数

    $sql_AVGPsn = "select AVG(Psnnum) as AVGPsn from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) = '" . $begindate_single . "' and Wangonname = '" . $wangon . "'";          //前一日三仓总数的平均数

    $singdayresult = array('maxk'=>sqlexec($sql_MaxK, 'maxk'),
        'maxK_count'=>sqlexec($sql_MaxK, 'count'),
        'maxM'=>sqlexec($sql_MaxM, 'maxM'),
        'maxM_count'=>sqlexec($sql_MaxM, 'count'),
        'AVGTotal'=>sqlexec($sql_AVGTotal, 'AVGTotal'),
        'AVGSer'=>sqlexec($sql_AVGSer, 'AVGSer'),
        'AVGPsn'=>sqlexec($sql_AVGPsn, 'AVGPsn'));
    return $singdayresult;
}

function fivedaystotal()                                                                                               //五个工作日总体的作废统计
{
    $checkingdate = new Totalcount();
    @ $begindate = $checkingdate->find5days()[4];
    $enddate = $checkingdate->find5days()[0];
    $wangon = $checkingdate->wangon;
    $sql_MaxK = "select top 1 Maxk as maxk ,COUNT(1) as count
from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "' and Wangonname = '" . $wangon . "'
group by MaxK
order by count DESC";                                                                                                 //五个工作日内报错最多K位

    $sql_MaxM = "select top 1 MaxM as maxM ,COUNT(1) as count
from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "' and Wangonname = '" . $wangon. "'
group by MaxM
order by count DESC";                                                                                                 //五个工作日内报错最多区域

    $sql_AVGTotal = "select AVG(Totalfail) as AVGTotal from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "' and Wangonname = '" . $wangon . "'";//五个工作日内报错总数的平均数

    $sql_AVGSer = "select AVG(Serfail) as AVGSer from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "' and Wangonname = '" . $wangon  . "'";//五个工作日内严重废总数的平均数

    $sql_AVGPsn = "select AVG(Psnnum) as AVGPsn from dbo.Kexin_WY
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "' and Wangonname = '" . $wangon  . "'";//五个工作日内三仓总数的平均数

    $fivedayresult = array('maxk'=>sqlexec($sql_MaxK, 'maxk'),
        'maxK_count'=>sqlexec($sql_MaxK, 'count'),
        'maxM'=>sqlexec($sql_MaxM, 'maxM'),
        'maxM_count'=>sqlexec($sql_MaxM, 'count'),
        'AVGTotal'=>sqlexec($sql_AVGTotal, 'AVGTotal'),
        'AVGSer'=>sqlexec($sql_AVGSer, 'AVGSer'),
        'AVGPsn'=>sqlexec($sql_AVGPsn, 'AVGPsn'));
    print_r($fivedayresult);
    return $fivedayresult;
}
fivedaystotal();
function fivedaycount()                                                                                                //前五个工作日每天的作废数据，保存在二维数组中
{
    $totalcount= new Totalcount();
    $fiveday=$totalcount->find5days();
    $wangon=$totalcount->wangon;
    for ($i=4;$i>=0;$i--)
    {
        @ $current=$fiveday[$i];
        $j=4-$i;
        $fivedaydetail[$j]=runsql_singleday($current,$wangon);
    }
    print_r($fivedaydetail);
    return $fivedaydetail;
}
fivedaycount();

?>*/
