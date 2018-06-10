<?php
include('./CountingFail_Daily.php');
function startDate(){
    $StartDate=$_GET['StartDate'];
    //$StartDate='2014-06-20';
    return $StartDate;
}
function endDate(){
    $EndDate=$_GET['EndDate'];
    //$EndDate='2014-06-25';
    return $EndDate;
}
function machineId(){
    $MachineId=$_GET['MachineId'];
    //$MachineId='J5';
    return $MachineId;
}

function datesearch_total(){
    $CountFailDaily=new CountingFailDaily();
    $begindate=startDate();
    $enddate=endDate();
    $machineId=machineId();
    $totaldetail=$CountFailDaily->runsql_total($begindate,$enddate,$machineId);
    return $totaldetail;
}
function datesearch_eachwagon(){
    $CountFailDaily=new CountingFailDaily();
    $begindate=startDate();
    $enddate=endDate();
    $machineId=machineId();
    $eachwangondetail=$CountFailDaily->runsql_eachwangon($begindate,$enddate,$machineId);
    return $eachwangondetail;
}
$totalresult=datesearch_total();
$eachwangonresult=datesearch_eachwagon();
$result=array("TotalResult"=>$totalresult)+array("EachWangonResult"=>$eachwangonresult);
echo json_encode($result);