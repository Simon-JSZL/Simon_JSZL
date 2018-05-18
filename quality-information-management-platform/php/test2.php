<?php $filename = "tset.bmp";
//为图片的路径可以用d:/upload/11.jpg等绝对路径
$file = fopen($filename, "rb");
$bin = fread($file,100000); //只读2字节
$bin=bin2hex($bin);
print_r($bin);