<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends MY_Controller {
	/**
	 * 我发起的上传列表
	 * 
	 */
    public function order_work() {
        $user_id = $this->input->get('user_id');
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        if(!$user_id){
        	$this->return['code'] = 201;
        	$this->return['description'] = '字段缺失';
        	$this->ajaxReturn();
        }

        // 查询条件
        
        $this->load->model('OrderWork_model', 'OrderWork');
        $where = array('user_id' => $user_id);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $list = $this->OrderWork->get_order_work($where, $limit, $offset);
        if(!empty($list)){
            $user_ids = array_unique(array_column($list, 'user_id'));
            $this->load->model('User_model', 'User');
            $user_list = $this->User->get_user_list($user_ids, 'id');

            foreach ($list as $key => &$value) {
                $value['avatar_url'] = $user_list[$value['user_id']]['avatar_url'];
                $value['username'] = $user_list[$value['user_id']]['username'];
            }
        }

        $this->return['data'] = $list;
        $this->ajaxReturn();

    }

    /**
     * 我参入的上传列表
     * 
     */
    public function join_order_work(){
        $user_id = $this->input->get('user_id');
        $page = isset($_GET['page']) ? $_GET['page'] : 1;
        if(!$user_id){
        	$this->return['code'] = 201;
        	$this->return['description'] = '字段缺失';
        	$this->ajaxReturn();
        }

        // 查询order_work_id条件
        $list = array();
        $this->load->model('OrderWorkJoin_model', 'OrderWorkJoin');
        $where = array('user_id' => $user_id);
        $limit = 10;
        $offset = ($page - 1) * $limit;
        $order_work_list = $this->OrderWorkJoin->get_join_order_work($where, $limit, $offset);

        if(!empty($order_work_list)){
            // 查询订单作品详情
            $this->load->model('OrderWork_model', 'OrderWork');
            $where = array('id' => join(',', array_column($order_work_list, 'order_work_id')));
            $list = $this->OrderWork->get_order_work($where); 
            if(!empty($list)){
                $user_ids = array_unique(array_column($list, 'user_id'));
                $this->load->model('User_model', 'User');
                $user_list = $this->User->get_user_list($user_ids, 'id');

                foreach ($list as $key => &$value) {
                    $value['avatar_url'] = $user_list[$value['user_id']]['avatar_url'];
                    $value['username'] = $user_list[$value['user_id']]['username'];
                }
            }
        }

        $this->return['data'] = $list;
        $this->ajaxReturn();
    }

    /**
     * 订单的图片列表
     */ 
     
    public function order_work_image(){
        $order_work_id = $this->input->get('order_work_id');
        $order_sn = $this->input->get('order_sn');
        $work_id = $this->input->get('work_id');
        
        $this->load->model('OrderWork_model', 'OrderWork');
        if(!$order_work_id){
            if(!$order_sn || !$work_id){
                $this->return['code'] = 201;
                $this->return['description'] = '字段缺失';
                $this->ajaxReturn();
            }

            $where = array('order_sn' => $order_sn, 'work_id' => $work_id);
            $order_work_detail = $this->OrderWork->get_order_work_detail($where);
            if(!empty($order_work_detail)){
                $order_work_id = $order_work_detail['id'];
            }
        }else{
            $where = array('id' => $order_work_id);
            $order_work_detail = $this->OrderWork->get_order_work_detail($where);
            if(!empty($order_work_detail)){
                $order_work_id = $order_work_detail['id'];
            }
        }

        // 查询条件
        $list = array();
        // 读取订单图片列表
        if(!$order_work_id){
            $this->return['code'] = 201;
            $this->return['description'] = '字段缺失';
            $this->ajaxReturn();
        }

        $this->load->model('OrderWorkImage_model', 'OrderWorkImage');
        $where = array('order_work_id' => $order_work_id);
        $order_work_list = $this->OrderWorkImage->get_order_work_image($where);
        if(!empty($order_work_list)){
            // 读取图片详情及分组
            $this->load->model('Image_model', 'Image');
            $where = array_column($order_work_list, 'image_id');
            $list = $this->Image->get_image($where);

            if(!empty($list)){
                $user_ids = array_unique(array_column($list, 'user_id'));
                $this->load->model('User_model', 'User');
                $user_list = $this->User->get_user_list($user_ids, 'id');

                foreach ($list as $key => &$value) {
                    $value['avatar_url'] = $user_list[$value['user_id']]['avatar_url'];
                    $value['username'] = $user_list[$value['user_id']]['username'];

                    // 查询order_work_image_id
                    foreach ($order_work_list as $vi) {
                        if($value['id'] == $vi['image_id']) {
                            $value['order_work_image_id'] = $vi['id'];
                            $value['status'] = $vi['status'];
                        }
                    }
                }
            }
        }

        $this->return['data'] = array(
            'order_work_id' => $order_work_id, 
            'status' => $order_work_detail['status'],
            'list' => $list
        );
        $this->ajaxReturn();
    }

    /**
     * 移除照片
     * 
     */
    public function remove_order_work_image(){
        $order_work_image_id = $this->input->get('order_work_image_id');
        if(!$order_work_image_id){
            $this->return['code'] = 201;
            $this->return['description'] = '字段缺失';
            $this->ajaxReturn();
        }

        $this->load->model('OrderWorkImage_model', 'OrderWorkImage');
        $result = $this->OrderWorkImage->delete_order_work_image($order_work_image_id);

        $this->ajaxReturn();
    }

    /**
     * 更新图片
     * Typecho Blog Platform
     * @copyright [copyright]
     * @license   [license]
     * @version   [version]
     * @return    [type]      [description]
     */
    public function update_order_work_image(){
        $order_work_image_id = $this->input->get('order_work_image_id');
        $status = $this->input->get('status');
        if(!$order_work_image_id || !$status){
            $this->return['code'] = 201;
            $this->return['description'] = '字段缺失';
            $this->ajaxReturn();
        }

        $this->load->model('OrderWorkImage_model', 'OrderWorkImage');
        $data = array('status' => $status);
        $result = $this->OrderWorkImage->update_order_work_image($order_work_image_id, $data);

        $this->ajaxReturn();
    }
}
