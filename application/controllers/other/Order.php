<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order extends MY_Controller {

    public function index(){
        $order_sn = isset($_POST['order_sn']) ? $_POST['order_sn'] : '';
        $work_id = isset($_POST['work_id']) ? $_POST['work_id'] : '';
        $user_id = isset($_POST['user_id']) ? $_POST['user_id'] : 0;
        if(!$user_id || !$order_sn || $work_id < 0){
            $this->return['code'] = 201;
            $this->return['description'] = '字段缺失';
            $this->ajaxReturn();
        }

        $this->load->model('OrderWork_model', 'OrderWork');
        $where = array('order_sn' => $order_sn, 'work_id' => $work_id);
        $detail = $this->OrderWork->get_order_work_detail($where);

        if(!empty($detail)){
            if($detail['status'] > 0){
                $description = array('订单正在处理中', '订单等待接单', '订单已经被已接单', '订单已经处理结束');
                $this->return['code'] = 402;
                $this->return['description'] = $description[$detail['status']];
                $this->ajaxReturn();
            }else if($detail['user_id'] != $user_id){
                $this->return['code'] = 402;
                $this->return['description'] = '订单已经被人上传过了, 分享才可上传';
                $this->ajaxReturn();
            }else{
                $this->return['data'] = $detail;
            }
        }else{
            // 创建新记录
            $current_time = time();
            $data = array(
                'order_sn' => $order_sn,
                'work_id' => $work_id,
                'user_id' => $user_id,
                'add_time' => $current_time,
                'update_time' => $current_time
            );
            $result = $this->OrderWork->add_order_work($data);
            if(!$result){
                $this->return['code'] = 500;
                $this->return['description'] = '创建订单作品失败';
            }else{
                $this->return['description'] = '新建订单作品成功';
                $this->return['data'] = array_merge($data, array('id' => $result));
            }
        }

        $this->ajaxReturn();
    }

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
            $ids = join(',', array_column($order_work_list, 'order_work_id'));
            $list = $this->OrderWork->get_order_work('id in (' . $ids . ')', 10, 0, 'FIELD(`id`, ' . $ids . ')'); 
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
     * 删除订单作品
     * Typecho Blog Platform
     * @copyright [copyright]
     * @license   [license]
     * @version   [version]
     * @return    [type]      [description]
     */
    public function remove_order_work(){
        $order_work_id = $this->input->get('order_work_id');

        // 判断order_work_id
        if(!$order_work_id){
            $this->return['code'] = 201;
            $this->return['description'] = '字段缺失';
            $this->ajaxReturn();
        }

        $this->load->model('OrderWork_model', 'OrderWork');
        $where = array('id' => $order_work_id);
        $order_work_detail = $this->OrderWork->get_order_work_detail($where);

        if($order_work_detail['status'] > 0){
            $status_txt = array('订单作品在处理中', '订单作品等待接单, 不能删除', '订单作品已被接单, 不能删除', '订单作品处理结束, 不能删除');

            $this->return['code'] = 201;
            $this->return['description'] = $status_txt[$order_work_detail['status']];
            $this->ajaxReturn();
        }

        $result = $this->OrderWork->update_order_work($order_work_id, array('is_delete' => 1, 'update_time' => time()));

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
            // 查询排序
            $sort = array('add_time DESC');
            $this->load->model('OrderWorkImageSort_model', 'OrderWorkImageSort');
            $sort_detail = $this->OrderWorkImageSort->get_order_work_image_sort($order_work_id);
            if(!empty($sort_detail) && $sort_detail['sort']){
                $sort = 'FIELD(`id`, ' . $sort_detail['sort'] . ')';
            }
            $list = $this->Image->get_image($where, $sort);

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
            'user_id' => $order_work_detail['user_id'],
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
        // 查询订单作品图片
        $order_work_image_detail = $this->OrderWorkImage->get_order_work_image_detail($order_work_image_id);
        if(empty($order_work_image_detail)){
            $this->return['code'] = 404;
            $this->return['description'] = '图片不存在';
        }else if($order_work_image_detail['status'] > 0){
            $status_text = array('临时区', '图片已经进入待提交区，暂不允许删除', '作品已提交，暂不允许删除');
            $this->return['code'] = 401;
            $this->return['description'] = $status_text[$order_work_image_detail['status']];
        }else{
            $result = $this->OrderWorkImage->delete_order_work_image($order_work_image_id);
        }

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
        if(!$order_work_image_id || $status < 0){
            $this->return['code'] = 201;
            $this->return['description'] = '字段缺失';
            $this->ajaxReturn();
        }
        $this->load->model('OrderWorkImage_model', 'OrderWorkImage');
        $data = array('status' => $status);
        $result = $this->OrderWorkImage->update_order_work_image($order_work_image_id, $data);
        $this->ajaxReturn();
    }

    /**
     * 订单作品图片排序
     * Typecho Blog Platform
     * @copyright [copyright]
     * @license   [license]
     * @version   [version]
     * @return    [type]      [description]
     */
    public function save_order_work_image(){
        $order_work_id = isset($_POST['order_work_id']) ? $_POST['order_work_id'] : '';
        $sort = isset($_POST['sort']) ? $_POST['sort'] : array();
        $save = isset($_POST['save']) ? $_POST['save'] : 0;

        if(!$order_work_id || empty($sort)){
            $this->return['code'] = 201;
            $this->return['description'] = '字段缺失';
            $this->ajaxReturn();
        }

        $this->load->model('OrderWorkImageSort_model', 'OrderWorkImageSort');
        $data = array(
            'order_work_id' => $order_work_id,
            'sort' => $sort
        );
        $result = $this->OrderWorkImageSort->update_order_work_image_sort($data);
        if($result){
            if($save > 0){
                // 更新order_work
                $this->load->model('OrderWork_model', 'OrderWork');
                $where = array('id' => $order_work_id);
                $result = $this->OrderWork->update_order_work($order_work_id, array('status' => 1, 'update_time' => time()));
            }   
        }else{
            $this->return['code'] = 500;
            $this->return['description'] = '保存失败';
        }
        $this->ajaxReturn();
    }
}
