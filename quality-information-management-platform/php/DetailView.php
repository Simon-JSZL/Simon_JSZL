<?php
include('./ConnectInfo.php');
function Procedure(){
    $Procedure=$_GET['Procedure'];
    //$Procedure=1;
    if($Procedure==0)
        $Procedure='W1';
    else if($Procedure==1)
        $Procedure='W2';
    return $Procedure;
}
function WagonName(){
    $WagonName=$_GET['WagonName'];
    //$WagonName='0DZ114';
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
function IpAddress(){
    $WangonName=WagonName();
    $Procedure=Procedure();
    $ConnInfo=new ConnectInfo();
    $sql_MachineId="select * from dbo.AllIndex where WangonName = '".$WangonName."'";
    $row_MachineId=$ConnInfo->returnRow($sql_MachineId);
    $MachineId=$row_MachineId['MachineId_'.$Procedure];
    $sql_IpAddress="select IpAddress from dbo.MachineInfo where MachineId = '".$MachineId."'";
    $row_IpAddress=$ConnInfo->returnRow($sql_IpAddress);
    $IpAddress=$row_IpAddress['IpAddress'];
    return $IpAddress;
}
function order ( $request, $columns )
{
    $order = '';
    if ( isset($request['order']) && count($request['order']) ) {
        $orderBy = array();
        $dtColumns = self::pluck( $columns, 'dt' );
        for ( $i=0, $ien=count($request['order']) ; $i<$ien ; $i++ ) {
            // Convert the column index into the column data property
            $columnIdx = intval($request['order'][$i]['column']);
            $requestColumn = $request['columns'][$columnIdx];
            $columnIdx = array_search( $requestColumn['data'], $dtColumns );
            $column = $columns[ $columnIdx ];
            if ( $requestColumn['orderable'] == 'true' ) {
                $dir = $request['order'][$i]['dir'] === 'asc' ?
                    'ASC' :
                    'DESC';
                $orderBy[] = '`'.$column['db'].'` '.$dir;
            }
        }
        if ( count( $orderBy ) ) {
            $order = 'ORDER BY '.implode(', ', $orderBy);
        }
    }
    return $order;
}
function ReturnData(){
    $columns = array(
        array( 'db' => '[Index]',   'dt' => 0 ),
        array( 'db' => 'FormatPos', 'dt' => 1 ),
        array( 'db' => 'Reserve2',  'dt' => 2 ),
        array( 'db' => 'Reserve3',  'dt' => 3 ));
    $WagonName='T'.WagonName();
    $Data=array();
    $conn=Connect2Machine();
    $params = array();
    $options = array("Scrollable" => SQLSRV_CURSOR_KEYSET);
    $sql="select [Index] as ID,FormatPos,Reserve2 as Grade,Reserve3 as Dim from ".$WagonName;
    $query= sqlsrv_query($conn, $sql, $params, $options);
    while($row=sqlsrv_fetch_array($query)){
        $Data[]=array(
            'ID'=>$row['ID'],
            'FormatPos'=>$row['FormatPos'],
            'Grade'=>($row['Grade']==1?"一般":"严重"),
            'Dim'=>$row['Dim'],
            "IpAddress"=>trim(IpAddress())
            //'Image'=>base64_encode($header.$row['ErrorImage'])
        );
    }
    $ReturnData=array(
        "draw" =>isset ($_GET['draw'])?
        intval( $_GET['draw'] ) :
        0,
        "recordsTotal"=>count($Data),
        "recordsFiltered"=>count($Data),
        "data"=>$Data
    );
    echo json_encode($ReturnData);
}
ReturnData();