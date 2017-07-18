<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Api Class
 *
 */
class MY_Model extends CI_Model {
	public function __construct()
    {
        parent::__construct();
    }

	public function Format($array, $field = 'id') {
		$result = array();
		if(empty($array)) return $result;

		foreach ($array as $key => $value) {
			$result[$value[$field]] = $value;
		}

		return $result;
	}
}
