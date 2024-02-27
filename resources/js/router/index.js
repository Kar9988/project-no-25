import { createWebHistory, createRouter } from "vue-router";
import Admin from "../layouts/Admin.vue";
import Auth from "../layouts/Auth.vue";

// views for Admin layout

import Dashboard from "../pages/admin/Dashboard.vue";
import Settings from "../pages/admin/Settings.vue";
import Tables from "../pages/admin/Tables.vue";
import Users from "../pages/admin/Users.vue";
// views for Auth layout

import Login from "../pages/auth/Login.vue";
import Register from "../pages/auth/Register.vue";

// views without layouts

import Landing from "../pages/Landing.vue";
import Profile from "../pages/Profile.vue";
import Index from "../pages/Index.vue";
import Edit from "../pages/admin/Edit.vue";
import {createApp} from "vue";
import App from "../App.vue";
const routes = [
    {
        path: "/admin",
        redirect: "/admin/dashboard",
        component: Admin,
        children: [
            {
                path: "/admin/dashboard",
                component: Dashboard,

            },
            {
                path: "/admin/settings",
                component: Settings,
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
    {
        path: "/",
        component: Index,
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
            next('/admin/dashboard');
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
