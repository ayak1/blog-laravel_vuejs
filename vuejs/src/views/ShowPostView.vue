<template>
  <div class="showPost">
   <div class="div">
     <h2>Show Post</h2>
        <router-link to="/my-blog" class="btn">My Blogs</router-link>
   </div>
    <template v-if="post">
 <div class="post-details">
        <h3>Title:</h3>
        <p>{{ post.title }}</p>
        <h3>Body:</h3>
        <p>{{ post.body }}</p>
      </div>
        
    </template>
  </div>
</template>

<script>
export default {
  props: ['id'],
  data() {
    return {
      post: null
    };
  },
  created() {
    this.fetchPost();
  },
  methods: {
    async fetchPost() {
      try {
        const token = localStorage.getItem('token');
        const response = await fetch(`http://127.0.0.1:8000/api/post/${this.id}`, {
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
        this.post = responseData.post;
      } catch (error) {
        console.error(error);
      }
    }
  }
};
</script>
<style>
.showPost{
    display: flex;
    /* justify-content: center; */
    align-items: center;
    flex-direction: column;
    row-gap: 20px;
}
.showPost .div{
    display: flex;
    column-gap: 15px;
}
.post-details {
  border: 1px solid #ccc;
  padding: 20px;
  width: fit-content;
  text-align:left;
}

h2 {
  font-size: 24px;
  color: #333;
}

h3 {
  font-size: 18px;
  color: #555;
  margin-top: 10px;
}

p {
  font-size: 16px;
  color: #777;
}
</style>