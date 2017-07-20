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



		// 设置COS所在的区域，对应关系如下：
		//     华南  -> gz
		//     华中  -> sh
		//     华北  -> tj
		Cosapi::setRegion('gz');




		// Create folder in bucket.
		$ret = Cosapi::createFolder($bucket, $folder);
		//var_dump($ret);
		//echo json_encode($ret);

		$ret = Cosapi::delFile($bucket, $dst);
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

		function request_post($url = '', $post_data = array()) {
			if (empty($url) || empty($post_data)) {
				return false;
			}

			$o = "";
			foreach ( $post_data as $k => $v )
			{
				$o.= "$k=" . urlencode( $v ). "&" ;
			}
			$post_data = substr($o,0,-1);

			$postUrl = $url;
			$curlPost = $post_data;
			$ch = curl_init();//初始化curl
			curl_setopt($ch, CURLOPT_URL,$postUrl);//抓取指定网页
			curl_setopt($ch, CURLOPT_HEADER, 0);//设置header
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);//要求结果为字符串且输出到屏幕上
			curl_setopt($ch, CURLOPT_POST, 1);//post提交方式
			curl_setopt($ch, CURLOPT_POSTFIELDS, $curlPost);
			$data = curl_exec($ch);//运行curl
			curl_close($ch);

			return $data;
		}


		function testAction($ret){
			$url = 'cs.hucai.com/appimg/api.php/upload/save_oss';
			$post_data['pic_id']       = '20170707';
			$post_data['pic_name']       = '我的作品-2017';
			$post_data['work_id']      = '88888';
			$post_data['pic_info']      = '我的作品';
			$post_data['account_id']      = '13632333996';
			$post_data['sml_img']      = $ret;
			$post_data['mid_img'] = 'http://p3.so.qhimgs1.com/bdr/_240_/t01415d129d2dffbf33.jpg';
			$post_data['big_img']    = 'http://p3.so.qhimgs1.com/bdr/_240_/t01415d129d2dffbf33.jpg';
			$post_data['filedate']    = '1480062335';
			//$post_data = array();
			$res = request_post($url, $post_data);
			//print_r($res);

		}
		if($file_url!=''){
			testAction($file_url);
		}



		echo json_encode($ret);
    }
}
