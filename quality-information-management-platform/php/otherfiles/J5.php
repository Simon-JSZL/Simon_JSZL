﻿<?php
class commondate{
    public $dbHost_Jitai = "localhost";
    public $dbHost_Server = "localhost";
    public $uid_Jitai = "";
    public $uid_Server = "";
    public $pwd_Jitai = "";
    public $pwd_Server = "";
    public $dbName_Jitai = 'DZVS';
    public $dbName_Server = 'AnalyzedData';
    public $conn_Jitai;
    public $conn_Server;
    public $charset = 'utf-8';
    public $currentdate;
    public $connectionInfo_Jitai;
    public $connectionInfo_Server;
    public $params = array();
    public $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    public $macroTable;
    public $machineId;
    public $procedure;
    public function  __construct()
    {
        $this->machineId = 'W10#2';
        $this->macroTable = 'dbo.ModelMacroLog_339';
        $this->procedure = 'W1';
        //$this->currentdate = date("Y-m-d", strtotime("-1 day"));
        $this->currentdate = '2014-06-21';                                          //获取前一天日期
        $this->connectionInfo_Jitai = array("UID" => $this->uid_Jitai, "PWD" => $this->pwd_Jitai, "Database" => $this->dbName_Jitai, 'CharacterSet' => $this->charset);
        $this->connectionInfo_Server = array("UID" => $this->uid_Server, "PWD" => $this->pwd_Server, "Database" => $this->dbName_Server, 'CharacterSet' => $this->charset);
        $this->conn_Jitai=sqlsrv_connect($this->dbHost_Jitai,$this->connectionInfo_Jitai);
        $this->conn_Server=sqlsrv_connect($this->dbHost_Server,$this->connectionInfo_Server);
        if($this->conn_Jitai == false)
        {
            sqlsrv_close($this->conn_Jitai);
            exit;
        }
        if($this->conn_Server == false)
        {
            sqlsrv_close($this->conn_Jitai);
            exit;
        }
    }
    public function findlastday()                                                                                     //查找最近的一个工作日
    {
        $count=0;
        $lastday=$this->currentdate;
        $stopdate=strtotime("2014-01-01 00:00:00");
        while(1)
        {
            $sql_searchlastday = "select count(1) as count from dbo.Indextable
where convert(varchar(10),Createtime,120) = '" . $lastday . "'";
            $query_searchlastday = sqlsrv_query($this->conn_Jitai, $sql_searchlastday, $this->params, $this->options);
            while ($row_lastday = sqlsrv_fetch_array($query_searchlastday)) {
                if ($row_lastday['count'] == 0) {
                $lastday = date("Y-m-d", (strtotime($lastday."-1 day")));
            }
            else if($row_lastday['count']!=0)
                $count++;
            }
        if($count>0||(strtotime($lastday)<$stopdate))
            break;
        }
        return $lastday;
    }
    public function returnMacroName($MacroId){//返回宏区域名称
        $sql = "select top 1 MacroTitle as MacroName from ".$this->macroTable."
                  where MacroID=".$MacroId;
        $query = sqlsrv_query($this->conn_Jitai, $sql, $this->params, $this->options);
        while ($row = sqlsrv_fetch_array($query)) {
            return $row['MacroName'];
        }
    return $MacroId;
    }
}
function isInSameCol($sheet1,$sheet2){//检查两开是否在同一列，如在返回列数，不在返回false
    for($i=0;$i<5;$i++)
    {
        if(($sheet1>=7*$i+1)&&($sheet1<=7*$i+7)&&($sheet2>=7*$i+1)&&($sheet2<=7*$i+7)){
            return $i+1;}
    }
    return false;
}
function ExtractComFail()
{
   $extractdate=new commondate();
   $lastday=$extractdate->findlastday();
   $conn_Jitai=$extractdate->conn_Jitai;
   $conn_Server=$extractdate->conn_Server;
   $params=$extractdate->params;
   $options=$extractdate->options;
   $faillist[]=array();
   $sql_searchindex = "select tablename,CreateTime from dbo.Indextable
where convert(varchar(10),Createtime,120) = '" . $lastday . "'";
   $query_searchindex = sqlsrv_query($conn_Jitai, $sql_searchindex, $params, $options);
    @ $row_count = sqlsrv_num_rows($query_searchindex);
    if ($row_count === false) {
        sqlsrv_close($conn_Jitai);
        exit;
    } else {
        $i = 0;
        while ($row_eachwagon = sqlsrv_fetch_array($query_searchindex)) {
            $sql_total = "select COUNT(1) as count from dbo." . $row_eachwagon['tablename'];                          //报废总数
            $sql_ser = "select COUNT(1) as count from dbo." . $row_eachwagon['tablename'] . " where Reserve2=2";     //报出严重废总数
            $sql_psn = "select count(distinct PSN) as psnnum from dbo." . $row_eachwagon['tablename'] . " where Reserve2=2";//打仓数
            $sql_maxk = "select top 1 FormatPos,COUNT(*) as count from dbo." . $row_eachwagon['tablename'] . "       
                  group by FormatPos
                  order by count DESC";//作废最多K位
            $sql_maxM = "select top 1 MacroIndex as MacroId from dbo." . $row_eachwagon['tablename'] . "
                  group by MacroIndex
                  order by count(*) DESC";//作废最多区域
            $query_total = sqlsrv_query($conn_Jitai, $sql_total, $params, $options);
            $query_ser = sqlsrv_query($conn_Jitai, $sql_ser, $params, $options);
            $query_psn = sqlsrv_query($conn_Jitai, $sql_psn, $params, $options);
            $query_maxk = sqlsrv_query($conn_Jitai, $sql_maxk, $params, $options);
            $query_maxM = sqlsrv_query($conn_Jitai, $sql_maxM, $params, $options);
            $faillist[$i]['WangonName'] = substr($row_eachwagon['tablename'], 1, 7);
            $faillist[$i][] = $row_eachwagon['CreateTime']->format('Y/m/d H:i:s');
            $row_total=sqlsrv_fetch_array($query_total);
            $row_ser=sqlsrv_fetch_array($query_ser);
            $row_psn=sqlsrv_fetch_array($query_psn);
            $row_maxk=sqlsrv_fetch_array($query_maxk);
            $row_maxM=sqlsrv_fetch_array($query_maxM);
            $faillist[$i][] = $row_total['count'];
            $faillist[$i][] = $row_ser['count'];
            $faillist[$i][] = $row_psn['psnnum'];
            $faillist[$i][] = $row_maxk['FormatPos'];
            $faillist[$i][] = $row_maxM['MacroId'];
            $index[$i]['WangonName'] = substr($row_eachwagon['tablename'], 1, 7);
            $index[$i][] = $row_eachwagon['CreateTime']->format('Y/m/d H:i:s');
            $index[$i][] = $extractdate->machineId;
            $i++;
        }
    }
    for ($temp = 0; $temp < count($faillist); $temp++) {
    	$MacroName=trim($extractdate->returnMacroName($faillist[$temp][5]));
        $sql_insertFail = "insert into dbo.GeneralFail_".$extractdate->machineId."([WangonName],[CreateTime],[TotalFail],[SerFail],[PsnNum],[MaxK],[MaxM])
values('" . $faillist[$temp]['WangonName'] . "','" . $faillist[$temp][0] . "','" . $faillist[$temp][1] . "','" . $faillist[$temp][2] . "','" . $faillist[$temp][3] . "','" . $faillist[$temp][4] . "','" . $MacroName . "')";
        $sql_insertIndex = "insert into dbo.AllIndex([WangonName],[CreateTime_".$extractdate->procedure."],[MachineId_".$extractdate->procedure."])
values('".$index[$temp]['WangonName']."','".$index[$temp][0]."','".$index[$temp][1]."')";
        $sql_updateIndex = "update dbo.AllIndex set [CreateTime_".$extractdate->procedure."] = '".$index[$temp][0]."' , [MachineId_".$extractdate->procedure."] = '".$index[$temp][1]."' where WangonName = '".$index[$temp]['WangonName']."'";
        $sql_checkifexist = "select count(1) as checksum from dbo.AllIndex where WangonName = '".$index[$temp]['WangonName']."'";
        $query_checkifexist = sqlsrv_query($conn_Server, $sql_checkifexist, $params, $options);
        $checkifexist = sqlsrv_fetch_array($query_checkifexist);
        if($checkifexist['checksum']==0)
        sqlsrv_query($conn_Server, $sql_insertIndex, $params, $options);
        else
        sqlsrv_query($conn_Server, $sql_updateIndex, $params, $options);
        sqlsrv_query($conn_Server, $sql_insertFail, $params, $options);  	
    }
}

