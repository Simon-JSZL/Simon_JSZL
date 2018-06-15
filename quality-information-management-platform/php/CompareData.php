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
function productId(){
    $ProductId=$_GET['ProductId'];
    return $ProductId;
}
function sideId(){
    $SideId=$_GET['SideId'];
    return $SideId;
}
function returnWagon($StartDate,$EndDate,$ProductId,$SideId){
    $ConnInfo=new ConnectInfo();
    $sql="select MachineId from dbo.MachineInfo where ProductId='".$ProductId."' and SideId=".$SideId;
}