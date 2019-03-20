<?php
include ('./CountingFail_Wagon.php');
include ('./ReturnProcedure.php');
function MachineId(){
    //$MachineId=$_GET['machineId'];
    $MachineId='J5';
    return $MachineId;
}
function FindLast2Wagon($MachineId)
{
    $WagonInfo = [];
    $ConnInfo = new ConnectInfo();
    $Procedure = returnProcedure($MachineId)['Procedure'];
    $ProcedureName = "";
    switch($Procedure){
        case 0:$ProcedureName = "W1";break;
        case 1:$ProcedureName = "W2";break;
        default:break;
    }
    $sql = "select WangonName as WagonName, CreateTime_".$ProcedureName." as CreateTime from dbo.AllIndex where CreateTime_".$ProcedureName." in
    (select top 2 CreateTime_".$ProcedureName." from dbo.AllIndex where MachineId_".$ProcedureName." = '".$MachineId."'
                                          order by CreateTime_".$ProcedureName." DESC )";
    $query = $ConnInfo->returnQuery($sql);
    while($row = sqlsrv_fetch_array($query)) {
        $WagonInfo[] = $row;
        //$WagonNames[]['CreateTime'] = $row[1];
    }
    //print_r($WagonInfo[1]['CreateTime']);
    return $WagonInfo;
}
function LastWagonFails(){
    $MachineId = MachineId();
    $WagonName = FindLast2Wagon($MachineId)[1]['WagonName'];
    $WagonFail = new CountingFailWagon();
    $LastWagonGenFail = $WagonFail->generalfail($MachineId,$WagonName);
    $LastWagonConFail = $WagonFail->confail($MachineId,$WagonName);
    $LastWagonTypFail = $WagonFail->typfail($MachineId,$WagonName);
    $result = array("WagonName"=>$WagonName)+array("LastWagonGenFail"=>$LastWagonGenFail) + array("LastWagonConFail"=>$LastWagonConFail)+array("LastWagonTypFail"=>$LastWagonTypFail);
    return $result;
}

//FindLast2Wagon(MachineId());
print_r(LastWagonFails());
//echo json_encode(LastWagonFails());