<?php

namespace Tests\Unit\Services\Roulette;

use App\Repositories\Mydb\Bet as BetRepositories;
use App\Repositories\Mydb\Player as PlayerReositories;
use App\Services\Roulette\GameService;
use Mockery;
use PHPUnit\Framework\TestCase;

class GameServiceTest extends TestCase
{
    private $oGameService;
    private $oPlayerRepository;
    private $oBetRepository;

    public function setUp(): void
    {
        parent::setUp();

        $this->oPlayerRepository = Mockery::mock(PlayerReositories::class);

        $this->oBetRepository = Mockery::mock(BetRepositories::class);

        $this->oGameService = new GameService($this->oPlayerRepository, $this->oBetRepository);
    }

    /**
     * testBetAmountLessThanBalance
     * 下注金額小於玩家餘額
     *
     * @group game
     * @return void
     */
    public function testBetAmountLessThanBalance()
    {
        $iPlayerId = 1;
        $iBalance = 500;

        $this->oPlayerRepository->shouldReceive('getPlayerBalance')
            ->with($iPlayerId)
            ->andReturn($iBalance);

        $this->oBetRepository->shouldReceive('addBetRecord')
            ->once()
            ->withArgs([$iPlayerId, Mockery::type('int')])
            ->andReturn(true);

        $iBetAmount = 300;
        $bActual = $this->oGameService->placeBet($iPlayerId, $iBetAmount);

        $bExpected = true;

        $this->assertEquals($bExpected, $bActual);
    }

    /**
     * testBetAmountExceedsBalance
     * 下注金額大於玩家餘額
     *
     * @group game
     * @return void
     */
    public function testBetAmountExceedsBalance()
    {
        $iPlayerId = 1;
        $iBalance = 500;

        $this->oPlayerRepository->shouldReceive('getPlayerBalance')
            ->with($iPlayerId)
            ->andReturn($iBalance);

        $this->oBetRepository->shouldNotReceive('addBetRecord');

        $iBetAmount = 700;
        $bActual = $this->oGameService->placeBet($iPlayerId, $iBetAmount);
        $bExpected = false;

        $this->assertEquals($bExpected, $bActual);
    }

    /**
     * testAddMultipleBetRecords
     * 新增多筆下注紀錄
     * 下注兩筆
     * @group game
     * @return void
     */
    public function testAddMultipleBetRecords()
    {
        $aBets = [
            [
                'player_id' => 1,
                'bet_id' => 1,
                'bet_type' => 'number',
                'bet_content' => '5',
                'bet_amount' => 100,
            ],
            [
                'player_id' => 1,
                'bet_id' => 1,
                'bet_type' => 'color',
                'bet_content' => 'red',
                'bet_amount' => 200,
            ],
        ];

        $this->oBetRepository->shouldReceive('addBetRecord')
            ->times(count($aBets))
            ->with(Mockery::on(function ($bet) {
                return isset($bet['player_id']) && isset($bet['bet_id']) && isset($bet['bet_type']) && isset($bet['bet_content']) && isset($bet['bet_amount']);
            }))
            ->andReturn(true);

        $bActual = $this->oGameService->addMultipleBetRecords($aBets);

        $bExpected = true;

        $this->assertEquals($bExpected, $bActual);
    }

    /**
     * testAddMultipleBetRecords
     * 新增多筆下注紀錄
     * 當只有一筆下注
     * @group game
     * @return void
     */
    public function testAddMultipleBetRecords2()
    {
        $aBets = [
            [
                'player_id' => 1,
                'bet_id' => 1,
                'bet_type' => 'number',
                'bet_content' => '5',
                'bet_amount' => 100,
            ],
        ];

        $this->oBetRepository->shouldReceive('addBetRecord')
            ->times(count($aBets))
            ->with(Mockery::on(function ($bet) {
                return isset($bet['player_id']) && isset($bet['bet_id']) && isset($bet['bet_type']) && isset($bet['bet_content']) && isset($bet['bet_amount']);
            }))
            ->andReturn(true);

        $bActual = $this->oGameService->addMultipleBetRecords($aBets);

        $bExpected = true;

        $this->assertEquals($bExpected, $bActual);
    }

}
