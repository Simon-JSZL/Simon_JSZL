<?php
include('./CountingFail_Daily.php');
function startDate(){
    $StartDate=$_GET['StartDate'];
    //$StartDate='2014-06-20';
    return $StartDate;
}
function endDate(){
    $EndDate=$_GET['EndDate'];
    //$EndDate='2014-06-24';
    return $EndDate;
}
function productId(){
    $ProductId=$_GET['ProductId'];
    //$ProductId='9607T';
    return $ProductId;
}
function sideId(){
    $SideId=$_GET['SideId'];
    //$SideId=1;
    return $SideId;
}
function returnWagon($StartDate,$EndDate,$ProductId,$SideId){
    $i=0;
    $ConnInfo=new ConnectInfo();
    $CompareResult=array();
    $sql_machine="select MachineId from dbo.MachineInfo where ProductId='".$ProductId."' and SideId=".$SideId;
    $query_machine=$ConnInfo->returnQuery($sql_machine);
    while($row_machine=sqlsrv_fetch_array($query_machine)){
        $j=0;
        $MachineId=$row_machine['MachineId'];
        $CompareResult[$i][$j++]['MachineId']=$MachineId;
        $sql_fail="select CreateTime as CurrentDate,TotalFail as AvgTotal,SerFail as AvgSer,PsnNum as AvgPsn from dbo.SumFail_".$MachineId." where convert(varchar(10),CreateTime,120) between '" . $StartDate . "' and '" . $EndDate . "'";
        $query_fail=$ConnInfo->returnQuery($sql_fail);
        if(sqlsrv_num_rows($query_fail)==false)
            return 0;
        while($row_fail=sqlsrv_fetch_array($query_fail)){
            $CompareResult[$i][$j]['CurrentDate']=$row_fail['CurrentDate']->format('Y/m/d');
            $CompareResult[$i][$j]['AvgTotal']=$row_fail['AvgTotal'];
            $CompareResult[$i][$j]['AvgSer']=$row_fail['AvgSer'];
            $CompareResult[$i][$j]['AvgPsn']=$row_fail['AvgPsn'];
            $j++;
        }
        $i++;
    }
return $CompareResult;
}
$compareData=returnWagon(startDate(),endDate(),productId(),sideId());
$result=array("CompareData"=>$compareData);
echo json_encode($result);
