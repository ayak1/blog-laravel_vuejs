
<template>
  <div class="blog">
    <div class="header">
      <h2>Posts</h2>
      <router-link class="btn addBtn" to="/add-post">Add Post</router-link>
      <router-link to="/my-blog" class="btn myBlog">My Blogs</router-link>
    
    </div>
    <div v-for="post in posts" :key="post.id" class="post">
      <div class="post-header">
        <div class="avatar"></div>
        <div class="post-info">
          <h2 class="post-title">{{ post.title }}</h2>
          <p class="post-body">{{ post.body }}</p>
        </div>
      </div>
      <div class="comment-section">
        <h3 class="comment-heading">Comments</h3>
        <ul class="comment-list">
          <li v-for="comment in post.comments" :key="comment.id" class="comment-item">
            <div class="comment-header">
              <div class="avatar"></div>
              <div class="comment-info">
                <p class="comment-text">{{ comment.comment }}</p>
                <div class="reply-container">
                  <input v-model="replyText[comment.id]" type="text" :placeholder="`Add a reply`" class="reply-input">
                  <button class="reply-button" @click="createReply(comment.id, replyText[comment.id], post.id)">Add Reply</button>
                </div>
              </div>
            </div>
            <ul class="reply-list">
              <li v-for="reply in comment.replies" :key="reply.id" class="reply-item">
                <div class="reply-header">
                  <div class="avatar"></div>
                  <div class="reply-info">
                    <p class="reply-text">{{ reply.reply }}</p>
                    <div class="reply-container">
                      <input v-model="replyText[reply.id]" type="text" :placeholder="`Add a reply`" class="reply-input">
                      <button class="reply-button" @click="createReply(comment.id, replyText[reply.id], post.id, reply.id)">Add Reply</button>
                    </div>
                  </div>
                </div>
                <ul class="reply-list replyToReply">
                  <li v-for="nestedReply in reply.nested_replies" :key="nestedReply.id" class="reply-item">
                    <div class="reply-header">
                      <div class="avatar"></div>
                      <div class="reply-info">
                        <p class="reply-text">{{ nestedReply.reply }}</p>
                      </div>
                    </div>
                  </li>
                </ul>
              </li>
            </ul>
          </li>
        </ul>
        <form @submit.prevent="addComment(post.id)" class="comment-form">
          <input type="text" v-model="commentText[post.id]" :placeholder="`Add a comment`" class="comment-input">
          <button type="submit" class="comment-button">Add Comment</button>
        </form>
      </div>
    </div>
    <p v-if="posts.length === 0" class="noPosts">There are no posts yet</p>
    <p v-if="errorMessage" class="error-message">{{ errorMessage }}</p>
  </div>
</template>

<script>
import { mapState } from 'vuex';

export default {
  computed: {
    ...mapState(['token'])
  },
  data() {
    return {
      posts: [],
      errorMessage: '',
      commentText: {},
      replyText: {}
    };
  },

  created() {
    this.fetchPosts();
  },
  methods: {
    processPosts(posts) {
    // Create a map to store the processed posts
    const processedPosts = {};

    // Iterate over the posts
    for (const post of posts) {
      const processedComments = [];

      for (const comment of post.comments) {
        const processedReplies = {};

        for (const reply of comment.replies) {
          if (reply.parent_id) {
            const parentReply = processedReplies[reply.parent_id];
            if (parentReply) {
              parentReply.nested_replies.push(reply);
            }
          } else {
            processedReplies[reply.id] = reply;
            reply.nested_replies = [];
          }
        }
        comment.replies = Object.values(processedReplies);

        processedComments.push(comment);
      }

      post.comments = processedComments;

      processedPosts[post.id] = post;
    }
    return Object.values(processedPosts);
  },
    async fetchPosts() {
      try {
        const token = localStorage.getItem('token');
        const response = await fetch('http://127.0.0.1:8000/api/posts', {
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

        const processedData = this.processPosts(responseData.posts);
    
    this.posts = processedData;
        console.log('posts',this.posts);

        this.errorMessage = '';
      } catch (error) {
        this.errorMessage = error.message;
      }
    },
    async addComment(id) {
      const comment = {
        comment: this.commentText[id],
      };
      try {
        const token = localStorage.getItem('token');
        const response = await fetch(`http://127.0.0.1:8000/api/comment/${id}`, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(comment),
        });
        if (!response.ok) {
          throw new Error(`${response.status}: ${response.statusText}`);
        }
        const responseData = await response.json();
        console.log(responseData);
        this.commentText = '';
        this.errorMessage = '';
        this.fetchPosts();
      } catch (error) {
        this.errorMessage = error.message;
      }
    },
    async createReply(commentId, replyText, postId, parentId = null) {
      const reply = {
        comment_id: commentId,
        reply: replyText,
        post_id: postId,
        parent_id: parentId,
      };
      try {
        const token = localStorage.getItem('token');
        const response = await fetch('http://127.0.0.1:8000/api/replies', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'Authorization': `Bearer ${token}`
          },
          body: JSON.stringify(reply),
        });

        if (!response.ok) {
          throw new Error(`${response.status}: ${response.statusText}`);
        }

        const responseData = await response.json();
        console.log(responseData);

        // Clear reply text input
        this.replyText[commentId] = '';
        this.replyText[parentId] = '';

        // Fetch posts to update the comments with the newly added reply
        this.fetchPosts();
      } catch (error) {
        this.errorMessage = error.message;
      }
    },
  }
};
</script>

