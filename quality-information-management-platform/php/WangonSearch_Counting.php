<?php
class Totalcount                                                                                                       //公共的数据链接、sql执行和日期查找封包
{
    public $dbHost = "localhost";
    public $wangonName;
    public $conn;
    public $params = array();
    public $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    public $connectionInfo = array("UID" =>"", "PWD" => "", "Database" => "AnalyzedData", 'CharacterSet' => "utf-8");
    public $runningdate;
    function __construct(){
        $this->conn = sqlsrv_connect($this->dbHost, $this->connectionInfo);
        //$this->wangonName = '0DZ302';
        $this->wangonName = $_GET['wangonName'];
        if ($this->conn == false) {
            echo "连接至服务器数据库失败";
            die(print_r(sqlsrv_errors(), true));}
    }
   public function returnQuery($sql){
       return sqlsrv_query($this->conn, $sql, $this->params, $this->options);
   }
}
function generalfail($machineId,$wangonName){                                                                                           //通用的sql执行方法
    $GeneralFail= new Totalcount();
    $sql="select Totalfail as TotalFail,Serfail as SerFail,Psnnum as PsnNum,MaxK as MaxK,MaxM as MaxM
from dbo.GeneralFail_".$machineId."
where WangonName='".$wangonName."'";
    $query = $GeneralFail->returnQuery($sql);
    @ $row_count = sqlsrv_num_rows($query);
    if($row_count==0){
        return 0;
    }
    else {
        $row=sqlsrv_fetch_array($query);
        $GeneralResult["TotalFail"]=$row['TotalFail'];
        $GeneralResult["SerFail"]=$row['SerFail'];
        $GeneralResult["PsnNum"]=$row['PsnNum'];
        $GeneralResult["MaxK"]=$row['MaxK'];
        $GeneralResult["MaxM"]=$row['MaxM'];
        return $GeneralResult;
    }
}
function confail($machineId,$wangonName){
    $ConFail= new Totalcount();
    $header="424d56010100000000003600000028000000b40000007800000001002000000000000000000000000000000000000000000000000000";
    $sql="select Id as ImageId,ConNumber as ConNum,StartPsn as StartPsn,EndPsn as EndPsn,ConArea as ConArea,ConCol as ConCol
from dbo.ConFail_".$machineId."
where WangonName='".$wangonName."'";
    $query = $ConFail->returnQuery($sql);
    @ $row_count = sqlsrv_num_rows($query);
    if($row_count==0){
        return 0;
    }
    else {
        while($row=sqlsrv_fetch_array($query)){
            $sql_image="select ConImage1 as Image1,ConImage2 as Image2
from dbo.ConImage_".$machineId."
where ImageId=".$row['ImageId'];
            $query_image=$ConFail->returnQuery($sql_image);
            $row_image=sqlsrv_fetch_array($query_image);
            $ConResult[] = array("ConNum"=>$row['ConNum'],
                "StartPsn"=>$row['StartPsn'],
                "EndPsn"=>$row['EndPsn'],
                "ConArea"=>$row['ConArea'],
                "ConCol"=>$row['ConCol'],
                "Image1"=>base64_encode(hex2bin($header.($row_image['Image1']))),
                "Image2"=>base64_encode(hex2bin($header.($row_image['Image2'])))
            );
        }
    }
return $ConResult;
}
function typfail($machineId,$wangonName){
    $TypFail=new Totalcount();
    $sql="";

}
function returnMachineInfo($WangonName){
    $returnMachineId=new Totalcount();
    $sql="select MachineId as MachineId,CreateTime as CreateTime from dbo.AllIndex where WangonName = '".$WangonName."'";
    $query=$returnMachineId->returnQuery($sql);
    $row=sqlsrv_fetch_array($query);
    $MachineId=$row['MachineId'];
    $CreateTime=$row['CreateTime'];
    $Info = array("MachineId"=>trim($MachineId),"CreateTime"=>$CreateTime);
    return $Info;
}

function returnData(){
    $getInfo=new Totalcount();
    $WangonName=$getInfo->wangonName;
    $Info=returnMachineInfo($WangonName);
    $MachineId=$Info['MachineId'];
    if($Info['MachineId']==Null){
        $result=0;
        echo json_encode($result);
    }
    else {
        $CreateTime=$Info['CreateTime']->format('Y/m/d H:i:s');
        $WangonInfo=array("MachineId"=>$MachineId,"CreateTime"=>$CreateTime);
        $GeneralFail = generalfail($MachineId,$WangonName);
        $ConFail = confail($MachineId,$WangonName);
        $result = array("WangonInfo"=>$WangonInfo)+array("GeneralFail" => $GeneralFail) + array("ConFail" => $ConFail);
        echo json_encode($result);
    }
}
returnData();