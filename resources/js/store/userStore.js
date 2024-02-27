import {defineStore} from "pinia";
import axios from "../axios-configured.js";
import Swal from 'sweetalert2';


export const useUserStore = defineStore('userStore', {
    state: () => ({
        users: [],
        user: {},
    }),
    actions: {
        getUsers() {
            return new Promise((resolve, reject) => {
                axios.get('/api/admin/users',)
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
                axios.delete(`/api/admin/users/${userId}`,)
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
                axios.get(`/api/admin/users/${userId}`,)
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

            try {
                await axios.put(`/api/admin/users/${id}`, {
                        name: form.name,
                        email: form.email,
                    },)
                    .then(() => {
                        Swal.fire({
                            position: "top",
                            icon: "success",
                            title: "Post updated successfully",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    })
            } catch (error) {
                console.error('Error posting data:', error);
            }
        },
    }
})
