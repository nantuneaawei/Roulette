<?php

namespace App\Http\Controllers\Game;

use App\Http\Controllers\Controller;
use App\Repositories\mydb\Player as PlayerRepositories;
use App\Repositories\RedisRepository as RedisRepositories;
use App\Services\CookieService;
use App\Services\Roulette\GameService;
use App\Services\Roulette\RouletteService;
use Illuminate\Http\Request;

class BetController extends Controller
{
    protected $oPlayerRepositories;
    protected $oRedisRepositories;
    protected $oGameService;
    protected $oRouletteService;
    protected $oCookieService;

    public function __construct(PlayerRepositories $_oPlayerRepositories, RedisRepositories $_oRedisRepositories, GameService $_oGameService, RouletteService $_oRouletteService, CookieService $_oCookieService)
    {
        $this->oPlayerRepositories = $_oPlayerRepositories;
        $this->oRedisRepositories = $_oRedisRepositories;
        $this->oGameService = $_oGameService;
        $this->oRouletteService = $_oRouletteService;
        $this->oCookieService = $_oCookieService;
    }

    public function roulette()
    {
        return view('game.roulette');
    }

    public function placeBet(Request $oRequest)
    {
        $sCookieUID1 = $this->oCookieService->get('uid1');
        $sCookieUID2 = $this->oCookieService->get('uid2');
        $sUID1 = $this->getCryptCookie($sCookieUID1);
        $sUID2 = $this->getCryptCookie($sCookieUID2);
        $iPlayerId = $this->oRedisRepositories->getPlayerIdByCookieUIDs($sUID1, $sUID2);

        $aBet = $oRequest->all();

        $aBetData = $this->oGameService->countTotalBetAmount($aBet, $iPlayerId);
        $iTotalBetAmount = $aBetData['total_bet_amount'];
        $aModifiedBet = $aBetData['bets'];

        //檢查餘額是否大於下注金額
        $bBetAmount = $this->oGameService->checkAmount($iPlayerId, $iTotalBetAmount);
        if ($bBetAmount) {
            //新增下注紀錄
            $bAddBetRecord = $this->oGameService->addMultipleBetRecords($aModifiedBet);
            if ($bAddBetRecord) {
                //扣除餘額
                $bDeductAmount = $this->oGameService->deductPlayerAmount($iPlayerId, $iTotalBetAmount);
                //生成遊戲結果
                $aGame = $this->oRouletteService->generateRoulette();
                return response()->json(['status' => 'true', 'message' => '下注成功']);
            }
        } else {
            return response()->json(['status' => 'false', 'message' => '金額不足']);
        }
    }

    protected function checkUserAuthentication($_oRequest)
    {
        if ($_oRequest->hasCookie('uid1') && $_oRequest->hasCookie('uid2')) {
            return true;
        } else {
            return false;
        }
    }

    protected function getCryptCookie($_iCookie)
    {
        $iDecrypt = decrypt($_iCookie, false);
        $iAssort = explode('|', $iDecrypt);
        return $iAssort[1];
    }
}
