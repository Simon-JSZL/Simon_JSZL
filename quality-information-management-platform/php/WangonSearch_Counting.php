<?php
include('./CountingFail_Wagon.php');
function wagonName(){
    $WagonName=$_GET['wangonName'];
    return $WagonName;
}
function returnMachineInfo($WangonName){
    $ConnInfo=new ConnectInfo();
    $sql="select MachineId as MachineId,CreateTime as CreateTime from dbo.AllIndex where WangonName = '".$WangonName."'";
    $query=$ConnInfo->returnQuery($sql);
    $row=sqlsrv_fetch_array($query);
    $MachineId=$row['MachineId'];
    $CreateTime=$row['CreateTime'];
    $sql_machineInfo="select SideId as SideId,ProductId as ProductId from dbo.MachineInfo where MachineId = '".$MachineId."'";
    $query_machineInfo=$ConnInfo->returnQuery($sql_machineInfo);
    $row_machineInfo=sqlsrv_fetch_array($query_machineInfo);
    $SideId=$row_machineInfo['SideId'];
    $ProductId=$row_machineInfo['ProductId'];
    $Info = array("MachineId"=>trim($MachineId),"CreateTime"=>$CreateTime,"SideId"=>$SideId,"ProductId"=>$ProductId);
    return $Info;
}

function returnData(){
    $WangonName=wagonName();
    $Info=returnMachineInfo($WangonName);
    $MachineId=$Info['MachineId'];
    $CountingFailWagon=new CountingFailWagon();
    if($Info['MachineId']==Null){
        $result=0;
        echo json_encode($result);
    }
    else {
        $CreateTime=$Info['CreateTime']->format('Y/m/d H:i');
        $SideId=$Info['SideId'];
        $ProductId=$Info['ProductId'];
        $WangonInfo=array("MachineId"=>$MachineId,"CreateTime"=>$CreateTime,"SideId"=>$SideId,"ProductId"=>$ProductId);
        $GeneralFail = $CountingFailWagon->generalfail($MachineId,$WangonName);
        $ConFail = $CountingFailWagon->confail($MachineId,$WangonName);
        $TypFail = $CountingFailWagon->typfail($MachineId,$WangonName);
        $result = array("WangonInfo"=>$WangonInfo)+array("GeneralFail" => $GeneralFail) + array("ConFail" => $ConFail) + array("TypFail"=> $TypFail);
        echo json_encode($result);
    }
}
returnData();
