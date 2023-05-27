<template>
  <div class="myBlog">
    <h2>My posts</h2>
    <div class="buttons">
      <router-link to="/add-post" class="btn">Add Post</router-link>
    <router-link to="/" class="btn">Blog</router-link>
    </div>
    <table class="post-table">
      <thead>
        <tr>
          <th>Title</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="post in posts" :key="post.id">
          <td>{{ post.title }}</td>
          <td>
            <router-link :to="{ name: 'show-post', params: { id: post.id } }" class="action-link">Show</router-link>
            <router-link :to="{ name: 'edit-post', params: { id: post.id } }" class="action-link">Edit</router-link>
            <button @click="deletePost(post.id)" class="delete-button">Delete</button>
          </td>
        </tr>
      </tbody>
    </table>
    <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
  </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  computed: {
    ...mapState(['token']),
  },
  data() {
    return {
      posts: [],
      errorMessage: ''
    };
  },

  created() {
    this.fetchPosts();
  },
  
  methods: {
    async fetchPosts() {
      try {
        const token = localStorage.getItem('token');
        const response = await fetch('http://127.0.0.1:8000/api/user_posts', {
          method: 'GET',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
        });
        if (!response.ok) {
          throw new Error(`${response.status}: ${response.statusText}`);
        }
        const responseData = await response.json();
        this.posts = responseData.posts;
        this.errorMessage = '';
      } catch (error) {
        this.errorMessage = error.message;
      }
    },
    async deletePost(id) {
      try {
        const token = localStorage.getItem('token');
        const response = await fetch(`http://127.0.0.1:8000/api/post/${id}`, {
          method: 'DELETE',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
        });
        if (!response.ok) {
          throw new Error(`${response.status}: ${response.statusText}`);
        }
        // Remove the deleted post from the local posts array
        this.posts = this.posts.filter(post => post.id !== id);

        this.errorMessage = '';
      } catch (error) {
        this.errorMessage = error.message;
      }
    }
  }
}
</script>

<style>
.myBlog{
  display: flex;
  justify-content: center;
  align-items: center;
  flex-direction: column;
  row-gap: 10px;
}
.buttons{
  display: flex;
  column-gap: 20px;
}
.post-table {
  width: 100%;
  border-collapse: collapse;
}

.post-table th,
.post-table td {
  padding: 10px;
  text-align: left;
}

.post-table th {
  background-color: #f2f2f2;
}

.action-link {
  margin-right: 10px;
}

.delete-button {
  padding: 5px 10px;
  background-color: #dc3545;
  color: #fff;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

.delete-button:hover {
  background-color: #c82333;
}

.error-message {
  color: red;
  margin-top: 10px;
}
</style>