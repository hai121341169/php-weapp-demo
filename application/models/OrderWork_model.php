<?php
class OrderWork_model extends CI_Model {
    private $table_name = 'order_work';
    public function __construct(){
        parent::__construct();
    }

    public function get_order_work($where, $limit = 10, $offset = 0){
        $query = $this->db->where($where)->limit($limit)->offset($offset)->get('order_work');

        // echo $this->db->last_query(); exit;
        return $query->result_array();
    }

    public function get_order_work_detail($where){
        $query = $this->db->where($where)->get('order_work');

        return $query->row_array();
    }
}