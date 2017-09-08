<?php

namespace App\Http\Controllers\Speedup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models as Model;

class GridController extends Controller
{
    public function updGridInfo(Request $request)
    {
        $params = $this->validation($request, [
            'gridId' => 'required|numeric',
            'content' => 'required|string'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $result = Model\ScProjGrid::where('grid_id', $gridId)->update(['content' => $content]);
        Model\ScProjGridLog::create(['grid_id' => $gridId, 'content' => $content]);
        return $this->output(['updated' => $result]);
    }

    public function getGridInfo(Request $request)
    {
        $params = $this->validation($request, [
            'projId' => 'required|numeric',
            'gridNum' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $map = [
            ['proj_id', '=', $projId],
            ['grid_num', '=', $gridNum]
        ];
        $gridObj = Model\ScProjGrid::firstOrCreate(['proj_id' => $projId, 'grid_num' => $gridNum]);
        $content = is_null($gridObj->content) ? '' : $gridObj->content;
        $gridInfo = ['gridId' => $gridObj->grid_id, 'content' => $content];
        return $this->output(['gridInfo' => $gridInfo]);
    }

    public function getGridLog(Request $request)
    {
        $params = $this->validation($request, [
            'gridId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $logList = Model\ScProjGridLog::where('grid_id', $gridId)
            ->select('content', 'created_at')
            ->orderBy('created_at', 'desc')
            ->get()->toArray();
        return $this->output(['logList' => $logList]);
    }

    public function getCanvasInfo(Request $request)
    {
        $params = $this->validation($request, [
            'projId' => 'required|numeric'
        ]);
        if ($params === false) {
            return self::$ERROR1;
        }
        extract($params);
        $cardNum = array();
        for ($i = 0; $i < 8; $i++) {
            $cardNum[$i] = Model\ScProjCard::join('sc_proj_grid', 'sc_proj_card.grid_id', '=', 'sc_proj_grid.grid_id')
                ->where([['proj_id', $projId], ['grid_num', $i + 1], ['status', 0]])->count();
        }
        $canvasInfo['cardNum'] = $cardNum;
        return $this->output(['canvasInfo' => $canvasInfo]);
    }
}
