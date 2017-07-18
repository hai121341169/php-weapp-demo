<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use qcloudcos\Cosapi;

class Image extends MY_Controller {

    public function add_image(){
        $user_id = $_POST['user_id'];
        $order_work_id = $_POST['order_work_id'];
        $bucket = 'uploadimg';
        $src = $_FILES["file"]["tmp_name"];
        $dst = '/'.$_POST["filepath"].'/'.$_POST["filename"];
        $folder = '/'.$_POST["filepath"];

        if(!$user_id || !$order_work_id || empty($_FILES) || !$_POST['filepath'] || !$_POST['filename']){
            $this->return['code'] = 201;
            $this->return['description'] = '字段缺失';
            $this->ajaxReturn();
        } 

        // 读取图片的大小宽高
        $tmp_image_detail = getimagesize($src); 
        !empty($tmp_image_detail) && $tmp_image_detail['size'] = filesize($src);
        // var_dump($tmp_image_detail); exit;

        Cosapi::setTimeout(180);

        // 设置COS所在的区域，对应关系如下：华南=gz, 华中=sh, 华北=tj
        Cosapi::setRegion('gz');

        // Create folder in bucket.
        $ret = Cosapi::createFolder($bucket, $folder);
        //var_dump($ret);

        $ret = Cosapi::upload($bucket, $src, $dst);
        //var_dump($ret);

        $this->return['data'] = $ret;
        if ($ret['code'] != '0') {
            $this->return['code'] = 301;
            $this->return['description'] = '上传失败';
            // $this->ajaxReturn();
        }

        // 写入到image数据表
        $data = array(
            'image_url' => 'http://uploadimg-1253710425.cosgz.myqcloud.com/E20170425115139012585793/0/0.jpg', // $ret['source_url'],
            'user_id' => $user_id,
            'width' => $tmp_image_detail[0],
            'height' => $tmp_image_detail[1],
            'source' => 1,
            'quality' => 0
        );
        $this->load->model('Image_model', 'Image');
        $result = $this->Image->add_image($data);

        if(!$result){
            $this->return['code'] = 202;
            $this->return['description'] = '上传失败';
        }else{
            // 写入order_work_image数据表
            if($order_work_id){
                $data = array(
                    'order_work_id' => $order_work_id,
                    'image_id' => $result,
                    'user_id' => $user_id,
                    'status' => 0,
                );
                $this->load->model('OrderWorkImage_model', 'OrderWorkImage');
                $result = $this->OrderWorkImage->add_order_work_image($data);
            }
        }

        $this->ajaxReturn();
    }
    
    public function test(){
        $this->return['data'] = array(
            array('name' => 'sdf'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'qwe'),
            array('name' => 'gas')
        );
        $this->ajaxReturn();
    }
}
