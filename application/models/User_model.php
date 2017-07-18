<?php
class User_model extends MY_Model {
    public function __construct(){
        parent::__construct();
    }

    public function get_user($where){
        $query = $this->db->where($where)->get('user');
        // echo $this->db->last_query(); exit;
        return $query->row_array();
    }

    public function get_user_list($where, $format_field = ''){
        $result = array();
        $query = $this->db->where_in('id', $where)->get('user');
        // echo $this->db->last_query(); exit;
        $result = $query->result_array();
        if($format_field != ''){
            return $this->format($result, $format_field);
        }
        return $result;
    }

    public function add_user($data){
        $result = $this->db->insert('user', $data);
    	return $result ? $this->db->insert_id() : 0;
    }
}