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