function ExtractConFail(){//查找并向服务器端数据库插入连续废
    $extractCon=new commondate();
    $lastday=$extractCon->findlastday();
    $conn_Jitai=$extractCon->conn_Jitai;
    $conn_Server=$extractCon->conn_Server;
    $params=$extractCon->params;
    $options=$extractCon->options;
    $confail_all[]=array();//用来保存连续废的二维数组
    $j=0;
    $ConCol=0;//连续废所在的列
    $sql_searchindex = "select tablename from dbo.Indextable
where convert(varchar(10),Createtime,120) = '" . $lastday . "'";
    $query_searchindex = sqlsrv_query($conn_Jitai, $sql_searchindex, $params, $options);
    @ $row_count = sqlsrv_num_rows($query_searchindex);
    if ($row_count === false) {
        sqlsrv_close($conn_Jitai);
        exit;
    } else {
        while ($row_eachwagon = sqlsrv_fetch_array($query_searchindex)) {
            unset($confail);
            $confail[0]=array(0,0,0);
            $i=0;
            $count=0;
            $sql_confail = "select PSN as psn,FormatPos as pos,MacroIndex as area,[Index] as Id from dbo." . $row_eachwagon['tablename'] ." order by PSN";
            $query_confail = sqlsrv_query($conn_Jitai, $sql_confail, $params, $options);
            while($row_confail = sqlsrv_fetch_array($query_confail))
            {
                if($confail[$i][0]==0&&$confail[$i][1]==0&&$confail[$i][2]==0)
                {
                    $confail[$i]=$row_confail;
                }
                else if($confail[$i][0]==$row_confail['psn'])
                {
                    continue;
                }
                else if(($confail[$i][0]+3>=$row_confail['psn'])){
                    if((isInSameCol($confail[$i][1],$row_confail['pos'])==true)&&($confail[$i][2]==$row_confail['area'])){
                        $count++;
                        $i++;
                        $confail[$i]=$row_confail;
                        if($count>=3)
                            $ConCol=isInSameCol($confail[$i][1],$row_confail['pos']);
                    }
                    else{
                        $confail[$i]=$row_confail;
                    }
                }
                else if($confail[$i][0]+3<$row_confail['psn']){
                    if($count<10){
                        unset($confail);
                        $i=0;
                        $count=0;
                        $confail[$i]=$row_confail;
                    }
                    else if($count>=10){//符合连续废条件
                        $confail_all[$j]=array(//插入confail表的作废信息
                            'WangonName'=>substr($row_eachwagon['tablename'], 1, 7),
                            'ConNumber'=>$i+1,
                            'StartPsn'=>$confail[0][0],
                            'EndPsn'=>$confail[$i][0],
                            'ConCol'=>$ConCol,
                            'ConArea'=>$confail[0][2]);
                        $sql_getImage1 = "select ErrorImage as image from dbo." . $row_eachwagon['tablename'] . " where [index]=" . $confail[0]['Id'];
                        $query_getImage1 = sqlsrv_query($conn_Jitai, $sql_getImage1, $params, $options);
                        $image1 = bin2hex(sqlsrv_fetch_array($query_getImage1)['image']);
                        $sql_getImage2 = "select ErrorImage as image from dbo." . $row_eachwagon['tablename'] . " where [index]=" . $confail[count($confail)-1]['Id'];
                        $query_getImage2 = sqlsrv_query($conn_Jitai, $sql_getImage2, $params, $options);
                        $image2 = bin2hex(sqlsrv_fetch_array($query_getImage2)['image']);
                        $confail_image[$j]=array(
                            'ConImage1'=>$image1,
                            'ConImage2'=>$image2);
                        $j++;
                        unset($confail);
                        $i=0;
                        $count=0;
                        $confail[$i]=$row_confail;
                    }
                }
            }
        }
    }
    for($temp=0;$temp<count($confail_all);$temp++){
        $sql_insertCon = "insert into dbo.ConFail_".$extractCon->machineId."([WangonName],[ConNumber],[StartPsn],[EndPsn],[ConCol],[ConArea])
values('" . $confail_all[$temp]['WangonName'] . "','" . $confail_all[$temp]['ConNumber'] . "','" . $confail_all[$temp]['StartPsn'] . "','" . $confail_all[$temp]['EndPsn'] . "','" . $confail_all[$temp]['ConCol'] . "','" . $extractCon->returnMacroName($confail_all[$temp]['ConArea']) . "')";
        $sql_insertImage="insert into dbo.ConImage_".$extractCon->machineId."([ImageId],[ConImage1],[ConImage2])
values(@@IDENTITY,'".$confail_image[$temp]['ConImage1']."','".$confail_image[$temp]['ConImage2']."')";
        $sql_checkifexist = "select count(1) as checksum from dbo.ConFail_".$extractCon->machineId." where WangonName ='".$confail_all[$temp]['WangonName']."' and StartPsn = '".$confail_all[$temp]['StartPsn']."' and EndPsn = '".$confail_all[$temp]['EndPsn']."'";
        $query_checkifexist = sqlsrv_query($conn_Server, $sql_checkifexist, $params, $options);
        while ($checkifexist = sqlsrv_fetch_array($query_checkifexist)) {
            if ($checkifexist['checksum'] == 0) {
                sqlsrv_query($conn_Server, $sql_insertCon, $params, $options);
                sqlsrv_query($conn_Server, $sql_insertImage, $params, $options);
            }
        }
    }
}

