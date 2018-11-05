<?php
include('./ConnectInfo.php');
function startMonth(){
    //$StartMonth=$_GET['StartDate'];
    $StartMonth='2018-02';
    return $StartMonth;
}
function endMonth(){
    //$EndMonth=$_GET['EndDate'];
    $EndMonth='2018-08';
    return $EndMonth;
}
function machineId(){
    //$MachineId=$_GET['MachineId'];
    $MachineId='J5';
    return $MachineId;
}
function searchTerm(){
    //$SearchTerm=$_GET['SearchTerm'];
    $SearchTerm="Totalfail";
    return $SearchTerm;
}
function biggerThan(){
    //$BiggerThan=$_GET['BiggerThan'];
    $BiggerThan=800;
    return $BiggerThan;
}
function lesserThan(){
    //$LesserThan=$_GET['LesserThan'];
    $LesserThan=200;
    return $LesserThan;
}

function returnGeneralResult($BeginDate,$EndDate,$MachineId){//一段时间内生产车次，和符合条件的车次
    $SearchTerm=searchTerm();
    $TableName='dbo.GeneralFail_'.$MachineId;
    $ConnInfo=new ConnectInfo();
    $sql = "select AVG(".$SearchTerm.") as AvgValue,MIN(".$SearchTerm.") as MinValue,MAX(".$SearchTerm.") as MaxValue,COUNT(1) as TotalNum from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $BeginDate . "' and '" . $EndDate . "'";//返回一段时间内生产的车次总数、作废平均数、单车最多及最低作废数
    $row = $ConnInfo->returnRow($sql);
    $totalresult = array(
        'AvgValue' => $row['AvgValue'],
        'MaxValue' => $row['MaxValue'],
        'MinValue' => $row['MinValue'],
        'TotalNum' => $row['TotalNum']);
    return $totalresult;
}
function returnConditionResult($BeginDate,$EndDate,$MachineId,$BiggerThan,$LesserThan){
    $ConditionResult=array();
    $SearchTerm=searchTerm();
    $TableName='dbo.GeneralFail_'.$MachineId;
    $ConnInfo=new ConnectInfo();
    if($BiggerThan==""&&$LesserThan==""){//没有条件查询
        return 0;
    }
    else if($BiggerThan==""&&$LesserThan!=""){//只查小于某值
        $sql="select COUNT(1) as ConditionNum from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $BeginDate . "' and '" . $EndDate . "'
and ".$SearchTerm."<=".$LesserThan;
        $row=$ConnInfo->returnRow($sql);
        $ConditionResult = array(
            'ConditionNum'=>$row['ConditionNum']);
    }
    else if($LesserThan==""&&$BiggerThan!=""){//只查大于某值
        $sql="select COUNT(1) as ConditionNum from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $BeginDate . "' and '" . $EndDate . "'
and ".$SearchTerm.">=".$BiggerThan;
        $row=$ConnInfo->returnRow($sql);
        $ConditionResult = array(
            'ConditionNum'=>$row['ConditionNum']);
    }
    else if($BiggerThan<$LesserThan){//查某个区间内
        $sql = "select COUNT(1) as ConditionNum from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $BeginDate . "' and '" . $EndDate . "'
and ".$SearchTerm.">".$BiggerThan." and ".$SearchTerm."<".$LesserThan;
        $row=$ConnInfo->returnRow($sql);
        $ConditionResult = array(
            'ConditionNum'=>$row['ConditionNum']);
    }
    else if($BiggerThan>$LesserThan){//查区间两端
        $sql1="select COUNT(1) as ConditionNum from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $BeginDate . "' and '" . $EndDate . "'
and ".$SearchTerm."<=".$LesserThan;
        $sql2="select COUNT(1) as ConditionNum from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $BeginDate . "' and '" . $EndDate . "'
and ".$SearchTerm.">=".$BiggerThan;
        $row1=$ConnInfo->returnRow($sql1);
        $row2=$ConnInfo->returnRow($sql2);
        $ConditionResult = array(
            'ConditionNum1'=>$row1['ConditionNum'],
            'ConditionNum2'=>$row2['ConditionNum']);
    }
    else if($BiggerThan==$LesserThan){//查固定的某个值
        $sql="select COUNT(1) as ConditionNum from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $BeginDate . "' and '" . $EndDate . "'
and ".$SearchTerm."=".$BiggerThan;
        $row=$ConnInfo->returnRow($sql);
        $ConditionResult = array(
            'ConditionNum'=>$row['ConditionNum']);
    }
    return $ConditionResult;
}
function checkRunning($date){//检测当前日期是否有生产车次
    $ConnInfo=new ConnectInfo();
    $sql_searchdate = "select count(1) as count from dbo.GeneralFail_".machineId()." where convert(varchar(10),Createtime,120) = '" . $date . "'";
    if ($ConnInfo->returnRow($sql_searchdate)['count'] != 0)
        return true;
    else
        return false;
}
function returnWeekDate($StartMonth,$EndMonth){
    $WeekDate=array();
    for($Month=$StartMonth;strtotime($Month)<=strtotime($EndMonth);$Month=date("Y-m", (strtotime($Month."+1 month")))) {
        $i=1;
        for($Day=date("Y-m-d",(strtotime($Month)));$Day<date("Y-m-d",(strtotime($Month."+1 month")));$Day=date("Y-m-d", (strtotime($Day."+1 day")))){
            $DayOfWeek=date("w",strtotime($Day));
            if($DayOfWeek==0&&$Day!=date("Y-m-d",(strtotime($Month)))){//每周日并且该日不为每月第一天时周计数加一
                $i++;
            }
            if(checkRunning($Day)==true){
                $WeekDate[$Month."-0".$i][]=$Day;
            }
        }
    }
return $WeekDate;
}
function returnMonthData()
{
    $i=0;
    $MonthResult=array();
    for ($Month = startMonth(); strtotime($Month) <= strtotime(endMonth()); $Month = date("Y-m", (strtotime($Month . "+1 month")))) {
        $BeginDate = date("Y-m-d", (strtotime($Month)));
        $EndDate = date("Y-m-d", (strtotime($Month . "+1 month")));
        $SingleMonthResult=returnGeneralResult($BeginDate,$EndDate,machineId());
        if($SingleMonthResult['TotalNum']!=0){
        $MonthResult[$i]=$SingleMonthResult;
        $MonthResult[$i]['Date']=$Month;
        $i++;
        }
    }
return $MonthResult;
}
function returnResult($WeekDate){
    $i=0;
    $GeneralResult=array();
    $ConditionResult=array();
    foreach($WeekDate as $key=>$value){
        $FirstDate=$value[0];
        $LastDate=end($value);
        $GeneralResult[$i]=returnGeneralResult($FirstDate,$LastDate,machineId());
        $GeneralResult[$i]["Date"]=$key;
        $ConditionResult[$i]=returnConditionResult($FirstDate,$LastDate,machineId(),biggerThan(),lesserThan());
        $i++;
    }
    $Result=array("GeneralResult"=>$GeneralResult)+array("ConditionResult"=>$ConditionResult)+array("MonthResult"=>returnMonthData());
    echo json_encode($Result);
    //print_r($Result);
}
returnResult(returnWeekDate(startMonth(),endMonth()));
