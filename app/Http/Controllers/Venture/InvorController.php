<?php

namespace App\Http\Controllers\Venture;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models as Model;

class InvorController extends Controller
{
    public function addInvor (Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'realName' => 'required|string',
            'company' => 'required|string',
            'position' => 'required|string',
            'intro' => 'required|string',
            'meetId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $result = Model\VmInvor::updateOrCreate(['user_id' => $userId, 'real_name' => $realName, 'company' => $company, 'position' => $position, 'intro' => $intro]);
        if ($meetId > 0) {
            Model\VmMeetInvor::firstOrCreate(['meet_id' => $meetId, 'user_id' => $userId]);
            Model\VmUser::where('user_id', $userId)->update(['cur_meet_id' => $meetId]);
        }
        return $this->output(['temp' => $result]);
    }

    //public function delInvor (Request $request)
    //{
    //$params = $this->validation($request, [
    //'userId' => 'required|numeric'
    //]);
    //if ($params === false) {
    //return self::$ERROR1;
    //}
    //extract($params);
    //$result = Model\VmInvor::where('user_id', $userId)->delete();
    //}

    public function updInvorInfo (Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'invorInfo' => 'required|array'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $invorInfo = $this->arrKeyToLine($invorInfo);
        $result = Model\VmInvor::where('user_id', $userId)->update($invorInfo);
        return $this->output(['updated' => $result]);
    }

    public function getInvorInfo (Request $request)
    {
        $params = $this->validation($request, [
            'invorId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $invorInfo = Model\VmInvor::join('user', 'vm_invor.user_id', '=', 'user.user_id')
            ->where('user.user_id', $invorId)
            ->select('user.user_id', 'user.avatar_url', 'user.nick_name', 'vm_invor.real_name', 'vm_invor.company', 'vm_invor.position', 'vm_invor.intro')
            ->first()->toArray();
        return $this->output(['invorInfo' => $invorInfo]);
    }

    public function getMeetInvorList (Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $meetIdArr = Model\VmUser::where('user_id', $userId)->pluck('cur_meet_id');
        $meetId = $meetIdArr[0];
        if ($meetId === 0) {
            $meetId = Model\VmMeeting::max('meet_id');
        }
        $invorIdArr = Model\VmMeetInvor::where('meet_id', $meetId)->pluck('user_id');
        if (count($invorIdArr) === 0) {
            return $this->output(['invorList' => []]);
        }
        $invorList = Model\VmInvor::join('user', 'vm_invor.user_id', '=', 'user.user_id')
            ->whereIn('user.user_id', $invorIdArr)
            ->select('user.user_id', 'user.avatar_url', 'user.nick_name', 'vm_invor.real_name', 'vm_invor.company', 'vm_invor.position', 'vm_invor.intro')
            ->get()->toArray();
        foreach ($invorList as &$value) {
            $invorId = $value['user_id'];
            $score = Model\VmInvorScore::where('invor_id', $invorId)->avg('score');
            $value['invorScore'] = round($score * 100) / 100;
        }
        return $this->output(['invorList' => $invorList]);
    }
}
