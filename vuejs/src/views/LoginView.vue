<template>
  <div>
    <form class="login-form" @submit.prevent="login">
      <div class="form-group">
        <label for="email">Email:</label>
        <input type="email" id="email" v-model="email" class="form-control">
      </div>

      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" v-model="password" class="form-control">
      </div>
      
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <div class="goToReg" >you do not have account? <router-link to="/register">register</router-link></div>    
    <p class="error-message" v-if="errorMessage">{{ errorMessage }}</p>
  </div>
</template>

<script>
import { mapMutations } from 'vuex';

export default {
  data() {
    return {
      email: '',
      password: '',
      errorMessage: '',
    };
  },
  methods: {
    async login() {
      const data = {
        email: this.email,
        password: this.password,
      };
      try {
        const response = await fetch('http://127.0.0.1:8000/api/login', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify(data)
        });
        if (!response.ok) {
          throw new Error(`${response.status}: ${response.statusText}`);
        }
        const responseData = await response.json();
        const user = responseData.user;
        const token = responseData.authorization.token;
        localStorage.setItem('token', token);
        this.$store.commit('setUser', user);
        this.$store.commit('setToken', token);
        this.errorMessage = '';
        this.$router.push({ name: 'blog' });
      } catch (error) {
        this.errorMessage = error.message;
      }
    },
    ...mapMutations(['setUser', 'setToken'])
  }
};
</script>

<style>
.goToReg{
  margin-top: 30px;
}
.login-form {
  max-width: 400px;
  margin: 0 auto;
}

.form-group {
  margin-bottom: 20px;
}

label {
  display: block;
  margin-bottom: 5px;
}

.form-control {
  width: 100%;
  padding: 10px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.btn {
  padding: 10px 20px;
  background-color: #007bff;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.error-message {
  color: red;
}
</style>
