<?php
class OrderWorkJoin_model extends CI_Model {
    public function __construct(){
        parent::__construct();
    }

    public function get_join_order_work($where, $limit=10, $offset=0){
        $query = $this->db->select(array('order_work_id'))->where($where)->limit($limit)->offset($offset)->order_by('add_time', 'DESC')->get('order_work_join', 10);
        // echo $this->db->last_query(); exit;
        $result = $query->result_array();

        return $result;
    }

    public function add_order_work_join($data){
    	$table_name = $this->db->dbprefix('order_work_join');
        $result = $this->db->query("insert ignore into `{$table_name}` value('".$data['order_work_id']."', '".$data['user_id']."', '".$data['add_time']."')");
    	return $result;
    }
}