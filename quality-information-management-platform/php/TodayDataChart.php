<?php
include ('./CountingFail_Wagon.php');
include ('./ReturnProcedure.php');
function MachineId(){
    $MachineId=$_GET['machineId'];
    //$MachineId='J5';
    return $MachineId;
}
function Procedure($MachineId){
    $Procedure = returnProcedure($MachineId)['Procedure'];
    $ProcedureName = "";
    switch($Procedure){
        case 0:$ProcedureName = "W1";break;
        case 1:$ProcedureName = "W2";break;
        default:break;
    }
    return $ProcedureName;
}
function FindTodayWagons($MachineId){
    $ConnectInfo = new ConnectInfo();
    $CurrentDate = date("Y-m-d");
    $Procedure = Procedure($MachineId);
    $TodayWagons = array();
    $sql = "select WangonName as WagonName, CreateTime_".$Procedure." as CreateTime from dbo.AllIndex where convert(varchar(10),CreateTime_".$Procedure.",120) = '" . $CurrentDate . "'
    and MachineId_".$Procedure." = '".$MachineId."' order by CreateTime_".$Procedure." DESC";
    $query = $ConnectInfo->returnQuery($sql);
    while($row = sqlsrv_fetch_array($query)) {
        $TodayWagons[] = $row;
    }
    //print_r($WagonInfo[1]['CreateTime']);
    if(count($TodayWagons) == 0){
        //print 0;
        return 0;
    }
    else
        //print $TodayWagons;
        return $TodayWagons;
}

//print_r(FindTodayWagons(MachineId()));

function ReturnTodayFails(){
    $result = array();
    $Wagons = FindTodayWagons(MachineId());
    $MachineId = MachineId();
    $CountingFailWagon = new CountingFailWagon();
    if($Wagons == 0)
        return 0;
    else {
        while ($Wagon = array_pop($Wagons)) {
            $WagonFail = $CountingFailWagon->generalfail($MachineId, $Wagon['WagonName']);
            $Procedure = returnProcedure($MachineId)['Procedure'];
            $result[] = array("WagonName" => trim($Wagon['WagonName']),
                "Procedure" =>$Procedure,
                "CreateTime" => $Wagon['CreateTime']->format('H:i'),
                "TotalFail" => $WagonFail['TotalFail'],
                "SerFail" => $WagonFail['SerFail'],
                "PsnNum" => $WagonFail['PsnNum']);
        }

        return $result;
    }
}

//print_r(ReturnTodayFails());
echo json_encode(ReturnTodayFails());