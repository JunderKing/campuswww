<?php

namespace App\Http\Controllers\Campus;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models as Model;

class SchoolController extends Controller
{
    public function addSchool(Request $request)
    {
        $params = $this->validation($request, [
            'adminId' => 'required|numeric',
            'userId' => 'required|numeric',
            'name' => 'required|string',
            'intro' => 'required|string',
            'province' => 'required|numeric'
        ]);
        if ($params === false || !($request->file('schlLogo')->isValid())) {
            return self::$ERROR1;
        }
        extract($params);
        $fileName = "school-$userId-" . time() . ".png";
        $result = $request->file('schlLogo')->storeAs('logo', $fileName, 'public');
        $schlObj = Model\School::create([
            'name' => $name,
            'intro' => $intro,
            'province' => $province,
            'logo_url' => "https://www.kingco.tech/storage/logo/$fileName",
            'admin_id' => $userId
        ]);
        $schlId = $schlObj->schl_id;
        Model\SchlAdmin::create([
            'schl_id' => $schlId,
            'user_id' => $userId
        ]);
        return $this->output(['schlId' => $schlId]);
    }

    public function delSchool(Request $request)
    {
        $params = $this->validation($request, [
            'schlId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $result = Model\School::where('schl_id', $schlId)->delete();
        Model\SchlAdmin::where('schl_id', $schlId)->delete();
        //是否要删除学校下所有的活动？
        return $this->output(['deleted' => $result]);
    }

    public function getSchlInfo(Request $request)
    {
        $params = $this->validation($request, [
            'schlId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $schoolInfo = Model\School::where('schl_id', $schlId)
            ->select('name', 'intro', 'logo_url', 'province')
            ->first()->toArray();
        return $this->output(['schoolInfo' => $schoolInfo]);
    }

    public function getSchlList(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $adminInfo['schlList'] = Model\School::select('schl_Id', 'name', 'logo_url', 'province')
            ->get()->toArray();
        $adminInfo['adminList'] = Model\User::where('role', 1)
            ->select('user_id', 'avatar_url', 'nick_name')
            ->get()->toArray();
        return $this->output(['adminInfo' => $adminInfo]);
    }

    public function addSchlAdmin(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'schlId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $result = Model\SchlAdmin::firstOrCreate([
            'schl_id' => $schlId,
            'user_id' => $userId
        ]);
        return $this->output(['temp' => $result]);
    }

    public function delSchlAdmin(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'schlId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $result = Model\SchlAdmin::where(['schl_id', $schlId], ['user_id', $userId])->delete();
        return $this->output(['deleted' => $result]);
    }

    public function getSchlAdminList(Request $request)
    {
        $params = $this->validation($request, [
            'schlId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $schlAdminList = Model\SchlAdmin::join('user', 'schl_admin.user_id', '=', 'user.user_id')
            ->where('schl_admin.schl_id', $schlId)
            ->select('avatar', 'nick_name', 'user.user_id')
            ->get()->toArray();
        return $this->output(['schlAdminList' => $schlAdminList]);
    }

    public function addOrger(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'appType' => 'required|numeric',
            'schlId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        switch ($appType) {
            case 1;
            $result = Model\SfUser::where('user_id', $userId)->update(['schl_id' => $schlId]);
            break;
            case 2;
            $result = Model\ScUser::where('user_id', $userId)->update(['schl_id' => $schlId]);
            break;
            case 3;
            $result = Model\VmUser::where('user_id', $userId)->update(['schl_id' => $schlId]);
            break;
        }
        return $this->output(['updated' => $result]);
    }

    public function delOrger(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'appType' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        switch ($appType) {
        case 1:
            $result = Model\SfUser::where('user_id', $userId)->update(['schl_id' => 0]);
            break;
        case 2:
            $result = Model\ScUser::where('user_id', $userId)->update(['schl_id' => 0]);
            break;
        case 3:
            $result = Model\VmUser::where('user_id', $userId)->update(['schl_id' => 0]);
            break;
        }
        return $this->output(['updated' => $result]);
    }

    public function getOrgerInfo (Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'schlId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $orgerInfo['schlAdmins'] = Model\User::join('schl_admin', 'user.user_id', '=', 'schl_admin.user_id')
            ->select('user.user_id', 'avatar_url', 'nick_name')
            ->where('schl_id', $schlId)
            ->get()->toArray();
        $orgerInfo['festOrgers'] = Model\User::join('sf_user', 'user.user_id', '=', 'sf_user.user_id')
            ->select('user.user_id', 'avatar_url', 'nick_name')
            ->where('schl_id', $schlId)
            ->get()->toArray();
        $orgerInfo['campOrgers'] = Model\User::join('sc_user', 'user.user_id', '=', 'sc_user.user_id')
            ->select('user.user_id', 'avatar_url', 'nick_name')
            ->where('schl_id',$schlId)
            ->get()->toArray();
        $orgerInfo['meetOrgers'] = Model\User::join('vm_user', 'user.user_id', '=', 'vm_user.user_id')
            ->select('user.user_id', 'avatar_url', 'nick_name')
            ->where('schl_id', $schlId)
            ->get()->toArray();
        return $this->output(['orgerInfo' => $orgerInfo]);
    }
}