<style>
.blog .header{
  display: flex;
  justify-content: center;
  align-items: center;
  column-gap:15px;
}
  .noPosts {
    margin-top: 30px;
  }

  .addBtn {
    text-decoration: none;
    border-radius: 10px;
    padding: 20px;
    background-color: #0056b3;
    color: #fff;
  }
.myBlog{
  text-decoration: none;
  background:green;
  border-radius: 10px;
  padding: 20px;
}
  .blog {
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
  }

  .post {
    width: 500px;
    margin-bottom: 20px;
    padding: 10px;
    border: 1px solid #ccc;
    background-color: #f5f5f5;
    border-radius: 10px;
    margin: 30px 0;
  }

  .post-header {
    display: flex;
    align-items: center;
    text-align:left;
    margin-bottom: 10px;
    column-gap:10px
  }

  .avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background-color: #ccc;
  }

  .post-info {
    margin-left: 10px;
  }

  .post-title {
    font-size: 20px;
    margin: 0;
  }

  .post-body {
    margin: 0;
  }

  .comment-section {
    border-top: 1px solid #ccc;
    padding-top: 10px;
    display:flex;
    justify-content:flex-start;
    align-items:flex-start;
    flex-direction:column;
  }

  .comment-heading {
    font-size: 18px;
    margin-bottom: 10px;
  }

  .comment-list {
    list-style-type: none;
    margin: 0;
    padding: 0;
  }

  .comment-item {
    margin-bottom: 15px;
  }

  .comment-header {
    display: flex;
    align-items: flex-start;
    margin-bottom: 5px;
  }

  .comment-info {
    margin-left: 10px;
  }

  .comment-text {
    text-align: left;
    width: fit-content;
    margin: 0;
    color: #0056b3;
    font-weight: 600;
    font-size: 1.3rem;
    margin-bottom: 10px;
  }

  .reply-container {
    display: flex;
  }

  .reply-input {
    flex: 1;
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px 0 0 4px;
  }

  .reply-button {
    padding: 5px 10px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 0 4px 4px 0;
    cursor: pointer;
  }

  .reply-button:hover {
    background-color: #0056b3;
  }

  .reply-list {
    list-style-type: none;
    margin-left: 40px;
    padding: 0;
  }

  .reply-item {
    margin-bottom: 5px;
  }

  .reply-header {
    display: flex;
    align-items: center;
    margin-bottom: 5px;
  }

  .reply-info {
    margin-left: 10px;
  }

  .reply-text {
    text-align: left;
    width:fit-content;
    color:#007bff;
    margin: 10px 0;
    font-size:1rem;
    font-weight: 600;
  }
  .replyToReply .reply-text{
    color:red;
    font-size:1rem;
    font-weight: 600;
  }
  .replyToReply .reply-header {
    margin-left: 80px; /* Increase the indentation for nested replies */
  }

  .comment-form {
    display: flex;
    margin-top: 10px;
  }

  .comment-input {
    flex: 1;
    padding: 5px;
    border: 1px solid #ccc;
    border-radius: 4px;
  }

  .comment-button {
    margin-left: 10px;
    padding: 5px 10px;
    background-color: #007bff;
    color: #fff;
    border: none;
    border-radius: 4px;
    cursor: pointer;
  }

  .comment-button:hover {
    background-color: #0056b3;
  }

  .error-message {
    color: red;
    margin-top: 10px;
  }
</style>