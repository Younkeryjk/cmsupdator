<?php
defined('IN_UPDATOR') or exit('Access Denied');
function get_update_files($server_version) {
    global $ftp;
    $files = array('sql' => '', 'txt' => '');
    $ftp->chgdir(PLUG_NAME . '/updates-' . $server_version);
    foreach ($ftp->filelist() as $filename) {
        if ('sql' == getFileExt($filename)) {
            $files['sql'] = str_replace('./', '', $filename);
        }
        if ('txt' == getFileExt($filename)) {
            $files['txt'] = str_replace('./', '', $filename);
        }
    }
    return $files;
}
function getFileExt($filename) {
    if(FALSE === strpos($filename, '.')) {
        return 'txt';
    }

    $extarr = explode('.', $filename);
    return end($extarr);
}
/*
 * 同步Web程序
 */
function sync_programs($server_version, $cmd_file_name)
{
    if (!$cmd_file_name) {
        return true;
    }

    global $ftp;
    require(PLUG_ROOT . '/libraries/dir.func.php');
    require(PLUG_ROOT . '/libraries/file.func.php');

    $cmds = curl_ftp_file_read(PLUG_NAME . '/updates-' . $server_version . '/' . $cmd_file_name);
    if (null == $cmds) {
        return false;
    }
    $program_cmd_arr = explode(PHP_EOL, $cmds);

    //echo '程序同步中......<br/>';
    foreach ($program_cmd_arr as $cmd) {
        list ($type, $file_path, $option) = explode('|', $cmd);
        $type = trim($type);
        $file_path = trim($file_path);
        $option = trim($option);
        if ('dir' == $type) {
            sync_dirs($file_path, $option);
        } else {
            sync_file($file_path, $option);
        }
    }
    return true;
}

/*
 * 同步目录
 */
function sync_dirs($dir_path, $option)
{
    global $ftp;
    sync_log(1, $dir_path, $option);
    switch ($option) {
        case 'add':
        case 'update':
            $ftp->download_dir($dir_path, CLIENT_ROOT . $dir_path);
            break;
        case 'delete':
            delFolder(CLIENT_ROOT . $dir_path);
            break;
        default :
            break;
    }
}

/*
 * 同步文件
 */
function sync_file($file_path, $option)
{
    global $ftp;
    sync_log(0, $file_path, $option);

    switch ($option) {
        case 'add':
        case 'update':
            $ftp->download($file_path, CLIENT_ROOT . $file_path);
            break;
        case 'delete':
            delFile(CLIENT_ROOT . $file_path);
            break;
        default :
            break;
    }
}

/*
 * 同步数据库数据
 */
function sync_database($server_version, $sql_file_name)
{
    if (!$sql_file_name) {
        return true;
    }
    require(PLUG_ROOT . '/libraries/DB.php');

    $sqls = curl_ftp_file_read(PLUG_NAME . '/updates-' . $server_version . '/'.$sql_file_name);
    if (null == $sqls) {
        return true;
    }
    $db = new DB();
    $sql_arr = explode(';', $sqls);
    //echo 'SQL同步中......<br/>';
    foreach ($sql_arr as $sql) {
        $sql = trim($sql);
        //echo $sql . "<br/><br/>";
        if ($sql) {
            $db->query($sql);
        }
    }
    return true;
}

function curl_ftp_file_read($path)
{
    require(PLUG_ROOT . "/config/config.ftp.php");
    $curlobj = curl_init();//初始化
    curl_setopt($curlobj, CURLOPT_URL, "ftp://" . $ftp_config["hostname"] . ':' . $ftp_config["port"] . "/" . $path);
    curl_setopt($curlobj, CURLOPT_HEADER, 0);//不输出header
    curl_setopt($curlobj, CURLOPT_RETURNTRANSFER, 1);
    //time out after 300s
    curl_setopt($curlobj, CURLOPT_TIMEOUT, 300);//超时时间
    //通过这个函数设置ftp的用户名和密码,没设置就不需要!
    curl_setopt($curlobj, CURLOPT_USERPWD, $ftp_config["username"] . ':' . $ftp_config["password"]);

    $result = curl_exec($curlobj);

    if (curl_errno($curlobj)) {
        return;
    }
    curl_close($curlobj);
    return $result;
}

function update_client_version($server_version, $database_state, $programs_state)
{
    if ($database_state && $programs_state) {
        $res = file_put_contents(PLUG_ROOT . '/VERSION', $server_version);
        return $res ? true : false;
    }
}

/**
 * @param int $type 0 为文件；1为目录
 * @param $path
 * @param $option
 */
function sync_log($type = 0, $path, $option)
{
    if (FILE_LOG) {
        $msg = '';
        switch ($option) {
            case 'add':
                $msg = '增加';
                break;
            case 'update':
                $msg = '修改';
                break;
            case 'delete':
                $msg = '删除';
                break;
            default :
                break;
        }
        $msg .= $type ? '目录：' : '文件：';
        $msg .= $path;
        $text = date("Y-m-d H:i:s") . " " . $msg . PHP_EOL;
        //echo $text;
        file_put_contents(FILE_LOG_PATH, $text, FILE_APPEND);
    }
}
