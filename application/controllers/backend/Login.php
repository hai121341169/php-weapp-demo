<?php
defined('BASEPATH') OR exit('No direct script access allowed');
use Gregwar\Captcha\CaptchaBuilder;

class Login extends CI_Controller {

    public function index(){
        $this->load->view('backend/index.php');
    }

    public function ulogin(){
        $this->load->view('backend/login.php');
    }

    public function welcome(){
        $data['server'] = $_SERVER;
        $this->load->view('backend/welcome.php', $data);
    }
    
    public function captcha(){
        $builder = new CaptchaBuilder();
        $builder->build(100, 40);

        header('Content-type: image/jpeg');
        $builder->output();
    }
}
