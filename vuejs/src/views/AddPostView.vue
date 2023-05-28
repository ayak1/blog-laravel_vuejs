<template>
  <div class="addPostView">
    <router-link to="/my-blog" class="btn">My Blogs</router-link>
    <form @submit.prevent="createPost" class="post-form">
      <label for="title">Title:</label>
      <input type="text" id="title" v-model="title" class="form-input">
      <label for="body">Body:</label>
      <textarea id="body" v-model="body" class="form-textarea"></textarea>
      <button type="submit" class="form-button">Create Post</button>
    </form>
    <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
    <div v-if="post" class="addedPost">
      <p class="p">Post added successfully</p> 
      <h1>{{ post.title }}</h1>
      <p>{{ post.body }}</p>
    </div>
  </div>
</template>

<script>
import fetchPostApi from '../services/fetchPostApi';
export default {
  data() {
    return {
      title: '',
      body: '',
      post: null,
      errorMessage: '', 
    };
  },
  methods: {
    async createPost() {
      try {
      const post = {
        title: this.title,
        body: this.body,
      };
        const token = localStorage.getItem('token');
        const responseData = await fetchPostApi.post('post', post,token);
        this.post = responseData.post;
        this.title = '';
        this.body = '';
        this.errorMessage = ''; 
      } catch (error) {
        this.errorMessage = error.message;
      }
    },
  },
};
</script>
<style>
.addedPost{
  text-align: left;
  border: 1px solid green;
  padding: 5px;
}
.addedPost.p{
  border-bottom: 1px solid;
}
.addPostView{
  display:flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
}
.addPostView .btn{
  color:#fff;
  background: #00b309;
  padding:20px;
  border-radius:10px;
  align-self: flex-end;
  margin-right: 50px;
  font-weight: 500;
  text-decoration: none;
}
.post-form {
  width:40%;
  margin-bottom: 20px;
}
.form-input {
  width: 100%;
  padding: 5px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.form-textarea {
  width: 100%;
  height: 150px;
  padding: 5px;
  border: 1px solid #ccc;
  border-radius: 4px;
}

.form-button {
  padding: 5px 10px;
  background-color: #007bff;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.form-button:hover {
  background-color: #0056b3;
}

.error-message {
  color: red;
  margin-top: 10px;
}
</style>