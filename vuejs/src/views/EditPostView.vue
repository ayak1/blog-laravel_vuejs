<template>
  <div>
    <h2>Edit Post</h2>
    <template v-if="editedPost">
      <form @submit.prevent="submitForm">
        <div>
          <label for="title">Title:</label>
          <input type="text" id="title" v-model="editedPost.title" />
        </div>
        <div>
          <label for="body">Body:</label>
          <input type="text" id="body" v-model="editedPost.body" />
        </div>

        <button type="submit">Save Changes</button>
      </form>
    </template>
  </div>
</template>

<script>
export default {
  props: ['id'],
  data() {
    return {
      editedPost: null
    };
  },
  created() {
    console.log(this.id)
    // Fetch the post data from the API
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
        this.editedPost = responseData.post;
        console.log(this.editedPost);
      } catch (error) {
        console.error(error);
      }
    },
    async submitForm() {
      try {
        const token = localStorage.getItem('token');
        const response = await fetch(`http://127.0.0.1:8000/api/post/${this.id}`, {
          method: 'PUT',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(this.editedPost)
        });
        if (!response.ok) {
          throw new Error(`${response.status}: ${response.statusText}`);
        }
        this.$router.push('/my-blog')
        } catch (error) {
        console.error(error);
      }
    }
  }
};
</script>
