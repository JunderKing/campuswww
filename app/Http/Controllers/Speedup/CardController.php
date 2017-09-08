<?php

namespace App\Http\Controllers\Speedup;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models as Model;

class CardController extends Controller
{
  public function addCard(Request $request)
  {
    $params = $this->validation($request, [
      'gridId' => 'required|numeric',
      'title' => 'required|string',
      'assumption' => 'required|string'
    ]);
    if ($params === false) {
      return self::$ERROR1;
    }
    extract($params);
    $cardObj = Model\ScProjCard::create([
      'grid_id' => $gridId,
      'title' => $title,
      'assumption' => $assumption
    ]);
    $cardId = $cardObj->card_id;
    return $this->output(['cardId'=>$cardId]);
  }

  public function delCard(Request $request)
  {
    $params = $this->validation($request, [
      'cardId' => 'required|numeric'
    ]);
    if ($params === false) {
      return self::$ERROR1;
    }
    extract($params);
    $result = Model\ScProjCard::find($cardId)->delete();
    return $this->output(['deleted' => $result]);
  }

  public function updCardInfo(Request $request)
  {
    $params = $this->validation($request, [
      'cardId' => 'required|numeric',
      'cardInfo' => 'required|array',
    ]);
    if ($params === false) {
      return self::$ERROR1;
    }
    extract($params);
    $result = Model\ScProjCard::where('card_id', $cardId)->update($cardInfo);
    return $this->output(['temp' => $result]);
  }

  public function getCardInfo(Request $request)
  {
    $params = $this->validation($request, [
      'cardId' => 'required|numeric'
    ]);
    if ($params === false) {
      return self::$ERROR1;
    }
    extract($params);
    $cardInfo = Model\ScProjCard::select('card_id', 'title', 'assumption', 'result', 'status', 'created_at')
      ->find($cardId)->toArray();
    return $this->output(['cardInfo' => $cardInfo]);
  }

  public function getGridCardList(Request $request)
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
    $gridInfo = Model\ScProjGrid::where($map)->first();
    if (!$gridInfo) {
      return $this->output(['cardList' => []]);
    }
    $gridId = $gridInfo->grid_id;
    $cardList = Model\ScProjCard::where('grid_id', $gridId)
      ->select('card_id', 'title', 'assumption', 'status', 'created_at')
      ->orderBy('created_at', 'desc')
      ->get()->toArray();
    return $this->output(['cardList' => $cardList]);
  }

  public function getProjCardList(Request $request)
  {
    $params = $this->validation($request, [
      'projId' => 'required|numeric'
    ]);
    if ($params === false) {
      return self::$ERROR1;
    }
    extract($params);
    $gridIds = Model\ScProjGrid::where('proj_id', $projId)->pluck('grid_id');
    $cardList = Model\ScProjCard::where('status', 0)
      ->whereIn('grid_id', $gridIds)
      ->select('card_id', 'title', 'assumption', 'status', 'created_at')
      ->orderBy('created_at', 'desc')
      ->get()->toArray();
    return $this->output(['cardList' => $cardList]);
  }
}
