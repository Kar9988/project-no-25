import {defineStore} from "pinia";
import axios from "../axios-configured.js";
import router from "../router/index.js";
import Swal from "sweetalert2";

export const useAdminStore = defineStore('adminStore', {
    state: () => ({
        admin: {},
        errors:{},
    }),
    actions: {
        adminLogin(data) {
            return new Promise((resolve, reject) => {
                axios.post(`/login`, data)
                    .then(response => {
                        if (response.status === 200){
                            this.admin = response.data.user
                            localStorage.setItem('token', response.data.token)
                            axios.defaults.headers.common['Authorization'] = `Bearer ${response.data.token}`
                            router.push({path:'/admin/dashboard'})
                        }
                    }).catch((e) => {
                    reject(e);
                });
            })
        },
        getAuthUser() {
            return axios.get(`/auth/user`, {
            }).then((res) => {
                this.admin = res.data.data
            })
        },
        logout() {
                axios.get(`/logout`,{
            }).then(response => {
                    Swal.fire({
                        title: "do you want to log out ?",
                        icon: "info",
                        showCloseButton: true,
                        showCancelButton: true,
                        focusConfirm: false,
                        confirmButtonText: `<i class="fa fa-thumbs-up"></i> Yes!`,
                        confirmButtonAriaLabel: "Thumbs up, great!",
                        cancelButtonText: `<i class="fa fa-thumbs-down"> No </i>`,
                        cancelButtonAriaLabel: "Thumbs down"
                    }).then((res) => {
                        if (res.isConfirmed === true) {
                            localStorage.removeItem('token');
                            router.push({path: '/auth/login'})
                        }else{
                            router.push({path: '/admin/dashboard'})
                        }
                    });

            })
        },
        createUser(data) {
            return axios.post('/admin/users', data).then(() => {
            }).catch((e) => {
                this.errors = e.response.data.errors
            })
        }
    }
})
