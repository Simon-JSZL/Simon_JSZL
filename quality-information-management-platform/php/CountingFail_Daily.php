<?php
include('./ConnectInfo.php');
class CountingFailDaily
{
    public function runsql_singleday($currentdate,$machineId){
        $TableName='dbo.GeneralFail_'.$machineId;
        $ConnInfo=new ConnectInfo();
        $sql_MaxK = "select top 1 Maxk as maxk ,COUNT(1) as count
from ".$TableName."
where convert(varchar(10),Createtime,120) = '" . $currentdate . "'
group by MaxK
order by count DESC";                                                                                                 //一日报错最多K位

        $sql_MaxM = "select top 1 MaxM as maxM ,COUNT(1) as count
from ".$TableName."
where convert(varchar(10),Createtime,120) = '" . $currentdate . "'
group by MaxM
order by count DESC";                                                                                                 //一日报错最多区域

        $sql_AVG = "select AVG(Totalfail) as AVGTotal,AVG(Serfail) as AVGSer,AVG(Psnnum) as AVGPsn from ".$TableName."
where convert(varchar(10),Createtime,120) = '" . $currentdate . "'";

        $row_MaxK=$ConnInfo->returnRow($sql_MaxK);
        $row_MaxM=$ConnInfo->returnRow($sql_MaxM);
        $row_Avg=$ConnInfo->returnRow($sql_AVG);
        $singdayresult = array('maxk'=>$row_MaxK['maxk'],
            'maxK_count'=>$row_MaxK['count'],
            'maxM'=>$row_MaxM['maxM'],
            'maxM_count'=>$row_MaxM['count'],
            'AVGTotal'=>$row_Avg['AVGTotal'],
            'AVGSer'=>$row_Avg['AVGSer'],
            'AVGPsn'=>$row_Avg['AVGPsn'],
            'CurrentDate'=>$currentdate);
        return $singdayresult;
    }

    public function runsql_total($begindate,$enddate,$machineId){//一段时间内总体的作废统计
        $TableName='dbo.GeneralFail_'.$machineId;
        $ConnInfo=new ConnectInfo();
        $sql_MaxK = "select top 1 Maxk as maxk ,COUNT(1) as count
from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "'
group by MaxK
order by count DESC";                                                                                                 //五个工作日内报错最多K位

        $sql_MaxM = "select top 1 MaxM as maxM ,COUNT(1) as count
from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "'
group by MaxM
order by count DESC";                                                                                                 //五个工作日内报错最多区域

        $sql_AVG = "select AVG(Totalfail) as AVGTotal,AVG(Serfail) as AVGSer,AVG(Psnnum) as AVGPsn from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "'";//五个工作日内报错总数的平均数
        $row_MaxK=$ConnInfo->returnRow($sql_MaxK);
        $row_MaxM=$ConnInfo->returnRow($sql_MaxM);
        $row_Avg=$ConnInfo->returnRow($sql_AVG);
        $totalresult = array('maxk_total'=>$row_MaxK['maxk'],
            'maxK_count_total'=>$row_MaxK['count'],
            'maxM_total'=>$row_MaxM['maxM'],
            'maxM_count_total'=>$row_MaxM['count'],
            'AVGTotal_total'=>$row_Avg['AVGTotal'],
            'AVGSer_total'=>$row_Avg['AVGSer'],
            'AVGPsn_total'=>$row_Avg['AVGPsn']);
        return $totalresult;
    }

    public function runsql_eachwangon($begindate,$enddate,$machineId){
        $j=0;
        $arr=array();
        $TableName='dbo.GeneralFail_'.$machineId;
        $ConnInfo=new ConnectInfo();
        $sql="select WangonName as wangon,Totalfail as totalfail,Serfail as serfail,Psnnum as psnnum,convert(varchar(20),Createtime,120) as createtime from ".$TableName."
where convert(varchar(10),Createtime,120) between '" . $begindate . "' and '" . $enddate . "'
order by Createtime";
        $query = $ConnInfo->returnQuery($sql);
        while ($row_eachline = sqlsrv_fetch_array($query)) {
            $arr[$j] = array('crtime_wangon'=>$row_eachline['createtime'],
                'tablename'=>$row_eachline['wangon'],
                'totalfail_wangon'=>$row_eachline['totalfail'],
                'serfail_wangon'=>$row_eachline['serfail'],
                'psnnum_wangon'=>$row_eachline['psnnum']);
            $j++;
        }
        return $arr;
    }
}