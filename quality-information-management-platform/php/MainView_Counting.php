<?php
include('./CountingFail_Daily.php');
function MachineId(){
    $MachineId=$_GET['machineId'];
    return $MachineId;
}
function find5days()                                                                                           //查找最近的五个工作日的日期
{
    $back5days=0;
    $ConnInfo=new ConnectInfo();
    $currentdate=date("Y-m-d",strtotime("-1 day"));
    $machineId=MachineId();
    while (1)
    {
        $sql_searchdate = "select count(1) as count from dbo.GeneralFail_".$machineId." where convert(varchar(10),Createtime,120) = '" . $currentdate . "'";
        if ($ConnInfo->returnRow($sql_searchdate)['count'] != 0) {
            $back5days++;
            $runningdate[] = $currentdate;
        }
        $currentdate= date("Y-m-d", (strtotime($currentdate."-1 day")));
        if (($back5days >= 5)||($currentdate == '2014-04-10'))
            break;
    }
    return $runningdate;//倒序:[arr0 => 2018-04-25,arr1=>2018-04-24...]
}

function fivedaycount_total(){
    @ $begindate = find5days()[4];
    $enddate = find5days()[0];
    $machineId = MachineId();
    $CountFailDaily=new CountingFailDaily();
    $totaldetail=$CountFailDaily->runsql_total($begindate,$enddate,$machineId);
    return $totaldetail;
}

function fivedaycount_singleday(){
    $CountFailDaily=new CountingFailDaily();
    $fivedaydetail=array();
    $fiveday=find5days();
    $arraylength=count($fiveday)-1;
    $machineId=MachineId();
    for ($i=$arraylength;$i>=0;$i--)
    {
        @ $current=$fiveday[$i];
        $j=$arraylength-$i;
        $fivedaydetail[$j]=$CountFailDaily->runsql_singleday($current,$machineId);
    }
    return $fivedaydetail;//正序:[arr0=>2018-04-24,arr1=>2018-04-25..]
}
function fivedaycount_eachwangon(){
    $CountFailDaily=new CountingFailDaily();
    @ $begindate = find5days()[4];
    $enddate = find5days()[0];
    $machineId= MachineId();
    $eachwangondetail=$CountFailDaily->runsql_eachwangon($begindate,$enddate,$machineId);
    return $eachwangondetail;
}
$totalresult=fivedaycount_total();
$singleresult=fivedaycount_singleday();
$eachwangonresult=fivedaycount_eachwangon();
$result=array("TotalResult"=>$totalresult) + array("SingleResult"=>$singleresult)+array("EachWangonResult"=>$eachwangonresult);
echo json_encode($result);

