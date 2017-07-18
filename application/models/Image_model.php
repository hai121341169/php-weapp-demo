<?php
class Image_model extends CI_Model {
    public function __construct(){
        parent::__construct();
    }

    public function get_image($where){
    	$limit = $this->config->item('max_row');
        $query = $this->db->where_in('id', $where)->get('image', $limit);

        return $query->result_array();
    }

    public function add_image($data){
        $result = $this->db->insert('image', $data);
    	return $result ? $this->db->insert_id() : 0;
    }
}