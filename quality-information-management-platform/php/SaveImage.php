<?php
function IpAddress(){
    //$MachineId=trim($_GET['MachineId']);
    $MachineId="J5";
    switch ($MachineId){
        //case "J5":$IpAddress="10.17.57.39";break;
        //case "J6":$IpAddress="10.17.57.37";break;
        //case "W10#1":$IpAddress="10.17.57.40";break;
        //case "W10#2":$IpAddress="10.17.57.41";break;
        case "J5":$IpAddress="localhost";break;
        case "J6":$IpAddress="localhost";break;
        case "W10#1":$IpAddress="localhost";break;
        case "W10#2":$IpAddress="localhost";break;
    }
    return $IpAddress;
}
function WagonName(){
    //$WagonName=$_GET['WagonName'];
    $WagonName="0DZ180";
    return $WagonName;
}
function Connect2Machine()
{
    $uid = "";
    $pwd = "";
    $dbName = 'DZVS';
    $charset = 'utf-8';
    $connectionInfo = array("UID" => $uid, "PWD" => $pwd, "Database" => $dbName, 'CharacterSet' => $charset);
    $conn=sqlsrv_connect(IpAddress(),$connectionInfo);
    if($conn == false)
    {
        sqlsrv_close($conn);
        exit;
    }
    else
        return $conn;
}
function ReturnData()
{
    $result=array();
    $WagonName = 'T' . WagonName();
    $header="424d56010100000000003600000028000000b40000007800000001002000000000000000000000000000000000000000000000000000";
    $conn = Connect2Machine();
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $sql = "select [Index] as Id,ErrorImage from " . $WagonName;
    $query= sqlsrv_query($conn, $sql, $params, $options);
    while($row= sqlsrv_fetch_array($query)){
        $Image=base64_encode(hex2bin($header).$row['ErrorImage']);
        $result[]=array(
            "Id"=>$row['Id'],
            "Image"=>$Image
        );
    }
    return $result;
}
echo json_encode(ReturnData());