import {defineStore} from "pinia";
import axios from "../axios-configured.js";
import Swal from 'sweetalert2';


export const useUserStore = defineStore('userStore', {
    state: () => ({
        users: [],
        user: {},
        errors:[]
    }),
    actions: {
        getUsers() {
            return new Promise((resolve, reject) => {
                axios.get('/admin/users',)
                    .then(({data}) => {
                        this.users = data
                        resolve(data)
                    }).catch(e => {
                    reject(e)
                })
            })
        },
        deleteUser(userId) {
            return new Promise((resolve, reject) => {
                axios.delete(`/admin/users/${userId}`,)
                    .then(response => {
                        console.log(response)
                        if (Array.isArray(this.users)) {
                            this.users = this.users.filter(user => user.id !== userId)
                        } else {
                            this.getUsers().then(() => {
                                resolve(response.data);
                            }).catch((e) => {
                                reject(e);
                            });
                        }
                    }).catch((e) => {
                    reject(e);
                });
            })
        },
        getUser(userId) {
            return new Promise((resolve, reject) => {
                axios.get(`/admin/users/${userId}`,)
                    .then(response => {
                        this.user = response.data.user;
                        resolve(response.data);
                        return response.data.use
                    }).catch((e) => {
                    reject(e);
                });
            })
        },
        async updateForm(id, form) {
            this.errors = [];
            try {
                await axios.put(`/admin/users/${id}`, {
                        name: form.name,
                        email: form.email,
                        amount:form.amount,
                        bonus:form.bonus,
                        balanceId:form.balanceId
                    },)
                    .then(({data}) => {
                        console.log(data,'tessssssssssss')
                        Swal.fire({
                            position: "top",
                            icon: data.type,
                            title: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    })
            } catch (e) {
                this.errors = e.response.data.details
            }
        },
    }
})
