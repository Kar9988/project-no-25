import {defineStore} from "pinia";
import axios from "axios";
import router from "../router/index.js";
// import router from "@/js/router";

export const useAdminStore = defineStore('adminStore', {
    state: () => ({
        admin: {},
    }),
    actions: {
        adminLogin(data) {
            return new Promise((resolve, reject) => {
                axios.post(`http://127.0.0.1:8001/api/login`,data)
                    .then(response => {
                        if (response.status === 200){
                            this.admin = response.data.user
                            localStorage.setItem('token', response.data.token)
                            router.push({path:'dashboard'})
                        }
                    }).catch((e) => {
                    reject(e);
                });
            })

        }
    }
})
