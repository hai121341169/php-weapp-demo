<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Api Class
 *
 */
class MY_Controller extends CI_Controller {
	public function __construct()
    {
        parent::__construct();
    }

	public $return = array(
		'code' => 200,
		'description' => '',
		'data' => array()
	);

	public function ajaxReturn($type='',$json_option=0) {
		$data = $this->return;
	    if(empty($type)) $type='JSON';
	    switch (strtoupper($type)){
	        case 'JSON' :
	            // 返回JSON数据格式到客户端 包含状态信息
	            header('Content-Type:application/json; charset=utf-8');
	            exit(json_encode($data,$json_option));
	        case 'XML'  :
	            // 返回xml格式数据
	            header('Content-Type:text/xml; charset=utf-8');
	            exit(xml_encode($data));
	        case 'JSONP':
	            // 返回JSON数据格式到客户端 包含状态信息
	            header('Content-Type:application/json; charset=utf-8');
	            $handler  =   isset($_GET['callback']) ? $_GET['callback'] : 'jsonpReturn';
	            exit($handler.'('.json_encode($data,$json_option).');');
	        case 'EVAL' :
	        default     :
	            // 返回可执行的js脚本
	            header('Content-Type:text/html; charset=utf-8');
	            exit($data);
	    }
	}
}
