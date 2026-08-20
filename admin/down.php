<?php
$nosession=true;
$nosecu=true;
include("../includes/common.php");
if($islogin==1){}else exit("<script language='javascript'>window.location.href='./login.php';</script>");

$urlarr=explode('/',$_SERVER['PATH_INFO']);
if (($length = count($urlarr)) > 1) {
$url = $urlarr[$length-1];
}
$extension=explode('&',$url);
if (($length = count($extension)) > 1) {
$pwd = $extension[$length-1];
$url = $extension[0];
}

if(strpos($url,".")){
    $hash=substr($url,0,strpos($url,"."));
}else{
    $hash=$url;
}

$row = $DB->getRow("SELECT * FROM `pre_file` WHERE `hash`=:hash limit 1", [':hash'=>$hash]);
if(!$row)exit('404 Not Found');

// 根据文件存储位置加载对应的存储驱动
$file_storage = $row['storage'] ? $row['storage'] : 'local';
$file_stor = \lib\StorHelper::getModel($file_storage);
if(!$file_stor){
    exit('存储驱动加载失败');
}

if($file_stor->exists($hash))
{
    $DB->exec("UPDATE `pre_file` SET `lasttime`=NOW(),`count`=`count`+1 WHERE `id`='{$row['id']}'");
    
    file_output($hash, $row['type'], $row['size'], $row['name'], $file_stor, true);
}
else{
    exit('File Not Found');
}