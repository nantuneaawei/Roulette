<template>
  <div>
    <h2>登入帳號</h2>
    <form @submit.prevent="login">
      <div>
        <label for="email">信箱:</label>
        <input type="email" id="email" v-model="email" required>
      </div>
      <div>
        <label for="password">密碼:</label>
        <input type="password" id="password" v-model="password" required>
      </div>
      <button type="submit">登入</button>
    </form>
    <button @click="redirectToRegister">註冊帳號</button>
    <p v-if="errorMessage" class="error">{{ errorMessage }}</p>
  </div>
</template>

<script>
import { mapActions } from 'vuex';

export default {
  data() {
    return {
      email: '',
      password: '',
      errorMessage: '',
    };
  },
  methods: {
    ...mapActions(['loginSuccess']),
    async login() {
      try {
        const response = await axios.post('/login', { email: this.email, password: this.password });
        const { state, message } = response.data;

        if (state) {
          await this.loginSuccess();
          alert(message);
          this.$router.push('/roulette');
        } else {
          this.errorMessage = message;
        }
      } catch (error) {
        console.error('Login failed:', error);
        this.errorMessage = 'Login failed, please try again';
      }
    },
    redirectToRegister() {
      this.$router.push('/register');
    }
  },
};

</script>

<style>
.error {
  color: red;
}
</style>
