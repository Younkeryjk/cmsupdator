<?php
defined('IN_UPDATOR') or exit('Access Denied');
/**
 * trans Byte Bytes/Kb/MB/GB/TB/EB
 * @param number $size
 * @return number
 */
function transFileByte($size) {
	$arr = array("B", "KB", "MB", "GB", "TB", "EB" );
	$i = 0;
	while ( $size >= 1024 ) {
		$size /= 1024;
		$i ++;
	}

	return round($size, 2) . $arr[$i];
}

/**
 * create File
 * @param string $filename
 * @return string
 */
function createFile($filename) {

	$pattern = "/[\/,\*,<>,\?\|]/";
	if( preg_match( $pattern, basename($filename) ) ) {
		return "非法文件名";
	} 

	if( file_exists($filename) ) {
		return "文件已存在，请重命名后创建";
	}

	return touch($filename) ? "文件创建成功" : "文件创建失败";
}


/**
 * check Filename
 * @param string $filename
 * @return boolean
 */
function checkFilename($filename) {
	//验证文件名的合法性,是否包含/,*,<>,?,|
	$pattern = "/[\/,\*,<>,\?\|]/";
	return preg_match($pattern, $filename) ? false : true;
}

/**
 * rename File
 * @param string $oldname
 * @param string $newname
 * @return string
 */
function renameFile($oldname, $newname){

	if( !checkFilename($newname) ) {
		return "非法文件名";
	}

	$path = dirname($oldname);
	if( file_exists("$path/$newname") ) {
		return "存在同名文件，请重新命名";
	}

	return rename($oldname,"$path/$newname") ? "重命名成功" : "重命名失败";
	
}

/**
 * cut File
 * @param string $filename
 * @param string $dstname
 * @return string
 */
function cutFile($filename, $dstname){
	
	if( !file_exists($dstname) ){
		return "目标目录不存在";
	}
	
	if( file_exists($dstname."/".basename($filename)) ){
		return "存在同名文件";
	}
	
	return rename($filename,$dstname."/".basename($filename)) ? "文件剪切成功" : "文件剪切失败";
}

/**
 * copyFile
 * @param string $filename
 * @param string $dstname
 * @return string
 */
function copyFile($filename, $dstname) {
	
	if( !file_exists($dstname) ){
		return "目标目录不存在";
	}
	
	if( file_exists($dstname."/".basename($filename)) ){
		return "存在同名文件";
	}
	
	return copy($filename,$dstname."/".basename($filename)) ? "文件复制成功" : "文件复制失败";
}




/**
 * delete File
 * @param string $filename
 * @return string
 */
function delFile($filename) {
	return unlink($filename) ? "文件删除成功" : "文件删除失败";
}


/**
 * send the file to the browser as a download
 * @param string $filename
 */
function downFile($filename) {
	header("content-disposition:attachment;filename=".basename($filename));
	header("content-length:".filesize($filename));
	readfile($filename);
}

/**
 * uploadFile
 * @param array $fileInfo
 * @param string $path
 * @param array $allowExt
 * @param int $maxSize
 * @return string
 */
function uploadFile($fileInfo, $path, $allowExt=array("gif","jpeg","jpg","png","txt"), $maxSize=10485760){

	if( $fileInfo['error']!=UPLOAD_ERR_OK ){
		switch( $fileInfo['error'] ){
			case 1:
				$mes="超过了配置文件的大小";
				break;
			case 2:
				$mes="超过了表单允许接收数据的大小";
				break;
			case 3:
				$mes="文件部分被上传";
				break;
			case 4:
				$mes="没有文件被上传";
				break;
		}
		return $mes;
	}

	if( !is_uploaded_file($fileInfo['tmp_name']) ){
		return "文件不是通过HTTP POST方式上传上来的";
	}

	$ext=getExt($fileInfo['name']);
	$allowExt=array("gif","jpeg","jpg","png","txt");
	if( !in_array($ext,$allowExt) ){
		return "非法文件类型";
	}
	if( $fileInfo['size']>$maxSize ){
		return "文件过大";
	}

	$uniqid = getUniqidName();
	$destination=$path."/".pathinfo($fileInfo['name'],PATHINFO_FILENAME)."_".$uniqid.".".$ext;
	return move_uploaded_file($fileInfo['tmp_name'], $destination) ? "文件上传成功" : "文件移动失敗";

}