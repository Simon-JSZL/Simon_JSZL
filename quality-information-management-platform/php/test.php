<?php
$dbHost= "localhost";$uid = "";$pwd = "";$dbName = 'DZVS';$charset = 'utf-8';
$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$connectionInfo = array("UID" => $uid, "PWD" => $pwd, "Database" => $dbName, 'CharacterSet' => $charset);
$conn=sqlsrv_connect($dbHost,$connectionInfo);
$sql="select top 1 ErrorImage as image from dbo.T0ZZ520
order by PSN";
$query = sqlsrv_query($conn, $sql, $params, $options);
while($row=sqlsrv_fetch_array($query)) {
    $header="424d56010100000000003600000028000000b40000007800000001002000000000000000000000000000000000000000000000000000";
    $img=$header.bin2hex($row['image']);
    $image=hex2bin($img);
    file_put_contents("tset4.bmp",imageflip($image,2));
    //$img = base64_encode($row['image']);
    //$src="data:image/bmp;base64,".$img;
    //echo "<img src='$src'>";
    //file_put_contents("file.bmp", $row['image']);
}

