<?php
include('./CountingFail_Wagon.php');
$CountingFailWagon=new CountingFailWagon();
$TypFail = $CountingFailWagon->typfail('J5','0DZ314');
$result = array("TypFail"=> $TypFail);
print_r(($TypFail));
echo json_encode($result);