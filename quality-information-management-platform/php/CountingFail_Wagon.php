<?php
include('./ConnectInfo.php');
class CountingFailWagon
{
    public function generalfail($machineId,$wangonName){                                                                                           //通用的sql执行方法
        $ConnInfo= new ConnectInfo();
        $sql="select Totalfail as TotalFail,Serfail as SerFail,Psnnum as PsnNum,MaxK as MaxK,MaxM as MaxM
from dbo.GeneralFail_".$machineId."
where WangonName='".$wangonName."'";
        $query = $ConnInfo->returnQuery($sql);
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
    public function confail($machineId,$wangonName){
        $ConnInfo= new ConnectInfo();
        $header="424d56010100000000003600000028000000b40000007800000001002000000000000000000000000000000000000000000000000000";
        $sql="select Id as ImageId,ConNumber as ConNum,StartPsn as StartPsn,EndPsn as EndPsn,ConArea as ConArea,ConCol as ConCol
from dbo.ConFail_".$machineId."
where WangonName='".$wangonName."'";
        $query = $ConnInfo->returnQuery($sql);
        @ $row_count = sqlsrv_num_rows($query);
        if($row_count==0){
            return 0;
        }
        else {
            while($row=sqlsrv_fetch_array($query)){
                $sql_image="select ConImage1 as Image1,ConImage2 as Image2
from dbo.ConImage_".$machineId."
where ImageId=".$row['ImageId'];
                $query_image=$ConnInfo->returnQuery($sql_image);
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
    public function typfail($machineId,$wangonName){
        $ConnInfo=new ConnectInfo();
        $sql="select * from dbo.TypicalFail_".$machineId." where WangonName = '".$wangonName."'";
        $query = $ConnInfo->returnQuery($sql);
        $header="424d56010100000000003600000028000000b40000007800000001002000000000000000000000000000000000000000000000000000";
        $row = sqlsrv_fetch_array($query);
        for($i=1;$i<4;$i++)
        {
            $TypResult[$i-1]['Max_Pos'] = $row['Max_Pos'.$i];
            $TypResult[$i-1]['Max_Area'] = $row['Max_Area'.$i];
            $TypResult[$i-1]['Max_Num'] = $row['Max_Num'.$i];
            $TypResult[$i-1]['Avg_Dim'] = $row['Avg_Dim'.$i];
            $sql_image="select TypImage".$i." as Image from dbo.TypicalImage_".$machineId." where WangonName = '".$wangonName."'";
            $query_image=$ConnInfo->returnQuery($sql_image);
            $row_image = sqlsrv_fetch_array($query_image);
            $TypResult[$i-1]['Image'] = base64_encode(hex2bin($header.($row_image['Image'])));
        }
        return $TypResult;
    }
}
