<template>
  <div class="register">
    <form class="register-form" @submit.prevent="register">
      <div class="form-group">
        <label for="name">Name:</label>
        <input type="text" id="name" v-model="name" class="form-control">
      </div>

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
        <div class="goToLog" >you already have account? <router-link to="/login">login</router-link></div>    

        <p class="error-message" v-if="errorMessage">{{ errorMessage }}</p>

  </div>
</template>

<script>
import { mapMutations } from 'vuex';

export default {
data() {
  return {
    name: '',
    email: '',
    password:'',
    response: {},
    errorMessage: '',
  }
},
  methods: {
    async register() {
      const data = {
        name: this.name,
        email: this.email,
        password: this.password,
      };
      try {
        const response = await fetch('http://127.0.0.1:8000/api/register', {
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
        const token = responseData.authorization.token;
        localStorage.setItem('token', token);
        this.setToken(token);
        this.errorMessage = '';
        this.$router.push({ name: 'blog' });
      } catch (error) {
        this.errorMessage = error.message;
      }
    },
    ...mapMutations(['setToken'])
  }
}
</script>

<style>
.goToLog{
  margin-top: 30px;
}
.register-form {
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
</style>