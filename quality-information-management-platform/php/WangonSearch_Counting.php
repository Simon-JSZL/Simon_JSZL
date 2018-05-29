<?php
class Totalcount                                                                                                       //公共的数据链接、sql执行和日期查找封包
{
    public $dbHost = "localhost";
    public $machineId;
    public $wangonName;
    public $startdate;
    public $conn;
    public $params = array();
    public $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    public $connectionInfo = array("UID" =>"", "PWD" => "", "Database" => "AnalyzedData", 'CharacterSet' => "utf-8");
    public $runningdate;
    function __construct(){
        $this->machineId = $_GET['machineId'];
        $this->wangonName = $_GET['wangonName'];
        //$this->machineId ='J5';
        //$this->wangonName = '0DZ156';
        $this->conn = sqlsrv_connect($this->dbHost, $this->connectionInfo);
        if ($this->conn == false) {
            echo "连接至服务器数据库失败";
            die(print_r(sqlsrv_errors(), true));}
    }
   public  function returnQuery($sql){
       return sqlsrv_query($this->conn, $sql, $this->params, $this->options);
   }
}
function generalfail(){                                                                                           //通用的sql执行方法
    $GeneralFail= new Totalcount();
    $sql="select Totalfail as TotalFail,Serfail as SerFail,Psnnum as PsnNum,MaxK as MaxK,MaxM as MaxM
from dbo.GeneralFail_".$GeneralFail->machineId."
where WangonName='".$GeneralFail->wangonName."'";
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
function confail(){
    $ConFail= new Totalcount();
    $header="424d56010100000000003600000028000000b40000007800000001002000000000000000000000000000000000000000000000000000";
    $sql="select Id as ImageId,ConNumber as ConNum,StartPsn as StartPsn,EndPsn as EndPsn,ConArea as ConArea,ConCol as ConCol
from dbo.ConFail_".$ConFail->machineId."
where WangonName='".$ConFail->wangonName."'";
    $query = $ConFail->returnQuery($sql);
    @ $row_count = sqlsrv_num_rows($query);
    if($row_count==0){
        return false;
    }
    else {
        while($row=sqlsrv_fetch_array($query)){
            $sql_image="select ConImage1 as Image1,ConImage2 as Image2
from dbo.ConImage_".$ConFail->machineId."
where ImageId=".$row['ImageId'];
            $query_image=$ConFail->returnQuery($sql_image);
            $ConResult[] = array("ConNum"=>$row['ConNum'],
                "StartPsn"=>$row['StartPsn'],
                "EndPsn"=>$row['EndPsn'],
                "ConArea"=>$row['ConArea'],
                "ConCol"=>$row['ConCol'],
                "Image1"=>base64_encode(hex2bin($header.(sqlsrv_fetch_array($query_image)['Image1']))),
                "Image2"=>base64_encode(hex2bin($header.(sqlsrv_fetch_array($query_image)['Image2'])))
            );
        }
    }
return $ConResult;
}
$GeneralFail=generalfail();
$ConFail=confail();
$result=array("GeneralFail"=>$GeneralFail)+array("ConFail"=>$ConFail);
echo json_encode($result);