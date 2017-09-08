<?php

namespace App\Http\Controllers\Campus;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models as Model;

class ProjectController extends Controller
{
    public function addProject(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'name' => 'required|string',
            'province' => 'required|numeric',
            'tag' => 'required|string',
            'intro' => 'required|string',
            'appType' => 'required|numeric',
            'actId' => 'required|numeric',
            'formId' => 'nullable|string'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        if ($formId) {
            $formObj = Model\UserForm::create([
                'user_id' => $userId,
                'app_type' => $appType,
                'form_id' => $formId,
                'expire_time' => time() + 3600 * 24 * 7,
            ]);
        }        
        if ($request->hasFile('projLogo') && $request->file('projLogo')->isValid()) {
            $fileName = "project-$appType-$userId-" . time() . ".png";
            $request->file('projLogo')->storeAs('logo', $fileName, 'public');
            $logoUrl = "https://www.kingco.tech/storage/logo/$fileName";
        } else {
            $result = Model\User::where('user_id', $userId)->pluck('avatar_url');
            $logoUrl = $result[0];
        }
        $projObj = Model\Project::create([
            'leader_id' => $userId,
            'name' => $name,
            'logo_url' => $logoUrl,
            'province' => $province,
            'tag' => $tag,
            'intro' => $intro,
            'origin' => 1
        ]);
        $projId = $projObj->proj_id;
        Model\ProjMember::Create(['proj_id' => $projId, 'user_id' => $userId, 'app_type' => 1, 'is_leader' => 1]);
        Model\ProjMember::Create(['proj_id' => $projId, 'user_id' => $userId, 'app_type' => 2, 'is_leader' => 1]);
        Model\ProjMember::Create(['proj_id' => $projId, 'user_id' => $userId, 'app_type' => 3, 'is_leader' => 1]);
        switch ($appType) {
        case 1:
            Model\SfUser::where('user_id', $userId)->update(['cur_proj_id' => $projId]);
            break;
        case 2:
            Model\ScUser::where('user_id', $userId)->update(['cur_proj_id' => $projId]);
            break;
        case 3:
            Model\VmUser::where('user_id', $userId)->update(['cur_proj_id' => $projId]);
            break;
        default:
            return self::$ERROR1;
            break;
        }
        $nickName = Model\User::where('user_id', $userId)->first()->nick_name;
        $projName = $name;
        $time = date("Y-m-d H-i-s");
        $msg1 = '';
        $msg2 = '';
        if ($actId) {
            switch ($appType) {
            case 1:
                Model\SfFestProject::create(['fest_id' => $actId, 'proj_id' => $projId]);
                Model\SfUser::where('user_id', $userId)->update(['cur_fest_id' => $actId]);
                Model\User::where([['user_id', $userId], ['festStage', '<', 2]])->update(['festStage' => 2]);
                $festName = Model\SfFestival::where('fest_id', $actId)->first()->name;
                $msg1 = "并加入火种节";
                $msg2 = "\n火种节名称：$festName";
                break;
            case 2:
                Model\ScCampProject::create(['camp_id' => $actId, 'proj_id' => $projId]);
                Model\ScUser::where('user_id', $userId)->update(['cur_camp_id' => $actId]);
                Model\User::where([['user_id', $userId], ['stage', '<', 2]])->update(['stage' => 2]);
                Model\User::where([['user_id', $userId], ['campStage', '<', 2]])->update(['campStage' => 2]);
                $campName = Model\ScCamp::where('camp_id', $actId)->first()->name;
                $msg1 = "并加入加速营";
                $msg2 = "\n加速营名称：$campName";
                break;
            }
        }
        $msg = "{$nickName}创建了项目{$msg1}！\n项目名称：$projName\n创始人：$nickName\n时间：$time" . $msg2;
        $this->sendMsg($msg);
        return $this->output(['proj_id' => $projId]);
    }

