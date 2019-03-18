<?php
function returnProcedure($machineId){
    $ConnInfo=new ConnectInfo();
    $sql = "select * from dbo.MachineInfo where MachineId = '".$machineId."'";
    $row= $ConnInfo->returnRow($sql);
    $Info = array("MachineId"=>$machineId,"Procedure"=>$row['SideId'],"ProductId"=>$row['ProductId']);
    return $Info;
}