function ExtractTypicalFail(){
    $extractTyp=new commondate();
    $lastday=$extractTyp->findlastday();
    $conn_Jitai=$extractTyp->conn_Jitai;
    $conn_Server=$extractTyp->conn_Server;
    $params=$extractTyp->params;
    $options=$extractTyp->options;
    $typfail=array();
    $typimage=array();
    $row=0;
    $sql_searchindex = "select tablename from dbo.Indextable
where convert(varchar(10),Createtime,120) = '" . $lastday . "'";
    $query_searchindex = sqlsrv_query($conn_Jitai, $sql_searchindex, $params, $options);
    @ $row_count = sqlsrv_num_rows($query_searchindex);
    if ($row_count === false) {
        sqlsrv_close($conn_Jitai);
        exit;
    } else {
        while ($row_eachwagon = sqlsrv_fetch_array($query_searchindex)) {
            $typfail[$row]['WangonName']=substr($row_eachwagon['tablename'], 1, 7);
            $typimage[$row]['WangonName']=substr($row_eachwagon['tablename'], 1, 7);
            $sql_typfail="select top 3 count(1) as count,FormatPos as pos,MacroIndex as area,avg(Reserve3) as dimension from  dbo." . $row_eachwagon['tablename'] ."
where FormatPos!=15 and FormatPos!=8 and FormatPos!=22
group by FormatPos,MacroIndex
order by count DESC";
            $query_typfail = sqlsrv_query($conn_Jitai, $sql_typfail, $params, $options);
            while($row_typfail = sqlsrv_fetch_array($query_typfail)){
                $typfail[$row][]=$row_typfail['pos'];
                $typfail[$row][]=$row_typfail['area'];
                $typfail[$row][]=$row_typfail['count'];
                $typfail[$row][]=$row_typfail['dimension'];
                $sql_getimage="select top 1 ErrorImage as image from dbo." . $row_eachwagon['tablename'] ."
            where FormatPos = ".$row_typfail['pos']." and MacroIndex = ".$row_typfail['area']."
            order by Reserve3 DESC";
                $query_getImage= sqlsrv_query($conn_Jitai, $sql_getimage, $params, $options);
                $typimage[$row][]=bin2hex(sqlsrv_fetch_array($query_getImage)['image']);
            }
        $row++;
        }
    }
    for($temp=0;$temp<count($typfail);$temp++){
        $sql_insertTyp = "insert into dbo.TypicalFail_".$extractTyp->machineId."([WangonName],[Max_Pos1],[Max_Area1],[Max_Num1],[Avg_Dim1],[Max_Pos2],[Max_Area2],[Max_Num2],[Avg_Dim2],[Max_Pos3],[Max_Area3],[Max_Num3],[Avg_Dim3])
values('".$typfail[$temp]['WangonName']."','".$typfail[$temp][0]."','".$extractTyp->returnMacroName($typfail[$temp][1])."','".$typfail[$temp][2]."','".$typfail[$temp][3]."','".$typfail[$temp][4]."','".$extractTyp->returnMacroName($typfail[$temp][5])."','".$typfail[$temp][6]."','".$typfail[$temp][7]."','".$typfail[$temp][8]."','".$extractTyp->returnMacroName($typfail[$temp][9])."','".$typfail[$temp][10]."','".$typfail[$temp][11]."')";
        $sql_insertImage="insert into dbo.TypicalImage_".$extractTyp->machineId."([WangonName],[TypImage1],[TypImage2],[TypImage3])
values ('".$typimage[$temp]['WangonName']."','".$typimage[$temp][0]."','".$typimage[$temp][1]."','".$typimage[$temp][2]."')";
            sqlsrv_query($conn_Server, $sql_insertTyp, $params, $options);
            sqlsrv_query($conn_Server, $sql_insertImage, $params, $options);
    }
}
ExtractComFail();
ExtractConFail();
ExtractTypicalFail();


