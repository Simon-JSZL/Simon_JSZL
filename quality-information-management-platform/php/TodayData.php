<?php
include ('./CountingFail_Wagon.php');
include ('./ReturnProcedure.php');
function MachineId(){
    $MachineId=$_GET['machineId'];
    //$MachineId='J5';
    return $MachineId;
}
function FindLastWagon($MachineId)
{
    $ConnInfo = new ConnectInfo();
    $Procedure = returnProcedure($MachineId)['Procedure'];
    $ProcedureName = "";
    switch($Procedure){
        case 0:$ProcedureName = "W1";break;
        case 1:$ProcedureName = "W2";break;
        default:break;
    }
    $sql = "select WangonName as WagonName from dbo.AllIndex where CreateTime_".$ProcedureName." =
    (select top 1 CreateTime_".$ProcedureName." from dbo.AllIndex where MachineId_".$ProcedureName." = '".$MachineId."'
                                          order by CreateTime_".$ProcedureName." DESC )";
    $WagonName = $ConnInfo->returnRow($sql)['WagonName'];
    return $WagonName;
}
function LastWagonFails(){
    $MachineId = MachineId();
    $WagonName = FindLastWagon($MachineId);
    $WagonFail = new CountingFailWagon();
    $LastWagonGenFail = $WagonFail->generalfail($MachineId,$WagonName);
    $LastWagonConFail = $WagonFail->confail($MachineId,$WagonName);
    $LastWagonTypFail = $WagonFail->typfail($MachineId,$WagonName);
    $result = array("WagonName"=>$WagonName)+array("LastWagonGenFail"=>$LastWagonGenFail) + array("LastWagonConFail"=>$LastWagonConFail)+array("LastWagonTypFail"=>$LastWagonTypFail);
    return $result;
}
//print_r(FindLastWagon(MachineId()));
//print_r(LastWagonFails());
echo json_encode(LastWagonFails());