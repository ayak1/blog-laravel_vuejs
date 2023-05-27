import Vue from 'vue';
import VueRouter from 'vue-router';
import RegisterView from '../views/RegisterView.vue';
import LoginView from '../views/LoginView.vue';
import AddPostView from '../views/AddPostView.vue';
import MyBlogsView from '../views/MyBlogsView.vue';
import BlogView from '../views/BlogView.vue';
import ShowPostView from '../views/ShowPostView.vue';
import EditPostView from '../views/EditPostView.vue';

Vue.use(VueRouter);

const routes = [
    {
        path:'/register',
        name:'register',
        component:RegisterView
    },
    {
        path:'/login',
        name:'login',
        component:LoginView
    },
    {
        path:'/my-blog',
        name:'my-blog',
        component: MyBlogsView,
        meta: {
            requiresAuth: true, 
        },
    },
    {
        path:'/add-post',
        name:'add-post',
        component: AddPostView,
        meta: {
            requiresAuth: true, 
        },
    },
    {
        path: '/edit-post/:id',
        name: 'edit-post',
        component: EditPostView,
        props: true ,
        meta: {
            requiresAuth: true, 
        }, 
      },
     {
        path:'/show-post/:id',
        name:'show-post',
        component: ShowPostView,
        props:true,
        meta: {
            requiresAuth: true, 
        },
    },
    {
        path:'/',
        name:'blog',
        component: BlogView,
        meta: {
            requiresAuth: true, 
        },
    },
    
];

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes
  });
  const isAuthenticated = () => {
    const token = localStorage.getItem('token');
    return !!token; 
  };
  router.beforeEach((to, from, next) => {
    if (to.meta.requiresAuth && !isAuthenticated()) {
        next({ name: 'login' }); 
      } else {
        next(); 
      }
  });
  export default router;
  