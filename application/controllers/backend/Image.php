<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Image extends CI_Controller {

    public function index(){
    	$this->load->view('backend/picture-list.php');
    }

    public function image_add(){
        $this->load->view('backend/picture-add.php');
    }

    public function image_show(){
        $this->load->view('backend/picture-show.php');
    }

    public function image_list(){
    	$draw = isset($_POST['draw']) ? $_POST['draw'] : 0;
    	$total = 59;
        $this->load->model('OrderWork_model', 'OrderWork');
        $where = array();
        $list = $this->OrderWork->get_order_work($where);
        $work = array('作品一', '作品二', '作品三', '作品四', '作品五', '作品六', '作品七', '作品八', '作品九', '作品十', '作品十一', '作品十二');
        foreach ($list as $key => $value) {
        	$value['work_id'] = $work[$value['work_id']];
        	$value['update_time'] = date('Y-m-d H:i:s', $value['update_time']);
        	$value['add_time'] = date('Y-m-d H:i:s', $value['add_time']);
        	$list[$key] = $value;
        }

    	$data = array(
            "draw" => intval($draw),
            "recordsTotal" => intval($total),
            "recordsFiltered" => intval($total),
            "data" => $list
        );
		echo json_encode($data);
    }
}
