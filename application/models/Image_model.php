<?php
class Image_model extends CI_Model {
    public function __construct(){
        parent::__construct();
    }

    public function get_image($where){
    	$limit = $this->config->item('max_row');
        $query = $this->db->where_in('id', $where)->get('image', $limit);
        $result = $query->result_array();

        if(!empty($result)){
            foreach ($result as $key => &$value) {
                !preg_match("/^(http:\/\/|https:\/\/).*$/", $value['image_url']) && $value['image_url'] = $this->config->item('image_host') . $value['image_url'];
            }
        }

        return $result;
    }

    public function add_image($data){
        $result = $this->db->insert('image', $data);
    	return $result ? $this->db->insert_id() : 0;
    }
}