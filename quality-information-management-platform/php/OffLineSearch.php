<?php
include('./ConnectInfo.php');
function WagonName(){
    $WagonName=$_GET['WagonName'];
    return $WagonName;
}

function ReturnMachineId($WagonName){
    $ConnInfo = new ConnectInfo();
    $sql_entid = "select ENTID from dbo.QUA_LXYOMES_DETAIL where BATCHNO = '".$WagonName."'
            group by ENTID";
    if(sqlsrv_num_rows($ConnInfo->returnQuery($sql_entid))==0)
        return 0;
    else{
        $query = $ConnInfo ->returnQuery($sql_entid);
        while($EntId = sqlsrv_fetch_array($query)['ENTID']){
            $sql_checkent = "select MachineId from dbo.MachineInfo 
                              where EntId =".$EntId;
            if(sqlsrv_num_rows($ConnInfo->returnQuery($sql_checkent))!=0)
                return array('MachineId' => $ConnInfo->returnRow($sql_checkent)['MachineId'],
                            'EntId' => $EntId);
        }
    }
    return 0;
}

function ReturnData($WagonName){
    $ConnInfo = new ConnectInfo();
    $MachineInfo = ReturnMachineId($WagonName);
    $PageId = 0;
    $i = 0;
    $j = 0;
    $total_score = 0;
    $result = array();
    if($MachineInfo == 0)
        return 0;
    else{
        $MachineId = $MachineInfo['MachineId'];
        $sql_get_detail = "select * from dbo.QUA_LXYOMES_DETAIL where BATCHNO = '".$WagonName."'";
        $query_wagon_detail = $ConnInfo->returnQuery($sql_get_detail);
        while($WagonDetail = sqlsrv_fetch_array($query_wagon_detail)){
            if ($PageId == $WagonDetail['PID']){
                $i++;
            }
            else {
                $PageId = $WagonDetail['PID'];
                if($total_score != 0)
                    $result[$j]['totalscore'] = $total_score;//防止第一行直接赋值时创建一个totalscore为0的一行
                $total_score = 0;
                $i = 0;
                $i++;
                $j++;
            }
            $result[$j]['item'.$i] = $WagonDetail['SCOREITEM'];
            $result[$j]['score'.$i] = $WagonDetail['SCORE'];
            $total_score += $result[$j]['score'.$i];
        }
        $result[$j]['totalscore'] = $total_score;//totalscore赋值操作发生在检测到新的pid时，最后一个pid无法触发该操作，需要在循环结束时单独定义
        $average_score = round(array_sum(array_column($result,'totalscore'))/$j); //round函数，四舍五入


        $result = array('MachineId' => trim($MachineId),'AverageScore' => $average_score) + $result;
        return $result;
    }
}
echo json_encode(ReturnData(WagonName()));
//print_r(ReturnData('10AP564'));