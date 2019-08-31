<?php
/**
 * Created by PhpStorm.
 * User: admin
 * Date: 2019/8/26
 * Time: 17:27
 */
defined('IN_UPDATOR') or exit('Access Denied');

define("PLUG_NAME", "cmsupdator");
define("SERVER_URL", "http://test.server.com");
define("SERVER_PLUG_URL", SERVER_URL . "/" . PLUG_NAME);

define("CLIENT_ROOT", dirname(dirname(dirname(__FILE__))));
define("PLUG_ROOT", dirname(dirname(__FILE__)));

define("FILE_LOG", true);//文件日志
define("FILE_LOG_PATH", PLUG_ROOT . '/log/filelog.txt');//文件日志路径