    public function delProject(Request $request)
    {
        $params = $this->validation($request, [
            'projId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        Model\Project::where('proj_id', $projId)->delete();
        Model\ProjMember::where('proj_id', $projId)->delete();
        $result = Model\Project::where('proj_id', $projId)->delete();
        Model\SfUser::where('cur_proj_id', $projId)->update(['cur_proj_id' => 0]);
        Model\SfFestProject::where('proj_id', $projId)->delete();
        Model\ScUser::where('cur_proj_id', $projId)->update(['cur_proj_id' => 0]);
        Model\ScCampProject::where('proj_id', $projId)->delete();
        Model\VmUser::where('cur_proj_id', $projId)->update(['cur_proj_id' => 0]);
        Model\VmMeetProject::where('proj_id', $projId)->delete();
        return $this->output(['deleted' => $result]);
    }

    public function updProjInfo(Request $request)
    {
        $params = $this->validation($request, [
            'projId' => 'required|numeric',
            'projInfo' => 'required|array'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $result = Model\Project::where('proj_id', $projId)->update($projInfo);
        return $this->output(['updated' => $result]);
    }

    public function getProjInfo(Request $request)
    {
        $params = $this->validation($request, [
            'projId' => 'required|numeric',
            'appType' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $projInfo = Model\Project::where('proj_id', $projId)
            ->select('proj_id', 'leader_id', 'name', 'intro', 'logo_url', 'province', 'tag', 'origin')
            ->first()->toArray();
        $projInfo['members'] = Model\ProjMember::join('user', 'proj_member.user_id', '=', 'user.user_id')
            ->where([['proj_member.proj_id', $projId], ['app_type', $appType]])
            ->orderBy('proj_member.created_at', 'asc')
            ->select('user.user_id', 'avatar_url', 'nick_name')
            ->get()->toArray();
        return $this->output(['projInfo' => $projInfo]);
    }

    public function getUserProjInfo(Request $request)
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
            $projId = Model\SfUser::where('user_id', $userId)->pluck('cur_proj_id');
            break;
        case 2:
            $projId = Model\ScUser::where('user_id', $userId)->pluck('cur_proj_id');
            break;
        case 3:
            $projId = Model\VmUser::where('user_id', $userId)->pluck('cur_proj_id');
            break;
        default:
            return self::$ERROR1;
            break;
        }
        if ($projId[0] === 0) {
            $projId = Model\ProjMember::where([['app_type', $appType], ['user_id', $userId]])
                ->orderBy('created_at', 'desc')
                ->pluck('proj_id')->toArray();
            if (count($projId) === 0) {
                return $this->output(['projInfo' => ['proj_id' => 0]]);
            }
            switch ($appType) {
            case 1:
                $projId = Model\SfUser::where('user_id', $userId)->update(['cur_proj_id' => $projId[0]]);
                break;
            case 2:
                $projId = Model\ScUser::where('user_id', $userId)->update(['cur_proj_id' => $projId[0]]);
                break;
            case 3:
                $projId = Model\VmUser::where('user_id', $userId)->update(['cur_proj_id' => $projId[0]]);
                break;
            }
        }
        $projInfo = Model\Project::where('proj_id', $projId[0])
            ->select('proj_id', 'leader_id', 'name', 'intro', 'logo_url', 'province', 'tag', 'origin')
            ->first()->toArray();
        $projInfo['members'] = Model\ProjMember::join('user', 'proj_member.user_id', '=', 'user.user_id')
            ->where([['proj_member.proj_id', $projId], ['app_type', $appType]])
            ->orderBy('proj_member.created_at', 'asc')
            ->select('user.user_id', 'avatar_url', 'nick_name')
            ->get()->toArray();
        return $this->output(['projInfo' => $projInfo]);
    }

    public function getUserProjList(Request $request)
    {
        $params = $this->validation($request, [
            'appType' => 'required|numeric',
            'userId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $projIds = Model\ProjMember::where([['app_type', $appType], ['user_id', $userId]])->pluck('proj_id');
        $projList = Model\Project::whereIn('proj_id', $projIds)
            ->select('proj_id', 'name')
            ->orderBy('created_at', 'desc')
            ->get()->toArray();
        return $this->output(['projList' => $projList]);
    }

    public function getAppProjList(Request $request)
    {
        $params = $this->validation($request, [
            'appType' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        switch ($appType) {
            case 0:
                $projIds = Model\Project::pluck('proj_id')->toArray();
                break;
            case 1:
                $projIds = Model\SfFestProject::pluck('proj_id')->toArray();
                break;
            case 2:
                $projIds = Model\ScCampProject::pluck('proj_id')->toArray();
                break;
            case 3:
                $projIds = Model\VmMeetProject::pluck('proj_id')->toArray();
                break;
            case 4:
                $festProjIds = Model\SfFestProject::pluck('proj_id')->toArray();
                $campProjIds = Model\ScCampProject::pluck('proj_id')->toArray();
                $meetProjIds = Model\VmMeetProject::pluck('proj_id')->toArray();
                $actProjIds = array_unique(array_merge($festProjIds, $campProjIds, $meetProjIds));
                $projIds = Model\Project::whereNotIn('proj_id', $actProjIds)->pluck('proj_id')->toArray();
                break;
            default:
                return self::$ERROR1;
                break;
        }
        if (count($projIds) === 0) {
            return $this->output(['projList' => []]);
        }
        $projList = Model\User::join('project', 'user.user_id', '=', 'project.leader_id')
            ->whereIn('project.proj_id', $projIds)
            ->select('user.avatar_url', 'user.nick_name', 'project.proj_id', 'project.name', 'project.tag', 'project.logo_url')
            ->orderBy('project.created_at', 'desc')
            ->get()->toArray();
        return $this->output(['projList' => $projList]);
    }

    public function getActProjList(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'appType' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        if ($appType === 3) {
            $meetIdArr = Model\VmUser::where('user_id', $userId)->pluck('cur_meet_id');
            $meetId = $meetIdArr[0];
            if ($meetId === 0) {
                $meetId = Model\VmMeeting::max('meet_id');
            }
            $projIds = Model\VmMeetProject::where('meet_id', $meetId)->pluck('proj_id');
            if (count($projIds) === 0) {
                return $this->output(['projList' => []]);
            }
            $projList = Model\User::join('project', 'user.user_id', '=', 'project.leader_id')
                ->whereIn('project.proj_id', $projIds)
                ->select('user.avatar_url', 'user.nick_name', 'project.proj_id', 'project.leader_id', 'project.name', 'project.tag', 'project.logo_url')
                ->get()->toArray();
            foreach ($projList as &$project) {
                $projId = $project['proj_id'];
                $tScore = Model\VmProjScore::join('comnt', 'vm_proj_score.comnt_id', '=', 'comnt.comnt_id')
                    ->where([['comnt.tar_type', $appType * 10 + 1], ['comnt.tar_id', $projId]])->avg('vm_proj_score.t_score');
                $aScore = Model\VmProjScore::join('comnt', 'vm_proj_score.comnt_id', '=', 'comnt.comnt_id')
                    ->where([['comnt.tar_type', $appType * 10 + 1], ['comnt.tar_id', $projId]])->avg('vm_proj_score.a_score');
                $bScore = Model\VmProjScore::join('comnt', 'vm_proj_score.comnt_id', '=', 'comnt.comnt_id')
                    ->where([['comnt.tar_type', $appType * 10 + 1], ['comnt.tar_id', $projId]])->avg('vm_proj_score.b_score');
                $cScore = Model\VmProjScore::join('comnt', 'vm_proj_score.comnt_id', '=', 'comnt.comnt_id')
                    ->where([['comnt.tar_type', $appType * 10 + 1], ['comnt.tar_id', $projId]])->avg('vm_proj_score.c_score');
                $project['projScore'] = round(($tScore + $aScore + $bScore + $cScore) / 4 * 100) / 100;
                $project['isMember'] = Model\ProjMember::where([['proj_id', $projId], ['user_id', $userId], ['app_type', $appType]])->count();
            }
        }
        return $this->output(['projList' => $projList]);
    }

    public function getAvlProjList(Request $request)
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
            $projIdArr = Model\SfFestProject::where('fest_id', $actId)->pluck('proj_id')->toArray();
            break;
        case 2:
            $projIdArr = Model\ScCampProject::where('camp_id', $actId)->pluck('proj_id')->toArray();
            break;
        case 3:
            $projIdArr = Model\VmMeetProject::where('meet_id', $actId)->pluck('proj_id')->toArray();
            break;
        default:
            return self::$ERROR1;
            break;
        }
        $projList = Model\Project::where('leader_id', $userId)
            ->whereNotIn('proj_id', $projIdArr)
            ->select('proj_id', 'name')
            ->get()->toArray();
        return $this->output(['projList' => $projList]);
    }

    public function addProjMember(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'projId' => 'required|numeric',
            'appType' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $result = Model\ProjMember::firstOrCreate(['user_id' => $userId, 'proj_id' => $projId, 'app_type' => $appType]);
        switch ($appType) {
        case 1:
            Model\SfUser::where('user_id', $userId)->update(['cur_proj_id' => $projId]);
            Model\User::where([['user_id', $userId], ['festStage', '<', 2]])->update(['festStage' => 2]);
            break;
        case 2:
            Model\ScUser::where('user_id', $userId)->update(['cur_proj_id' => $projId]);
            Model\User::where([['user_id', $userId], ['stage', '<', 2]])->update(['stage' => 2]);
            Model\User::where([['user_id', $userId], ['campStage', '<', 2]])->update(['campStage' => 2]);
            break;
        case 3:
            Model\VmUser::where('user_id', $userId)->update(['cur_proj_id' => $projId]);
            Model\User::where([['user_id', $userId], ['stage', '<', 4]])->update(['stage' => 4]);
            Model\User::where([['user_id', $userId], ['meetStage', '<', 2]])->update(['meetStage' => 2]);
            break;
        }
        return $this->output(['temp' => $result]);
    }

    public function delProjMember(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'projId' => 'required|numeric',
            'appType' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $map = [
            ['user_id', '=', $userId],
            ['proj_id', '=', $projId],
            ['app_type', '=', $appType],
            ['is_leader', '=', 0]
        ];
        $result = Model\ProjMember::where($map)->delete();
        return $this->output(['deleted' => $result]);
    }
}
