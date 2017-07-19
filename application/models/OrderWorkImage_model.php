<?php
class OrderWorkImage_model extends CI_Model {
    public function __construct(){
        parent::__construct();
    }

    public function get_order_work_image_detail($order_work_image_id){
        $query = $this->db->where(array('id' => $order_work_image_id))->get('order_work_image');
        // echo $this->db->last_query(); exit;
        return $query->row_array();
    }

    public function get_order_work_image($where){
    	$limit = $this->config->item('max_row');
        $query = $this->db->where($where)->get('order_work_image', $limit);
        // echo $this->db->last_query(); exit;
        return $query->result_array();
    }

    public function delete_order_work_image($order_work_image_id){
    	$result = $this->db->delete('order_work_image', array('id' => $order_work_image_id));
    	return $result;
    }

    public function add_order_work_image($data){
        $result = $this->db->insert('order_work_image', $data);
    	return $result ? $this->db->insert_id() : 0;
    }

    public function update_order_work_image($order_work_image_id, $data){
        $result = $this->db->update('order_work_image', $data, array('id' => $order_work_image_id));
    	return $result;
    }
}