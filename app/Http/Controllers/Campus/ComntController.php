<?php

namespace App\Http\Controllers\Campus;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models as Model;

class ComntController extends Controller
{
    public function addComnt(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'tarType' => 'required|numeric',
            'tarId' => 'required|numeric',
            'content' => 'required|string',
            'formId' => 'nullable|string'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $appType = (int)substr($tarType, 0, 1);
        if ($formId) {
            $formObj = Model\UserForm::create([
                'user_id' => $userId,
                'app_type' => $appType,
                'form_id' => $formId,
                'expire_time' => time() + 3600 * 24 * 7,
            ]);
        }
        if ($tarType == 11) {
            $fromUserData = Model\User::where('user_id', $userId)->first();
            $nickName = $fromUserData->nick_name;
            $projData = Model\Project::where('proj_id', $tarId)->first();
            $projName = $projData->name;
            $leaderId = $projData->leader_id;
            $toUserData = Model\SfUser::where('user_id', $leaderId)->first();
            $openId = $toUserData->open_id;
            $formData = Model\UserForm::where([
                ['user_id', $leaderId],
                ['app_type', $appType],
                ['expire_time', '>', time()],
                ['is_used', 0]
            ])->first();
            if ($formData && $openId) {
                $toFormId = $formData->form_id;
                $dataArr = array(
                    'keyword1' => array(
                        'value' => $projName,
                        'color' => '#3498DB'
                    ),
                    'keyword2' => array(
                        'value' => $content,
                        'color' => '#3498DB'
                    ),
                    'keyword3' => array(
                        'value' => $nickName,
                        'color' => '#3498DB'
                    )
                );
                $result = self::sendTplMsg($appType, $openId, $toFormId, $dataArr);
                $recordId = $formData->record_id;
                $flag = Model\UserForm::where('record_id', $recordId)
                    ->update(['is_used' => 1]);
            }
        }
        $comntObj = Model\Comnt::create([
            'comntor_id' => $userId,
            'tar_type' => $tarType,
            'tar_id' => $tarId,
            'content' => $content
        ]);
        $comntId = $comntObj->comnt_id;
        return $this->output(['comntId' => $comntId]);
    }

