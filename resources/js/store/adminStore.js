import {defineStore} from "pinia";
import axios from "../axios-configured.js";
import router from "../router/index.js";

export const useAdminStore = defineStore('adminStore', {
    state: () => ({
        admin: {},
    }),
    actions: {
        adminLogin(data) {
            return new Promise((resolve, reject) => {
                axios.post(`/api/login`, data)
                    .then(response => {
                        // if (response.status === 200) {
                        //     this.admin = response.data.user
                        //     localStorage.setItem('token', response.data.token)
                        //     router.push({path: 'dashboard'})
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
            return axios.get(`/api/auth/user`, {
            }).then((res) => {
                this.admin = res.data.data
            })
        },
        logout() {
                axios.get(`/api/logout`,{
            }).then(response => {
                localStorage.removeItem('token');
                router.push({path: '/auth/login'})
            })
        }
    }
})
