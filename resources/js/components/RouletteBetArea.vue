<template>
  <div class="betting-area">
    <h2>輪盤遊戲下注區</h2>
    <div class="chip-area">
      <h3>籌碼區</h3>
      <button @click="setBetAmount(100)">100</button>
      <button @click="setBetAmount(500)">500</button>
      <button @click="setBetAmount(1000)">1000</button>
      <button @click="setBetAmount(5000)">5000</button>
      <button @click="setBetAmount(10000)">10000</button>
    </div>
    <div class="number-bets">
      <h3>Numbers</h3>
      <div class="numbers">
        <button v-for="num in numbers" :key="num" @click="placeBet('number', num)">
          {{ num }}
          <span v-bind:style="{ color: bets['number'][num] ? 'red' : '' }" v-if="bets['number'][num]">{{
        bets['number'][num] }}</span>
        </button>
      </div>
    </div>
    <div class="color-bets">
      <h3>Color</h3>
      <button @click="placeBet('color', 'red')">紅<span v-if="bets['color']['red']">({{ bets['color']['red']
          }})</span></button>
      <button @click="placeBet('color', 'black')">黑<span v-if="bets['color']['black']">({{ bets['color']['black']
          }})</span></button>
    </div>
    <div class="parity-bets">
      <h3>Odd/Even</h3>
      <button @click="placeBet('parity', 'odd')">奇<span v-if="bets['parity']['odd']">({{ bets['parity']['odd']
          }})</span></button>
      <button @click="placeBet('parity', 'even')">偶<span v-if="bets['parity']['even']">({{ bets['parity']['even']
          }})</span></button>
    </div>
    <button id="betButton" @click="startGame">開始遊戲</button>
    <button @click="cancelAllBets">取消所有下注</button>
  </div>
</template>

<script>
import { sendBetsData } from '../bet';

export default {
  data() {
    return {
      numbers: Array.from({ length: 37 }, (_, index) => index),
      bets: {
        number: Array.from({ length: 37 }, () => 0),
        color: { red: 0, black: 0 },
        parity: { odd: 0, even: 0 },
      },
      betAmount: 0,
    };
  },
  methods: {
    setBetAmount(bet_amount) {
      this.betAmount = bet_amount;
    },
    placeBet(bet_type, bet_content) {
      if (this.betAmount === 0) {
        alert('尚未選擇籌碼! 請先選擇籌碼。');
        return;
      }

      if (bet_type === 'number') {
        this.bets[bet_type][bet_content] += this.betAmount;
      } else {
        this.bets[bet_type][bet_content] = this.betAmount;
      }
      console.log('Placing bet:', bet_type, bet_content, 'Amount:', this.betAmount);
    },
    startGame() {
      console.log('Starting game. Place your bets!');
      const totalBets = Object.values(this.bets.number).reduce((acc, cur) => acc + cur, 0)
        + this.bets.color.red + this.bets.color.black
        + this.bets.parity.odd + this.bets.parity.even;

      if (totalBets === 0) {
        alert('尚未下注! 請先選擇籌碼並下注。');
        return;
      }

      const betsData = [];
      for (const [bet_type, bets] of Object.entries(this.bets)) {
        for (const [bet_content, bet_amount] of Object.entries(bets)) {
          if (bet_amount > 0) {
            betsData.push({ bet_type, bet_content, bet_amount });
          }
        }
      }
      console.log('Bets Data:', betsData);

      sendBetsData(betsData)
        .then(data => {
          console.log('Bet placed successfully:', data);
        })
        .catch(error => {
          console.error('Error placing bet:', error);
        });
    },
    cancelAllBets() {
      this.bets = {
        number: Array.from({ length: 37 }, () => 0),
        color: { red: 0, black: 0 },
        parity: { odd: 0, even: 0 },
      };
      console.log('All bets canceled.');
    },
    placeBets(betsData) {
      console.log('Sending bets data to server:', betsData);
    },
  },
};
</script>
