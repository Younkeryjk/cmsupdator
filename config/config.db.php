<?php
    defined('IN_UPDATOR') or exit('Access Denied');
    $db_config["hostname"] = "localhost"; //服务器地址
    $db_config["username"] = "root"; //数据库用户名
    $db_config["password"] = "root"; //数据库密码
    $db_config["database"] = "client"; //数据库名称
    $db_config["charset"] = "utf-8";//数据库编码
    $db_config["pconnect"] = 0;//开启持久连接
    $db_config["log"] = 1;//开启日志
    $db_config["logfilepath"] = './log/';//日志路径
?>
