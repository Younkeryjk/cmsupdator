<?php
/**
* Created by Younker.
* User: admin
* Date: 2019/8/26
* Time: 17:27
* 1、对比客户端系统与服务器系统的版本号，客户端版本号小于服务器的进行自动升级。
* 2、检测更新包下是否含有数据库更新策略文件update.sql，有则下载并执行里面的语句。
* 3、检测更新包下是否含有程序文件更新策略文件update.txt，有则运行里面的更新策略。
*/
header("Content-Type: text/html;charset=utf-8");
date_default_timezone_set("Asia/Shanghai");
set_time_limit(0);
ini_set('memory_limit','1024M');

define('IN_UPDATOR', true);
require_once ('./config/constant.php');
require_once(PLUG_ROOT . '/libraries/FTP.php');
require_once (PLUG_ROOT . '/libraries/sync.func.php');

$server_version = curl_ftp_file_read(PLUG_NAME . '/VERSION');
if (null == $server_version) {
    exit('升级失败');
}
$client_version = file_get_contents('VERSION');
if ($server_version == $client_version) {
    exit('已是最新版本');
}

$ftp = new FTP();

$update_files = get_update_files($server_version);
$database_state = sync_database($server_version, $update_files['sql']);
$programs_state = sync_programs($server_version, $update_files['txt']);
$isSuccess = update_client_version($server_version, $database_state, $programs_state);
if ($isSuccess) {
    exit('升级成功');
}
exit('升级失败');
