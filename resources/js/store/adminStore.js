import {defineStore} from "pinia";
import axios from "axios";
import router from "../router/index.js";
import admin from "../layouts/Admin.vue";
// import router from "@/js/router";
export const useAdminStore = defineStore('adminStore', {
    state: () => ({
        admin: {},
    }),
    actions: {
        adminLogin(data) {
            return new Promise((resolve, reject) => {
                axios.post(`/api/login`,data)
                    .then(response => {
                        if (response.status === 200){
                            console.log(response.data.user)
                            this.admin = response.data.user
                            localStorage.setItem('token', response.data.token)
                            router.push({path:'/admin/dashboard'})
                        }
                    }).catch((e) => {
                    reject(e);
                });
            })

        },
        getAuthUser() {
            const token = localStorage.getItem('token');
            const config = {
                headers: {
                    Authorization: `Bearer ${token}`
                }
            };
            return axios.get(`/api/auth/user`, {
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('token')
                }
            }).then((res) => {
                this.admin = res.data.data
            })
        },
        logout() {
                axios.get(`/api/auth/user`,{
                headers: {
                    Authorization: 'Bearer ' + localStorage.getItem('token')
                }
            }).then(response => {
                localStorage.removeItem('token');
                router.push({path: '/auth/login'})
            })
        }
    }
})
