<?php
class OrderWorkImageSort_model extends CI_Model {
    public function __construct(){
        parent::__construct();
    }

    public function get_order_work_image_sort($order_work_id){
        if($order_work_id <= 0) return array();

        $query = $this->db->where(array('order_work_id' => $order_work_id))->get('order_work_image_sort');
        return $query->row_array();
    }
    public function update_order_work_image_sort($data){
        $result = $this->db->replace('order_work_image_sort', $data);
    	return $result;
    }
}