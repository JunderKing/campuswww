<?php

namespace App\Http\Controllers\Speedup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models as Model;

class RecordController extends Controller
{
  public function getRecList(Request $request)
  {
    $params = $this->validation($request, [
      'projId' => 'required|numeric'
    ]);
    if ($params === false) {
      return self::$ERROR1;
    }
    extract($params);
    $recList = Model\ScProjRecord::where('proj_id', $projId)
      ->orderBy('date', 'asc')
      ->select('rec_id', 'date', 'content')
      ->get()->toArray();
    return $this->output(['recList' => $recList]);
  }

  public function getRecInfo(Request $request)
  {
    $params = $this->validation($request, [
      'recId' => 'required|numeric'
    ]);
    if ($params === false) {
      return self::$ERROR1;
    }
    extract($params);
    $recInfo = Model\ScProjRecord::select('rec_id', 'date', 'content')->find($recId);
    return $this->output(['recInfo' => $recInfo]);
  }

  public function addRecord(Request $request)
  {
    $params = $this->validation($request, [
      'projId' => 'required|numeric',
      'date' => 'required|numeric',
      'content' => 'required|string'
    ]);
    if ($params === false) {
      return self::$ERROR1;
    }
    extract($params);
    $result = Model\ScProjRecord::create(['proj_id' => $projId, 'date' => $date, 'content' => $content]);
        $isCampProject = Model\ScCampProject::where('proj_id', $projId)->count();
        $recordNum = Model\ScProjRecord::where('proj_id', $projId)->count();
        if ($isCampProject && $recordNum >= 12) {
            $memberIds = Model\ProjMember::where('proj_id', $projId)->pluck('user_id');
            Model\User::whereIn('user_id', $memberIds)->where('stage', '<', 3)->update(['stage' => 3]);
            Model\User::whereIn('user_id', $memberIds)->where('campStage', '<', 3)->update(['campStage' => 3]);
        }
    return $this->output(['temp' => $result]);
  }

  public function updRecInfo(Request $request)
  {
    $params = $this->validation($request, [
      'recId' => 'required|numeric',
      'date' => 'required|numeric',
      'content' => 'required|string'
    ]);
    if ($params === false) {
      return self::$ERROR1;
    }
    extract($params);
    $result = Model\ScProjRecord::where('rec_id', $recId)
      ->update(['date' => $date, 'content' => $content]);
    return $this->output(['updated' => $result]);
  }

  public function delRecord (Request $request)
  {
    $params = $this->validation($request, [
      'recId' => 'required|numeric'
    ]);
    if ($params === false) {
      return self::$ERROR1;
    }
    extract($params);
    $result = Model\ScProjRecord::where('rec_id', $recId)->delete();
    return $this->output(['deleted' => $result]);
  }
}
