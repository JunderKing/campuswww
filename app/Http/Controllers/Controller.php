<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Validator;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public static $SUCCESS = ['errcode' => 0, 'errmsg' => 'Success'];
    public static $ERROR1 = ['errcode' => 1, 'errmsg' => 'Param Error'];
    public static $ERROR2 = ['errcode' => 2, 'errmsg' => 'Data Error'];
    public static $ERROR3 = ['errcode' => 3, 'errmsg' => 'Output Error'];

    public function validation(Request $request, Array $rules)
    {
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return false;
        } else {
            $params = $request->only(array_keys($rules));
            foreach ($params as $key => &$value) {
                if (strpos($rules[$key], 'numeric')!==false) {
                    $value = intval($value);
                }
            }
            return $params;
        }
    }

    public function output(Array $data)
    {
        $isValid = true;
        if (count($data) === 0) {
            $isValid = false;
        }
        array_walk_recursive($data, function($value, $key) use (&$isValid){
            if (is_null($value) || ($key === 'temp' && $value === false)) {
                $isValid = false;
                return;
            } 
        });
        //if (!$isValid) {
        if (false) {
            return self::$ERROR3;
        } else {
            unset($data['temp']);
            $result = $this->arrayKeyToCamel($data);
            return array_merge(self::$SUCCESS, $result);
        }
    }

    public function sendMsg($content)
    {
        //echo $content;
        //$content = 'Hello robot!%';
        //$content = 'hello';
        //$this->sendRobotMsg($content);
    }

    //public function sendTplMsg($userId, Array $param)
    //{
        //$url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appSecret";
        //$resData = file_get_contents($url);
        //$resArr = json_decode($resData, true);
        //$accessToken = $resArr['access_token'];
        //$url = "https://api.weixin.qq.com/cgi-bin/message/template/send?access_token=$accessToken";
        //$param = array(
            //'name'=>'fdipzone',
            //'gender'=>'male',
            //'age'=>30
        //);
        //$urlinfo = parse_url($url);
        //$host = $urlinfo['host'];
        //$path = $urlinfo['path'];
        //$query = isset($param)? http_build_query($param) : '';
        //$port = 80;
        //$errno = 0;
        //$errstr = '';
        //$timeout = 10;
        //$fp = fsockopen($host, $port, $errno, $errstr, $timeout);
        //$out = "POST ".$path." HTTP/1.1\r\n";
        //$out .= "host:".$host."\r\n";
        //$out .= "content-length:".strlen($query)."\r\n";
        //$out .= "content-type:application/x-www-form-urlencoded\r\n";
        //$out .= "connection:close\r\n\r\n";
        //$out .= $query;
        //fputs($fp, $out);
        //fclose($fp);
    //}

    //public function sendRobotMsg($content = 'Errmsg')
    //{
        //$url = "http://web.weslack.cn/zzl/AutomationTest_004cc5281256e661e36a23f08775be5d";
        //$urlArr = parse_url($url);
        //$host = $urlArr['host'];
        //$path = $urlArr['path'];
        //$port = 80;
        //$errno = 0;
        //$errstr = 'errmsg';
        //$timeout = 10;
        //$fp = fsockopen($host, $port, $errno, $errstr, $timeout);
        //$header = "POST ".$path." HTTP/1.1\r\n";
        //$header .= "host:".$host."\r\n";
        //$header .= "content-length:".strlen($content)."\r\n";
        //$header .= "content-type:text/plain\r\n";
        //$header .="charset=utf-8\r\n";
        //$header .= "connection:close\r\n\r\n";
        //$header .= $content;
        //$result = fwrite($fp, $header);
        //fclose($fp);
    //}

    public function arrayKeyToCamel (Array $array)
    {
        $newArray = array();
        foreach ($array as $key => $value) {
            $newKey = preg_replace_callback('/([-_]+([a-z]{1}))/i', function($matches){
                return strtoupper($matches[2]);
            }, $key);
            $newArray[$newKey] = is_array($value) ? $this->arrayKeyToCamel($value) : $value;
        }
        return $newArray;
    }

    public function arrayKeyToLine (Array $array)
    {
        $newArray = array();
        foreach ($array as $key => $value) {
            $newKey = preg_replace_callback('/([A-Z]{1})/', function($matches){
                return '_'.strtolower($matches[0]);
            }, $key);
            $newArray[$newKey] = is_array($value) ? $this->arrayKeyToCamel($value) : $value;
        }
        return $newArray;
    }

    public static function curlPost($url, $dataArr)
    {
        $dataJson = json_encode($dataArr);
        $length = strlen($dataJson);
        $curlObj = curl_init();
        curl_setopt($curlObj, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curlObj, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($curlObj, CURLOPT_HEADER, 0);
        curl_setopt($curlObj, CURLOPT_POST, 1);
        curl_setopt($curlObj, CURLOPT_URL, $url);
        curl_setopt($curlObj, CURLOPT_POSTFIELDS, $dataJson);
        curl_setopt($curlObj, CURLOPT_HTTPHEADER, array(
            "Content-type: application/json; charset=utf-8",
            "Content-length: $length"
        ));
        $result = curl_exec($curlObj);
        curl_close($curlObj);
        return $result;
    }

    public static function sendTplMsg ($appType, $openId, $formId, $dataArr)
    {
        switch ($appType) {
        case 1:
            $appId = "wx981c0f2acb244293";
            $appSecret = "802f929b8e2e937483ef719215a5b1cf";
            break;
        case 2:
            $appId = "wxd94ad08cbeb19cea";
            $appSecret = "00a9bdf7fe8f8b1b8c9143f4426c25f3";
            break;
        case 3:
            $appId = "wx92f10f1fb67ef51e";
            $appSecret = "745f91c316673e69bb7434662ce88ee0";
            break;
        case 4:
            $appId = "wx574b06f8d36fc4bf";
            $appSecret = "ac0dd7cb06ebd3ebf78b990d3fd3e021";
            break;
        default:
            return self::$ERROR2;
        }
        $url = "https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=$appId&secret=$appSecret";
        $resJson = file_get_contents($url);
        $resArr = json_decode($resJson, true);
        $accessToken = $resArr['access_token'];
        $url = "https://api.weixin.qq.com/cgi-bin/message/wxopen/template/send?access_token=$accessToken";
        $templateId = 'cKN_IeSMQFGuVj52FQnFhNT8MHwY_omHCAg9vGFLUgo';
        $postData = array(
            'touser' => $openId,
            'template_id' => $templateId,
            //'page' => $page,
            'form_id' => $formId,
            'data' => $dataArr,
        );
        $resJson = self::curlPost($url, $postData);
        $resArr = json_decode($resJson);

        return $resArr;
    }
}
