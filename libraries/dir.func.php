<?php
defined('IN_UPDATOR') or exit('Access Denied');
/**
 * opendir readdir
 * @param string $path
 * @return array
 */
function readDirectory($path) {
	$handle = opendir($path);
	while ( ($item = readdir($handle)) !== false ) { // dir 0
		//. & .. dir
		if ( $item!="." && $item!="..") {
			if ( is_file("$path/$item") ) {
				$arr['file'][] = $item;
			}
			if ( is_dir("$path/$item") ) {
				$arr['dir'][] = $item;
			}
		}
	}
	closedir($handle);
	return $arr;
}

/**
 * filesize
 * @param string $path
 * @return int 
 */
function dirSize($path){
	global $sum;
	$sum = 0;
	$handle = opendir($path);
	while( ($item=readdir($handle))!==false ){
		if( $item!="." && $item!=".." ){
			if( is_file("$path/$item") ){
				$sum+=filesize("$path/$item");
			}
			if( is_dir($path."/".$item) ){
				$func=__FUNCTION__;
				$func("$path/$item");
			}
		}
	}
	closedir($handle);
	return $sum;
}

/**
 * create Folder
 * @param string $dirname
 * @return string
 */
function createFolder($dirname) {

	if ( !checkFilename(basename($dirname)) ) {
		return "非法文件夹名称";
	}
	
	if ( file_exists($dirname) ) {
		return "存在同名文件夹";
	}
	
	return mkdir($dirname,0777,true) ? "文件夹创建成功" : "文件夹创建失敗";
}

/**
 * rename Folder
 * @param string $oldname
 * @param string $newname
 * @return string
 */
function renameFolder($oldname, $newname) {
	
	if( !checkFilename(basename($newname)) ){
		return "非法文件夹名称";
	}
	
	if( file_exists($newname) ){
		return "存在同名文件夹";
	}
	
	return rename($oldname,$newname) ? "重命名成功" : "重命名失败";
}

function copyFolder($src,$dst){
	if( !file_exists($dst) ){
		mkdir($dst,0777,true);
	}
	$handle=opendir($src);
	while( ($item=readdir($handle))!==false ){
		if( $item!="." && $item!=".." ) {
			if( is_file("$src/$item") ) {
				copy("$src/$item", "$dst/$item");
			}
			if( is_dir("$src/$item") ) {
				$func=__FUNCTION__;
				$func("$src/$item", "$dst/$item");
			}
		}
	}
	closedir($handle);
	return "复制成功";
	
}


/**
 * delete Folder
 * @param string $path
 * @return string
 */
function delFolder($path) {
	$handle=opendir($path);
	while( ($item=readdir($handle))!==false ){
		if( $item!="." && $item!=".." ){
			if( is_file($path."/".$item) ) {
				unlink($path."/".$item);
			}
			if( is_dir($path."/".$item) ) {
				$func=__FUNCTION__;
				$func($path."/".$item);
			}
		}
	}
	closedir($handle);
	rmdir($path);
	return "文件夹删除成功";
}