    public function delComnt(Request $request)
    {
        $params = $this->validation($request, [
            'comntId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $result = Model\Comnt::where('comnt_id', $comntId)->delete();
        Model\Reply::where('comnt_id', $comntId)->delete();
        Model\SfProjScore::where('comnt_id', $comntId)->delete();
        Model\VmProjScore::where('comnt_id', $comntId)->delete();
        Model\VmInvorScore::where('comnt_id', $comntId)->delete();
        return $this->output(['deleted' => $result]);
    }

    public function getComnt(Request $request)
    {
        $params = $this->validation($request, [
            'tarType' => 'required|numeric',
            'tarId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        if ($tarType === 41) {
            $comnts = Model\Comnt::where('tar_id', $tarId)
                ->whereIn('tar_type', [11, 21, 31, 41])
                ->join('user', 'comnt.comntor_id', '=', 'user.user_id')
                ->select('user.avatar_url', 'user.nick_name', 'comnt.comnt_id', 'comnt.comntor_id', 'comnt.content', 'comnt.updated_at')
                ->orderBy('comnt.updated_at', 'desc')
                ->get()->toArray();
        } else {
            $comnts = Model\Comnt::where([['tar_type', $tarType], ['tar_id', $tarId]])
                ->join('user', 'comnt.comntor_id', '=', 'user.user_id')
                ->select('user.avatar_url', 'user.nick_name', 'comnt.comnt_id', 'comnt.comntor_id', 'comnt.content', 'comnt.updated_at')
                ->orderBy('comnt.updated_at', 'desc')
                ->get()->toArray();
        }
        $comntIds = Model\Comnt::where([['tar_type', $tarType], ['tar_id', $tarId]])->pluck('comnt_id');
        $replies = Model\Reply::whereIn('comnt_id', $comntIds)
            ->join('user', 'reply.replier_id', '=', 'user.user_id')
            ->select('user.nick_name', 'reply.comnt_id', 'reply.content')
            ->orderBy('reply.created_at', 'desc')
            ->get()->toArray();
        for ($index= 0; $index < count($comnts); $index++) {
            $comnts[$index]['replies'] = array();
            foreach($replies as $item) {
                if ($comnts[$index]['comnt_id']===$item['comnt_id']) {
                    $comnts[$index]['replies'][] = $item;
                }
            }
        }      
        if ($tarType === 11||$tarType === 31||$tarType === 32) {
            switch ($tarType) {
            case 11:
                $score = Model\SfProjScore::whereIn('comnt_id', $comntIds)
                    ->select('comnt_id', 'a_score', 'b_score', 'c_score', 't_score')
                    ->get()->toArray();
                break;
            case 31:
                $score = Model\VmProjScore::whereIn('comnt_id', $comntIds)
                    ->select('comnt_id', 'a_score', 'b_score', 'c_score', 't_score')
                    ->get()->toArray();
                break;
            case 32:
                $score = Model\VmInvorScore::whereIn('comnt_id', $comntIds)
                    ->select('comnt_id', 'score')
                    ->get()->toArray();
                break;
            }
            for ($index= 0; $index < count($comnts); $index++) {
                foreach($score as $item) {
                    if ($comnts[$index]['comnt_id']===$item['comnt_id']) {
                        $comnts[$index]['score'] = $item;
                        break;
                    }
                }
            }      
        }
        return $this->output(['comnts' => $comnts]);
    }

    public function upsComntScore(Request $request)
    {
        $params = $this->validation($request, [
            'tarType' => 'required|numeric',
            'tarId' => 'required|numeric',
            'userId' => 'required|numeric',
            'scores' => 'required|array',
            'content' => 'required|string'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $scores = $this->arrayKeyToLine($scores);
        switch ($tarType) {
        case 11:
            $database = 'sf_proj_score';
            break;
        case 31:
            $database = 'vm_proj_score';
            break;
        case 32:
            $database = 'vm_invor_score';
            break;
        default:
            return self::$ERROR1;
            break;
        }
        $comntIdArr = Model\Comnt::join($database, 'comnt.comnt_id', '=', "$database.comnt_id")
            ->where([['comnt.tar_type', $tarType], ['comnt.tar_id', $tarId], ['comnt.comntor_id', $userId]])
            ->pluck('comnt.comnt_id');
        if (count($comntIdArr) == 0) {
            $comntObj = Model\Comnt::create([
                'comntor_id' => $userId,
                'tar_type' => $tarType,
                'tar_id' => $tarId,
                'content' => $content
            ]);
            $comntId = $comntObj->comnt_id;
        } else {
            $comntId = $comntIdArr[0];
            Model\Comnt::where('comnt_id', $comntId)->update(['content' => $content]);
        }
        switch ($tarType) {
        case 11:
            Model\SfProjScore::updateOrCreate(['comnt_id' => $comntId], $scores);
            break;
        case 31:
            Model\VmProjScore::updateOrCreate(['comnt_id' => $comntId], $scores);
            break;
        case 32:
            Model\VmInvorScore::updateOrCreate(['comnt_id' => $comntId], $scores);
            break;
        }
        return $this->output(['temp' => 'success']);
    }

    public function getComntScore(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'tarType' => 'required|numeric',
            'tarId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        switch ($tarType) {
        case 11:
            $projScore = Model\SfProjScore::join('comnt', 'sf_proj_score.comnt_id', '=', 'comnt.comnt_id')
                ->where([['comnt.tar_type', $tarType], ['comnt.tar_id', $tarId], ['comnt.comntor_id', $userId]])
                ->select('t_score', 'a_score', 'b_score', 'c_score', 'comnt.content')
                ->first();
            break;
        case 31:
            $projScore = Model\VmProjScore::join('comnt', 'sf_proj_score.comnt_id', '=', 'comnt.comnt_id')
                ->where([['comnt.tar_type', $tarType], ['comnt.tar_id', $tarId], ['comnt.comntor_id', $userId]])
                ->select('t_score', 'a_score', 'b_score', 'c_score', 'comnt.content')
                ->first();
            break;
        case 32:
            $projScore = Model\VmInvorScore::join('comnt', 'sf_proj_score.comnt_id', '=', 'comnt.comnt_id')
                ->where([['comnt.tar_type', $tarType], ['comnt.tar_id', $tarId], ['comnt.comntor_id', $userId]])
                ->select('score', 'comnt.content')
                ->first();
            break;
        }
        $projScore = is_null($projScore) ? [] : $projScore->toArray();
        return $this->output(['projScore' => $projScore]);
    }

    public function addReply(Request $request)
    {
        $params = $this->validation($request, [
            'userId' => 'required|numeric',
            'comntId' => 'required|numeric',
            'content' => 'required|string',
            'formId' => 'nullable|string'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $comntObj = Model\Comnt::where('comnt_id', $comntId)->first();
        $comntTarType = $comntObj->tar_type;
        $comntTarId = $comntObj->tar_id;
        $comntorId = $comntObj->comntor_id;
        $appType = (int)substr($comntTarType, 0, 1);
        if ($formId) {
            $formObj = Model\UserForm::create([
                'user_id' => $userId,
                'app_type' => $appType,
                'form_id' => $formId,
                'expire_time' => time() + 3600 * 24 * 7,
            ]);
        }
        if ($comntTarType == 11) {
            $fromUserData = Model\User::where('user_id', $userId)->first();
            $nickName = $fromUserData->nick_name;
            $projData = Model\Project::where('proj_id', $comntTarId)->first();
            $projName = $projData->name;
            $toUserData = Model\SfUser::where('user_id', $comntorId)->first();
            $openId = $toUserData->open_id;
            $formData = Model\UserForm::where([
                ['user_id', $comntorId],
                ['app_type', $appType],
                ['expire_time', '>', time()],
                ['is_used', 0]
            ])->first();
            if ($formData && $openId) {
                $toFormId = $formData->form_id;
                $dataArr = array(
                    'keyword1' => array(
                        'value' => $projName,
                        'color' => '#3498DB'
                    ),
                    'keyword2' => array(
                        'value' => $content,
                        'color' => '#3498DB'
                    ),
                    'keyword3' => array(
                        'value' => $nickName,
                        'color' => '#3498DB'
                    )
                );
                $result = self::sendTplMsg($appType, $openId, $toFormId, $dataArr);
                $recordId = $formData->record_id;
                $flag = Model\UserForm::where('record_id', $recordId)
                    ->update(['is_used' => 1]);
            }
        }
        $result = Model\Reply::create([
            'comnt_id' => $comntId,
            'replier_id' => $userId,
            'content' => $content
        ]);
        return $this->output(['temp' => $result]);
    }
}
