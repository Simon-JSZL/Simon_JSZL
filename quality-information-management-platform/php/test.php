<?php
$dbHost= "localhost";$uid = "";$pwd = "";$dbName = 'AnalyzedData';$charset = 'utf-8';
$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$connectionInfo = array("UID" => $uid, "PWD" => $pwd, "Database" => $dbName, 'CharacterSet' => $charset);
$conn=sqlsrv_connect($dbHost,$connectionInfo);
$sql="select ConImage1 as image from ConImage_J5";
$query = sqlsrv_query($conn, $sql, $params, $options);
while($row=sqlsrv_fetch_array($query)) {
    $header="424d56010100000000003600000028000000b40000007800000001002000000000000000000000000000000000000000000000000000";
    $imgfile=$header.$row['image'];
    $image=hex2bin($imgfile);
    //file_put_contents("tset.bmp",$image);
    $img = base64_encode($image);
    echo '<img src="data:image/bmp;base64,'.$img.'">';
    //file_put_contents("file.bmp", $row['image']);
}

