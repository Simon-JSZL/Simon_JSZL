<?php

function IpAddress(){
    $MachineId=trim($_POST['MachineId']);
    //$MachineId="J5";
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
    $WagonName=$_POST['WagonName'];
    //$WagonName="0DZ180";
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
function create_zip($files = array(),$destination = '',$overwrite = false) {
    //if the zip file already exists and overwrite is false, return false
    if(file_exists($destination) && !$overwrite) { return false; }
    if(count($files)) {
        //create the archive
        $zip = new ZipArchive();
        if($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            return false;
        }
        //add the files
        foreach($files as $file) {
            $zip->addFile($file,$file);
        }
        //debug
        //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;
        $zip->close();
        //check to make sure the file exists
        header('Content-type: application/zip');
        header('Content-Disposition: attachment; filename="'.WagonName().'.zip"');
        readfile($destination);
        return "下载完成";
    }
    else
    {
        return "下载失败";
    }
}
function Delete($path)
{
    if (is_dir($path) === true)
    {
        $files = array_diff(scandir($path), array('.', '..'));

        foreach ($files as $file)
        {
            Delete(realpath($path) . '/' . $file);
        }

        return rmdir($path);
    }

    else if (is_file($path) === true)
    {
        return unlink($path);
    }

    return false;
}
function ReturnData()
{
    $WagonName =WagonName();
    $header="424d56010100000000003600000028000000b40000007800000001002000000000000000000000000000000000000000000000000000";
    $conn = Connect2Machine();
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $sql = "select [Index] as Id,FormatPos,PSN,ErrorImage from " . "T" . $WagonName ;
    $query= sqlsrv_query($conn, $sql, $params, $options);
    @ $row_count = sqlsrv_num_rows($query);
    if($row_count==0){
        $result="未查询到该车次信息，请确认输入参数无误";
    }
    else {
        mkdir("../".$WagonName);
        define("IMAGE_FOLDER", "../".$WagonName."/");
        $FileList=array();
        while ($row = sqlsrv_fetch_array($query)) {
            $Image = hex2bin($header) . $row['ErrorImage'];
            $file = IMAGE_FOLDER . $row['Id'].'--'.$row['FormatPos'].'K--'.$row['PSN'].'Z'.'.jpg';
            file_put_contents($file, $Image);
            $FileList[]=$file;
        }
        $result=create_zip($FileList,IMAGE_FOLDER .$WagonName .'.zip');
        Delete(IMAGE_FOLDER);
    }
return $result;
}

echo ReturnData();