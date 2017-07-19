<?php
class OrderWork_model extends CI_Model {
    private $table_name = 'order_work';
    public function __construct(){
        parent::__construct();
    }

    public function get_order_work($where, $limit = 10, $offset = 0, $sort = 'update_time DESC'){
        if(is_array($where)) $where['is_delete'] = 0;
        else $where .= ' AND is_delete = 0';
        
        $query = $this->db->where($where)->limit($limit)->offset($offset)->order_by($sort)->get('order_work');

        // echo $this->db->last_query(); exit;
        return $query->result_array();
    }

    public function get_order_work_detail($where){
        $query = $this->db->where($where)->get('order_work');

        return $query->row_array();
    }

    public function add_order_work($data){
        $result = $this->db->insert('order_work', $data);
        return $result ? $this->db->insert_id() : 0;
    }

    public function update_order_work($order_work_id, $data){
        $result = $this->db->update('order_work', $data, array('id' => $order_work_id));
        return $result;
    }
}