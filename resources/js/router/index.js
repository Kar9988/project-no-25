import { createWebHistory, createRouter } from "vue-router";
import Admin from "../layouts/Admin.vue";
import Auth from "../layouts/Auth.vue";

// views for Admin layout

import Dashboard from "../pages/admin/Dashboard.vue";
import Tables from "../pages/admin/Tables.vue";
import Users from "../pages/admin/Users.vue";
import PaymentHistory from "../pages/admin/Payment-history.vue";
// views for Auth layout

import Login from "../pages/auth/Login.vue";
import Register from "../pages/auth/Register.vue";

// views without layouts

import Landing from "../pages/Landing.vue";
import Profile from "../pages/Profile.vue";
import Edit from "../pages/admin/Edit.vue";
import VideosList from "../pages/admin/VideosList.vue";
import VideosShow from "../pages/admin/VideoShow.vue";
import Categories from "../pages/admin/Categories.vue";
import EditCategory from "../pages/admin/EditCategory.vue";
import plans from "../pages/Plans.vue";
const routes = [
    {
        path: "/admin",
        redirect: "/admin/videos",
        component: Admin,
        children: [

            {
                path: "/admin/videos",
                component: VideosList,

            },
            {
                path: "/admin/payment-history",
                component: PaymentHistory,

            },
            {
                path: "/admin/categories",
                component: Categories,

            },

            {
                path: "/admin/videos/:id",
                component: VideosShow,
                name: "video.edit",
            },
            {
                path: "/admin/plans",
                name: "admin.plans",
                component: plans,
            },
            {
                path: "/admin/tables",
                component: Tables,
            },
            {
                path: "/admin/users",
                component: Users,
            },
            {
                path: "/admin/edit/:id",
                name: "admin.edit",
                component: Edit,
            },
            {
                path: "/admin/edit/categories:id",
                name: "categories.edit",
                component: EditCategory,
            },

        ],
    },
    {
        path: "/auth",
        redirect: "/auth/login",
        component: Auth,
        children: [
            {
                path: "/auth/login",
                component: Login,
            },
            {
                path: "/auth/register",
                component: Register,
            },
        ],
    },
    {
        path: "/landing",
        component: Landing,
    },
    {
        path: "/profile",
        component: Profile,
    },
    { path: "/:pathMatch(.*)*", redirect: "/auth/login" },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});
router.beforeEach((to, from, next) => {
    let token = localStorage.getItem('token');
    if (token) {
        if (to.path === '/auth/register' || to.path === '/auth/login') {
            next('/admin/videos');
        } else {
            next();
        }
    } else {
        if (to.path !== '/auth/login' && to.path !== '/auth/register') {
            next('/auth/login');
        } else {
            next();
        }
    }
});
export default router;
