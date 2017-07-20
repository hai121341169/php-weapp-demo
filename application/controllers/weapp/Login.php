<?php
defined('BASEPATH') OR exit('No direct script access allowed');

use Xiang\WechatApp\Decode\WXBizDataCrypt;

class Login extends MY_Controller {

    public function index(){
        $repeat = 0;
        $code = $_GET['code'];
        $signature = $_GET['signature'];
        $iv = $_GET['iv'];
        $encrypted_data = $_GET['encryptedData'];

        $wx_config = $this->config->item('wx');
        $appid = $wx_config['appid'];
        $appsecret = $wx_config['secret'];


        if(empty($_SESSION['3rd_session'])){
            $repeat = 1;
            $url = $wx_config['url']."?appid=".$appid."&secret=".$appsecret."&js_code=".$code."&grant_type=".$wx_config['grant_type'];

            $this->load->helper('url');
            $arr = vget($url);  // 一个使用curl实现的get方法请求
            $arr = json_decode($arr, true);
            $openid = $arr['openid'];
            $session_key = $arr['session_key'];

            $_SESSION['3rd_session'] = array(
                'openid' => $openid,
                'session_key' => $session_key
            );
        }else{
            $openid = $_SESSION['3rd_session']['openid'];
            $session_key = $_SESSION['3rd_session']['session_key'];
        }

        // 数据签名校验
        $signature2 = sha1($_GET['rawData'].$session_key);  //记住不应该用TP中的I方法，会过滤掉必要的数据
        if ($signature != $signature2) {
            $this->return['code'] = 201;
            $this->return['description'] = '数据签名验证失败！';
            $this->ajaxReturn();
        }

        $pc = new WXBizDataCrypt($appid, $session_key);
        $errCode = $pc->decryptdata($encrypted_data, $iv, $data);
        $data = json_decode($data, true);

        if ($errCode == 0) {
            $this->load->model('User_model', 'User');
            // 写入数据库
            $unionid = isset($data['unionid']) ? $data['unionid'] : '';
            $where = array('openid' => $data['openId'], 'unionid' => $unionid);
            $user_detail = $this->User->get_user($where);
            if(empty($user_detail)){
                $user_detail = array(
                    'username' => $data['nickName'],
                    'nickname' => $data['nickName'],
                    'gender' => $data['gender'],
                    'openid' => $data['openId'],
                    'unionid' => $unionid,
                    'avatar_url' => $data['avatarUrl'],
                    'add_time' => time()
                );
                $result = $this->User->add_user($user_detail);
                $user_detail['id'] = $result;
            }

            $user_detail['repeat'] = $repeat;
            $this->return['data'] = $user_detail;
        } else {
            $this->return['code'] = 401;
            $this->return['description'] = $errCode;
        }
        
        $this->ajaxReturn();
    }
}
