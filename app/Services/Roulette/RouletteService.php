<?php

namespace App\Services\Roulette;

use App\Support\Facades\Random;
use Illuminate\Support\Facades\Config;

class RouletteService
{
    protected $aSet;

    public function __construct()
    {
        $this->aSet = Config::get('RouletteSet.wheel');
    }
    
    /**
     * generateRoulette
     *
     * 生產輪盤結果
     * @return array
     */
    public function generateRoulette()
    {
        $iRandom = $this->getRandom();

        $iResult = $this->aSet[$iRandom];

        $aResultDetails = $this->getResultDetails($iResult);

        return [
            'result' => $iResult,
            'details' => $aResultDetails,
        ];
    }
    
    /**
     * getRandom
     *
     * 生成隨機數
     * @return int
     */
    private function getRandom()
    {
        $iMin = 0;
        $iMax = count($this->aSet) - 1;
        return Random::rand($iMin, $iMax);
    }
    
    /**
     * getResultDetails
     *
     * 生成詳細結果
     * @param  int $_iResult
     * @return array
     */
    private function getResultDetails($_iResult)
    {
        $sOddEven = ($_iResult % 2 === 0) ? '偶數' : '奇數';
        $sColor = ($_iResult === 0) ? '綠色' : (($_iResult % 2 === 1) ? '紅色' : '黑色');

        return [
            'odd_even' => $sOddEven,
            'color' => $sColor,
        ];
    }
}
