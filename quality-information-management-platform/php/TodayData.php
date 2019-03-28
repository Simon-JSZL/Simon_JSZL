<?php
include ('./CountingFail_Wagon.php');
include ('./ReturnProcedure.php');
function MachineId(){
    $MachineId=$_GET['machineId'];
    //$MachineId='J5';
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
    $WagonName = FindLast2Wagon($MachineId)[count(FindLast2Wagon($MachineId))-1]['WagonName'];
    $CreateTime = FindLast2Wagon($MachineId)[count(FindLast2Wagon($MachineId))-1]['CreateTime']->format('H:i');
    $WagonFail = new CountingFailWagon();
    $LastWagonGenFail = $WagonFail->generalfail($MachineId,$WagonName);
    $LastWagonConFail = $WagonFail->confail($MachineId,$WagonName);
    $LastWagonTypFail = $WagonFail->typfail($MachineId,$WagonName);
    $result = array("WagonName"=>$WagonName)+array("CreateTime"=>$CreateTime)+array("LastWagonGenFail"=>$LastWagonGenFail) + array("LastWagonConFail"=>$LastWagonConFail)+array("LastWagonTypFail"=>$LastWagonTypFail)+array("AcrossTypFails"=>AcrossTypFail());
    return $result;
}
function AcrossTypFail(){
    $MachineId = MachineId();
    $AcrossTypFails = array();
    if(count(FindLast2Wagon($MachineId))<=2)
        return 0;
    else{
    $WagonInfo1 =  FindLast2Wagon($MachineId)[0];
    $WagonInfo2 =  FindLast2Wagon($MachineId)[1];
    $WagonName1 = $WagonInfo1['WagonName'];
    $WagonName2 = $WagonInfo2['WagonName'];
    $CreateTime1 = $WagonInfo1['CreateTime']->format('H:i');
    $CreateTime2 = $WagonInfo2['CreateTime']->format('H:i');
    $WagonFail = new CountingFailWagon();
    $TypFail1 = $WagonFail->typfail($MachineId, $WagonName1);
    $TypFail2 = $WagonFail->typfail($MachineId, $WagonName2);
    for($i = 0;$i<3;$i++){
        $Max_Pos1 = $TypFail1[$i]['Max_Pos'];
        $Max_Area1 = $TypFail1[$i]['Max_Area'];
        for($j = 0;$j<3;$j++) {
            $Max_Pos2 = $TypFail2[$j]['Max_Pos'];
            $Max_Area2 = $TypFail2[$j]['Max_Area'];
            if($Max_Pos1 == $Max_Pos2 and $Max_Area1 == $Max_Area2 ){
                $AcrossTypFail['WagonName1'] = $WagonName1;
                $AcrossTypFail['WagonName2'] = $WagonName2;
                $AcrossTypFail['CreateTime1'] = $CreateTime1;
                $AcrossTypFail['CreateTime2'] = $CreateTime2;
                $AcrossTypFail['Max_Pos1'] = $Max_Pos1;
                $AcrossTypFail['Max_Pos2'] = $Max_Pos2;
                $AcrossTypFail['Max_Area1'] = $Max_Area1;
                $AcrossTypFail['Max_Area2'] = $Max_Area2;
                $AcrossTypFail['Image1'] = $TypFail1[$i]['Image'];
                $AcrossTypFail['Image2'] = $TypFail2[$j]['Image'];
                $AcrossTypFail['Max_Num1'] = $TypFail1[$i]['Max_Num'];
                $AcrossTypFail['Max_Num2'] = $TypFail2[$j]['Max_Num'];
                $AcrossTypFails[]= $AcrossTypFail;
            }
        }
    }
    return $AcrossTypFails;
    }
}
//FindLast2Wagon(MachineId());
//print_r(LastWagonFails());
echo json_encode(LastWagonFails());
//print_r(LastWagonFails());