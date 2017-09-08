<?php

namespace App\Http\Controllers\Spark;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models as Model;

class ProgressController extends Controller
{
    public function getProgInfo(Request $request)
    {
        $params = $this->validation($request, [
            'projId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $progInfo = Model\SfProjProgress::where('proj_id', $projId)
            ->select('step_num', 'image_url', 'content')
            ->orderBy('step_num', 'asc')
            ->get()->toArray();
        return $this->output(['progInfo' => $progInfo]);
    }

    public function updProgImage(Request $request)
    {
        $params = $this->validation($request, [
            'stepNum' => 'required|numeric',
            'projId' => 'required|numeric'
        ]);
        if ($params === false || !($request->file('progImage')->isValid())) {
            return self::$ERROR1;
        }
        extract($params);
        $fileName = "spark-proj-step-$stepNum-" . time() . ".png";
        $result = $request->file('progImage')->storeAs('progress', $fileName, 'public');
        $imageUrl = "https://www.kingco.tech/storage/progress/$fileName";
        $isExist = Model\SfProjProgress::where([['proj_id', $projId], ['step_num', $stepNum]])->count();
        if (!$isExist) {
            Model\SfProjProgress::create([
                'proj_id' => $projId,
                'step_num' => $stepNum,
                'image_url' => $imageUrl
            ]);
        } else {
            Model\SfProjProgress::where([['proj_id', $projId], ['step_num', $stepNum]])
                ->update(['image_url' => $imageUrl]);
        }
        $isFestProject = Model\SfFestProject::where('proj_id', $projId)->count();
        $imageNum = Model\SfProjProgress::where([['proj_id', $projId], ['image_url', '<>', '']])->count();
        $contentNum = Model\SfProjProgress::where([['proj_id', $projId], ['content', '<>', '']])->count();
        if ($isFestProject && $imageNum === 7 && $contentNum === 7) {
            $memberIds = Model\ProjMember::where('proj_id', $projId)->pluck('user_id');
            Model\User::whereIn('user_id', $memberIds)->where('stage', '<', 1)->update(['stage' => 1]);
            Model\User::whereIn('user_id', $memberIds)->where('festStage', '<', 3)->update(['festStage' => 3]);
        }
        return $this->output(['imageUrl' => $imageUrl]);
    }

    public function updProgContent(Request $request)
    {
        $params = $this->validation($request, [
            'stepNum' => 'required|numeric',
            'projId' => 'required|numeric',
            'content' => 'required|string'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $isExist = Model\SfProjProgress::where([['proj_id', $projId], ['step_num', $stepNum]])->count();
        if (!$isExist) {
            Model\SfProjProgress::create([
                'proj_id' => $projId,
                'step_num' => $stepNum,
                'content' => $content
            ]);
        } else {
            Model\SfProjProgress::where([['proj_id', $projId], ['step_num', $stepNum]])
                ->update(['content' => $content]);
        }
        $isFestProject = Model\SfFestProject::where('proj_id', $projId)->count();
        $imageNum = Model\SfProjProgress::where([['proj_id', $projId], ['image_url', '<>', '']])->count();
        $contentNum = Model\SfProjProgress::where([['proj_id', $projId], ['content', '<>', '']])->count();
        if ($isFestProject && $imageNum === 7 && $contentNum === 7) {
            $memberIds = Model\ProjMember::where('proj_id', $projId)->pluck('user_id');
            Model\User::whereIn('user_id', $memberIds)->where('stage', '<', 1)->update(['stage' => 1]);
            Model\User::whereIn('user_id', $memberIds)->where('festStage', '<', 3)->update(['festStage' => 3]);
        }
        return $this->output(['temp' => 'success']);
    }
}
