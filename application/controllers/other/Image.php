<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use qcloudcos\Cosapi;

class Image extends MY_Controller {

    public function add_image(){
        $user_id = $_POST['user_id'];
        $order_work_id = $_POST['order_work_id'];
        $bucket = 'uploadimg';
        $src = $_FILES["file"]["tmp_name"];

        // 判断参数有效值
        if(!$user_id || empty($_FILES)){
            $this->return['code'] = 201;
            $this->return['description'] = '字段缺失';
            $this->ajaxReturn();
        } 
        // 读取图片的大小宽高
        $tmp_image_detail = getimagesize($src); 
        !empty($tmp_image_detail) && $tmp_image_detail['size'] = filesize($src);

        // 文件夹
        $folder = '/'.date('Y/m/d');

        // 文件重命名
        $tmp_image = explode('.', $_FILES["file"]["name"]);
        $dst = $folder.'/' . date('YmdHis').rand(1000, 9999) . '.' . end($tmp_image);

        Cosapi::setTimeout(180);

        // 设置COS所在的区域，对应关系如下：华南=gz, 华中=sh, 华北=tj
        Cosapi::setRegion('gz');

        // Create folder in bucket.
        $ret = Cosapi::createFolder($bucket, $folder);
        //var_dump($ret);

        $ret = Cosapi::upload($bucket, $src, $dst);
        //var_dump($ret);

        if ($ret['code'] != '0') {
            $this->return['code'] = 301;
            $this->return['description'] = $ret['message'];
            $this->ajaxReturn();
        }

        $ret = $ret['data'];
        $resources_path = explode($bucket, $ret['resource_path']);
        $ret['image_url'] = $this->config->item('image_host') . end($resources_path);;

        // 写入到image数据表
        $data = array(
            'image_url' => $ret['image_url'], // $ret['source_url'],
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
            // 读取图片信息
            $image_detail = array_merge($data, array('id' => $result));
            $this->load->model('User_model', 'User');
            $user_detail = $this->User->get_user(array('id' => $user_id));
            if(!empty($user_detail)){
                $image_detail['avatar_url'] = $user_detail['avatar_url'];
                $image_detail['username'] = $user_detail['username'];
            }

            // 写入order_work_image数据表
            if($order_work_id > 0){
                // 读取order_work数据
                $this->load->model('OrderWork_model', 'OrderWork');
                $where = array('id' => $order_work_id);
                $order_work_detail = $this->OrderWork->get_order_work_detail($where);
                if(!empty($order_work_detail)){
                    $data = array(
                        'order_work_id' => $order_work_id,
                        'image_id' => $result,
                        'user_id' => $user_id,
                        'status' => ($order_work_detail['user_id'] == $user_id ? 1 : 0),
                    );
                    $this->load->model('OrderWorkImage_model', 'OrderWorkImage');
                    $result = $this->OrderWorkImage->add_order_work_image($data);
                    
                    // 图片订单作品信息
                    if($result){
                        $image_detail['order_work_image_id'] = $result;
                        $image_detail['status'] = $data['status'];
                    }
                }
            }

            $this->return['data'] = $image_detail;
        }

        $this->ajaxReturn();
    }
}
