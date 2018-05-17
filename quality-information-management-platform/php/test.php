<?php
$dbHost= "localhost";$uid = "";$pwd = "";$dbName = 'DZVS';$charset = 'utf-8';
$params = array();
$options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
$connectionInfo = array("UID" => $uid, "PWD" => $pwd, "Database" => $dbName, 'CharacterSet' => $charset);
$conn=sqlsrv_connect($dbHost,$connectionInfo);
$sql="select top 10 ErrorImage as image from dbo.T0ZZ520
order by PSN";
$query = sqlsrv_query($conn, $sql, $params, $options);
while($row=sqlsrv_fetch_array($query)) {
    //$img = base64_encode($row['image']);
    //$src="data:image/bmp;base64,".$img;
    //echo "<img src='$src'>";
    //file_put_contents("file.bmp", $row['image']);
}

