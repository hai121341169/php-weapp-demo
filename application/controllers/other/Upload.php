<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use qcloudcos\Cosapi;

class Upload extends CI_Controller {
	public function index() {
		$bucket = 'uploadimg';
		$src = $_FILES["file"]["tmp_name"];
		$dst = '/'.$_POST["filepath"].'/'.$_POST["filename"];
		$folder = '/'.$_POST["filepath"];
		
		Cosapi::setTimeout(180);

		// 设置COS所在的区域，对应关系如下：华南=gz 华中=sh 华北=tj
		Cosapi::setRegion('gz');

		// Create folder in bucket.
		$ret = Cosapi::createFolder($bucket, $folder);
		//var_dump($ret);

		// Upload file into bucket.
		$ret = Cosapi::upload($bucket, $src, $dst);
		//var_dump($ret);

	    $file_url='';
	    if ($ret['code'] == '0') {
	        //上传成功了的提示
			//$file_url=json_encode($ret);
	        //$u=$ret['data']['resource_path'];
	        //$file_url=str_replace("", "",$u);
			$file_url=$ret['data']['access_url'];
			$file_url=str_replace("file","cosgz",$file_url);
		}

		echo json_encode($ret);
    }
}
