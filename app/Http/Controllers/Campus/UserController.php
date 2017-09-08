<?php

namespace App\Http\Controllers\Campus;

include_once __DIR__ . '/aes/wxBizDataCrypt.php';
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models as Model;

class UserController extends Controller
{
    public function login(Request $request)
    {
        $params = $this->validation($request, [
            'code' => 'required|string',
            'rawData' => 'required|string',
            'iv' => 'required|string',
            'appType' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
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
        $url = "https://api.weixin.qq.com/sns/jscode2session?appid=$appId&secret=$appSecret&js_code=$code&grant_type=authorization_code";
        $resJson = file_get_contents($url);
        $resArr = json_decode($resJson, true);
        if (array_key_exists('errcode', $resArr)) {
            return self::$ERROR2;
        }
        $pc = new \WXBizDataCrypt($appId, $resArr['session_key']);
        $errCode = $pc->decryptData($rawData, $iv, $data);
        if ($errCode !== 0) {
            return self::$ERROR2;
        } 
        $data = json_decode($data, true);
        $unionId = isset($data['unionId']) ? $data['unionId'] : $data['openId'];
        $userInfo = Model\User::updateOrCreate(['union_id' => $unionId], [
            //'open_id' => $data['openId'],
            'nick_name' => $data['nickName'],
            'avatar_url' => $data['avatarUrl']
        ]);
        $userId = $userInfo->user_id;
        switch ($appType) {
        case 1:
            Model\SfUser::updateOrCreate(['user_id' => $userId], ['open_id' => $data['openId']]);
            Model\User::where([['user_id', $userId], ['festStage', '<', 1]])->update(['festStage' => 1]);
            break;
        case 2:
            Model\ScUser::updateOrCreate(['user_id' => $userId], ['open_id' => $data['openId']]);
            Model\User::where([['user_id', $userId], ['campStage', '<', 1]])->update(['campStage' => 1]);
            break;
        case 3:
            Model\VmUser::updateOrCreate(['user_id' => $userId], ['open_id' => $data['openId']]);
            Model\User::where([['user_id', $userId], ['meetStage', '<', 1]])->update(['meetStage' => 1]);
            break;
        }
        return $this->getUserInfo($request, $userId, $appType);
    }

    public function getUserInfo(Request $request, $userId = 0, $appType = 0) 
    {
        if ($userId === 0 || $appType === 0) {
            $params = $this->validation($request, [
                'userId' => 'required|numeric',
                'appType' => 'required|numeric'
            ]);
            if ($params === false) {
                return self::$ERROR1;
            }
            extract($params);
        }
        $userInfo = Model\User::where('user_id', $userId)
            ->select('user_id', 'avatar_url', 'nick_name', 'role', 'stage', 'festStage', 'campStage', 'meetStage')
            ->first()->toArray();
        switch ($appType) {
        case 1:
            $appUserInfo = Model\SfUser::where('user_id', $userId)
                ->select('schl_id', 'cur_fest_id', 'cur_proj_id')
                ->first()->toArray();
            break;
        case 2:
            $appUserInfo = Model\ScUser::where('user_id', $userId)
                ->select('schl_id', 'cur_camp_id', 'cur_proj_id')
                ->first()->toArray();
            break;
        case 3:
            $appUserInfo = Model\VmUser::where('user_id', $userId)
                ->select('schl_id', 'cur_meet_id', 'cur_proj_id')
                ->first()->toArray();
            break;
        case 4:
            $appUserInfo = Model\SchlAdmin::where('user_id', $userId)
                ->pluck('schl_id')->toArray();
            $appUserInfo = isset($appUserInfo[0]) ? ['schlId' => $appUserInfo[0]] : ['schlId' => 0];
            break;
        default:
            $appUserInfo = [];
            break;
        }
        $userInfo = array_merge($userInfo, $appUserInfo);
        return $this->output(['userInfo' => $userInfo]);
    }

    public function addAdmin(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'adminId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $result = Model\User::where('user_id', $userId)->update(['role' => 1]);
        return $this->output(['updated' => $result]);
    }

    public function delAdmin(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'adminId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $result = Model\User::where('user_id', $userId)->update(['role' => 0]);
        return $this->output(['updated' => $result]);
    }

    public function chgCurProject(Request $request)
    {
        $params = $this->validation($request, [
            'appType' => 'required|numeric',
            'userId' => 'required|numeric',
            'projId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $exist = Model\Project::find($projId);
        if (!$exist) {
            return self::$ERROR2;
        }
        switch ($appType) {
        case 1:
            $result = Model\SfUser::where('user_id', $userId)->update(['cur_proj_id' => $projId]);
            break;
        case 2:
            $result = Model\ScUser::where('user_id', $userId)->update(['cur_proj_id' => $projId]);
            break;
        case 3:
            $result = Model\VmUser::where('user_id', $userId)->update(['cur_proj_id' => $projId]);
            break;
        default:
            return self::$ERROR2;
            break;
        }
        return $this->output(['updated' => $result]);
    }

    public function chgCurActivity(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'appType' => 'required|numeric',
            'actId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        switch ($appType) {
        case 1:
            Model\SfUser::where('user_id', $userId)->update(['cur_fest_id' => $actId]);
            $actProjIds = Model\SfFestProject::where('fest_id', $actId)->pluck('proj_id');
            break;
        case 2:
            Model\ScUser::where('user_id', $userId)->update(['cur_camp_id' => $actId]);
            $actProjIds = Model\SfFestProject::where('fest_id', $actId)->pluck('proj_id');
            break;
        case 1:
            Model\SfUser::where('user_id', $userId)->update(['cur_fest_id' => $actId]);
            $actProjIds = Model\ScCampProject::where('camp_id', $actId)->pluck('proj_id');
            break;
        default:
            return self::$ERROR1;
        }
        $myProjIds = Model\ProjMember::whereIn('proj_id', $actProjIds)->where([['user_id', $userId], ['app_type', $appType]])->pluck('proj_id')->toArray();
        if (count($myProjIds) > 0) {
            switch ($appType) {
            case 1:
                Model\SfUser::where('user_id', $userId)->update(['cur_proj_id' => $myProjIds[0]]);
                break;
            case 1:
                Model\ScUser::where('user_id', $userId)->update(['cur_proj_id' => $myProjIds[0]]);
                break;
            case 1:
                Model\VmUser::where('user_id', $userId)->update(['cur_proj_id' => $myProjIds[0]]);
                break;
            }
        } 
        $curProjId = isset($myProjIds[0]) ? $myProjIds[0] : 0;
        return $this->output(['curProjId' => $curProjId]);
    }

    public function getQrcode(Request $request){
        $params = $this->validation($request, [
            'path' => 'required|string',
            'name' => 'required|string',
            'appType' => 'numeric|string'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $filePath = __DIR__ . "/../../../../public/static/qrcode/$name.png";
        if (is_file($filePath)) {
            return $this->output(['exist' => 1]);
        }
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
        $resData = file_get_contents($url);
        $resArr = json_decode($resData, true);
        $accessToken = $resArr['access_token'];
        if ($accessToken){
            $url = "https://api.weixin.qq.com/cgi-bin/wxaapp/createwxaqrcode?access_token=$accessToken";
            $data = json_encode(array('path'=>$path, 'width'=>200));
            $opts = array(
                'http'=> array(
                    'method'=>'POST',
                    'header'=>"Content-type: application/x-www-form-urlencoded",
                    'content'=>$data
                )
            );
            $context = stream_context_create($opts);
            $resData = file_get_contents($url, false, $context);
            $result = file_put_contents($filePath, $resData);
            return $this->output(['temp' => $result]);
        }
    }

    public function getWxCode(Request $request){
        $params = $this->validation($request, [
            'path' => 'required|string',
            'name' => 'required|string',
            'appType' => 'numeric|string'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $filePath = __DIR__ . "/../../../../public/static/wxcode/$name.png";
        if (is_file($filePath)) {
            return $this->output(['exist' => 1]);
        }
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
        $resData = file_get_contents($url);
        $resArr = json_decode($resData, true);
        $accessToken = $resArr['access_token'];
        if ($accessToken){
            $url = "https://api.weixin.qq.com/wxa/getwxacode?access_token=$accessToken";
            $data = json_encode(array('path'=>$path, 'width'=>200));
            $opts = array(
                'http'=> array(
                    'method'=>'POST',
                    'header'=>"Content-type: application/x-www-form-urlencoded",
                    'content'=>$data
                )
            );
            $context = stream_context_create($opts);
            $resData = file_get_contents($url, false, $context);
            $result = file_put_contents($filePath, $resData);
            return $this->output(['temp' => $result]);
        }
    }
}
