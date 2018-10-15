<?php
function IpAddress(){
    $IpAddress=$_GET['IpAddress'];
    return $IpAddress;
}
function WagonName(){
    $WagonName=$_GET['WagonName'];
    return $WagonName;
}
function ID(){
    $ID=$_GET['ID'];
    return $ID;
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
    $WagonName = 'T' . WagonName();
    $Id=ID();
    $header="424d56010100000000003600000028000000b40000007800000001002000000000000000000000000000000000000000000000000000";
    $conn = Connect2Machine();
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $sql = "select ErrorImage from " . $WagonName . " where [index] = ".$Id;
    $query= sqlsrv_query($conn, $sql, $params, $options);
    $row= sqlsrv_fetch_array($query);
    $Image=base64_encode(hex2bin($header).$row['ErrorImage']);
    return array("Image"=>$Image);
}
echo json_encode(ReturnData());