<?php
include('./CountingFail_Daily.php');
function startDate(){
    $StartDate=$_GET['StartDate'];
    return $StartDate;
}
function endDate(){
    $EndDate=$_GET['EndDate'];
    return $EndDate;
}
function machineId(){
    $MachineId=$_GET['MachineId'];
    return $MachineId;
}
function checkRunning($date){//检测当前日期是否有生产车次
    $ConnInfo=new ConnectInfo();
        $sql_searchdate = "select count(1) as count from dbo.GeneralFail_".machineId()." where convert(varchar(10),Createtime,120) = '" . $date . "'";
        if ($ConnInfo->returnRow($sql_searchdate)['count'] != 0)
            return true;
        else
            return false;
}
function  returnInfo(){
    $machineId= MachineId();
    $CountFailDaily = new CountingFailDaily();
    $Info = $CountFailDaily->returnProcedure($machineId);
    return $Info;
}
function datesearch_total(){
    $CountFailDaily=new CountingFailDaily();
    $begindate=startDate();
    $enddate=endDate();
    $machineId=machineId();
    $totaldetail=$CountFailDaily->runsql_total($begindate,$enddate,$machineId);
    return $totaldetail;
}
function datesearch_single(){
    $CountFailDaily=new CountingFailDaily();
    $begindate=startDate();
    $enddate=endDate();
    $machineId=machineId();
    $SingleDetail=array();
    for($CurrDate=$begindate;strtotime($CurrDate)<=strtotime($enddate);$CurrDate=date("Y-m-d", (strtotime($CurrDate."+1 day"))))
    {
        if(checkRunning($CurrDate)==true)
        $SingleDetail[]=$CountFailDaily->runsql_singleday($CurrDate,$machineId);
    }
    return $SingleDetail;
}
function datesearch_eachwagon(){
    $CountFailDaily=new CountingFailDaily();
    $begindate=startDate();
    $enddate=endDate();
    $machineId=machineId();
    $eachwangondetail=$CountFailDaily->runsql_eachwangon($begindate,$enddate,$machineId);
    return $eachwangondetail;
}
function returnData(){
    $ConnInfo=new ConnectInfo();
    $sql_searchdate = "select count(1) as count from dbo.GeneralFail_".machineId()." where convert(varchar(10),Createtime,120) between '".startDate()."' and '".endDate()."'";
    if ($ConnInfo->returnRow($sql_searchdate)['count'] == 0)
        $result=0;
    else{
        $Info=returnInfo();
        $totalresult=datesearch_total();
        $SingleResult=datesearch_single();
        $eachwangonresult=datesearch_eachwagon();
        $result=array("MachineInfo"=>$Info)+array("TotalResult"=>$totalresult)+array("EachWangonResult"=>$eachwangonresult)+array("SingleResult"=>$SingleResult);
    }
    echo json_encode($result);
}
returnData();