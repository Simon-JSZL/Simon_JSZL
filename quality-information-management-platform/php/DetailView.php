<?php
function Connect2Server()
{
    $dbHost = "localhost";
    $uid = "";
    $pwd = "";
    $dbName = 'AnalyzedData';
    $charset = 'utf-8';
    $connectionInfo = array("UID" => $uid, "PWD" => $pwd, "Database" => $dbName, 'CharacterSet' => $charset);
    $conn=sqlsrv_connect($dbHost,$connectionInfo);
    if($conn == false)
    {
        sqlsrv_close($this->conn_Jitai);
        exit;
    }
    else
        return $conn;
}
function Connect2Machine($MachineId)
{
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $conn_Server=Connect2Server();
    $sql="select IpAddress from dbo.MachineInfo where MachineId = '".$MachineId."'";//根据传入的机台号从服务器端查询到该机台的ip
    $query=sqlsrv_query($conn_Server, $sql, $params, $options);
    $dbHost=sqlsrv_fetch_array($query);
    $uid = "";
    $pwd = "";
    $dbName = 'DZVS';
    $charset = 'utf-8';
    $connectionInfo = array("UID" => $uid, "PWD" => $pwd, "Database" => $dbName, 'CharacterSet' => $charset);
    $conn=sqlsrv_connect($dbHost['IpAddress'],$connectionInfo);
    if($conn == false)
    {
        sqlsrv_close($this->conn_Jitai);
        exit;
    }
    else
        return $conn;
}
function MachineId(){
    //$MachineId=$_GET['machineId'];
    $MachineId='J5';
    return $MachineId;
}
function WagonName(){
    //$WagonName='T'.$_GET['wagonName'];
    $WagonName='T0DZ114';
    return $WagonName;
}
function ReturnData($MachineId,$WagonName){
    $i=1;
    $header=hex2bin("424d56010100000000003600000028000000b40000007800000001002000000000000000000000000000000000000000000000000000");
    $conn=Connect2Machine($MachineId);
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $sql="select PSN,FormatPos,Reserve2 as Grade,Reserve3 as Dim,ErrorImage from ".$WagonName;
    $query= sqlsrv_query($conn, $sql, $params, $options);
    while($row=sqlsrv_fetch_array($query)){
        $ReturnData[]=array(
            'ID'=>$i++,
            'Psn'=>$row['PSN'],
            'FormatPos'=>$row['FormatPos'],
            'Grade'=>$row['Grade'],
            'Dim'=>$row['Dim'],
            'Image'=>base64_encode($header.$row['ErrorImage'])
        );
    }
    print_r($ReturnData);
    return $ReturnData;
}
ReturnData(MachineId(),WagonName());