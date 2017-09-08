<?php

namespace App\Http\Controllers\Venture;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Models as Model;

class MeetingController extends Controller
{
    public function addMeeting(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'name' => 'required|string',
            'intro' => 'required|string',
            'sponsor' => 'required|string',
            'startTime' => 'required|numeric',
            'endTime' => 'required|numeric',
            'addr' => 'required|string'
        ]);
        if ($params === false || !($request->file('meetLogo')->isValid())) {
            return self::$ERROR1;
        }
        extract($params);
        $schlId = Model\VmUser::where('user_id', $userId)->first()->schl_id;
        $fileName = "venture-meeting-$schlId-" . time() . ".png";
        $result = $request->file('meetLogo')->storeAs('logo', $fileName, 'public');
        $meetObj = Model\VmMeeting::create([
            'orger_id' => $userId,
            'schl_id' => $schlId,
            'name' => $name,
            'intro' => $intro,
            'start_time' => $startTime,
            'end_time' => $endTime,
            'addr' => $addr,
            'sponsor' => $sponsor,
            'logo_url' => "https://www.kingco.tech/storage/logo/$fileName" 
        ]);
        $meetId = $meetObj->meet_id;
        Model\VmUser::where('user_id', $userId)->update(['cur_meet_id' => $meetId]);
        return $this->output(['meetId' => $meetId]);
    }

    public function delMeeting(Request $request)
    {
        $params = $this->validation($request, [
            'meetId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        Model\VmMeeting::where('meet_id', $meetId)->delete();
        Model\VmMeetProject::where('meet_id', $meetId)->delete();
        Model\VmMeetInvor::where('meet_id', $meetId)->delete();
        Model\VmUser::where('cur_meet_id', $meetId)->update(['cur_meet_id' => 0]);
        return $this->output(['deleted' => 'success']);
    }

    public function updMeetInfo(Request $request)
    {
        $params = $this->validation($request, [
            'meetId' => 'required|numeric',
            'meetInfo' => 'required|array'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $data = $this->arrayKeyToLine($meetInfo);
        $result = Model\VmMeeting::where('meet_id', $meetId)->update($data);
        return $this->output(['updated' => $result]);
    }

    public function getMeetInfo(Request $request)
    {
        $params = $this->validation($request, [
            'meetId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $meetInfo = Model\VmMeeting::select('orger_id', 'name', 'intro', 'start_time', 'end_time', 'addr', 'sponsor', 'logo_url')->find($meetId)->toArray();
        $invorIds = Model\VmMeetInvor::where('meet_id', $meetId)
            ->pluck('user_id');
        $meetInfo['invors'] = Model\User::whereIn('user_id', $invorIds)
            ->select('user_id', 'avatar_url', 'nick_name')
            ->get()->toArray();
        $projIds = Model\VmMeetProject::where('meet_id', $meetId)->pluck('proj_id');
        $projList = Model\Project::join('user', 'project.leader_id', '=', 'user.user_id')
            ->whereIn('project.proj_id', $projIds)
            ->select('project.proj_id', 'project.name', 'project.logo_url', 'user.avatar_url', 'user.nick_name')
            ->get()->toArray();
        $meetInfo['projList'] = $projList;
        return $this->output(['meetInfo' => $meetInfo]);
    }

    public function getUserMeetInfo(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $meetId = Model\VmUser::where('user_id', $userId)->pluck('cur_meet_id');
        $meetId = $meetId[0];
        if ($meetId === 0) {
            $meetId = Model\VmMeeting::max('meet_id');
        }
        if (!$meetId) {
            return $this->output(['meetInfo' => []]);
        }
        $meetInfo = Model\VmMeeting::select('meet_id', 'orger_id', 'name', 'intro', 'start_time', 'end_time', 'addr', 'sponsor', 'logo_url')->find($meetId)->toArray();
        $meetList = Model\VmMeeting::select('meet_id', 'name')->get()->toArray();
        $meetInfo['meetList'] = $meetList;
        return $this->output(['meetInfo' => $meetInfo]);
    }

    public function addMeetProject(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'meetId' => 'required|numeric',
            'projId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $result = Model\VmMeetProject::firstOrCreate([
            'meet_id' => $meetId,
            'proj_id' => $projId
        ]);
        Model\VmUser::where('user_id', $userId)->update(['cur_meet_id' => $meetId]);
        Model\User::where([['user_id', $userId], ['stage', '<', 4]])->update(['stage' => 4]);
        Model\User::where([['user_id', $userId], ['meetStage', '<', 2]])->update(['meetStage' => 2]);
        return $this->output(['temp' => $result]);
    }

    public function delMeetProject(Request $request)
    {
        $params = $this->validation($request, [
            'meetId' => 'required|numeric',
            'projId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $result = Model\VmMeetProject::where([
            ['meet_id', '=', $meetId],
            ['proj_id', '=', $projId]
        ])->delete();
        return $this->output(['deleted' => $result]);
    }

    public function addMeetInvor(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'meetId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $isInvor = Model\VmInvor::where('user_id', $userId)->count();
        if ($isInvor === 0) {
            return $this->output(['isInvor' => 0]);
        }
        $result = Model\VmMeetInvor::firstOrCreate(['user_id' => $userId, 'meet_id' => $meetId]);
        return $this->output(['isInvor' => 1, 'temp' => $result]);
    }

    public function delMeetInvor(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'meetId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $map = [
            ['user_id', '=', $userId],
            ['meet_id', '=', $meetId]
        ];
        $result = Model\VmMeetInvor::where($map)->delete();
        return $this->output(['deleted' => $result]);
    }
}
