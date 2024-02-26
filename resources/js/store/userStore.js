import {defineStore} from "pinia";
import axios from "axios";
import router from "../router/index.js";
// import router from "@/js/router";
import Swal from 'sweetalert2';


export const useUserStore = defineStore('userStore', {
    state: () => ({
        users: [],
        user: {},
    }),
    actions: {
        getUsers() {
            return new Promise((resolve, reject) => {
                axios.get('http://127.0.0.1:8000/api/admin/users')
                    .then(({data}) => {
                        this.users = data
                        resolve(data)
                    }).catch(e => {
                    reject(e)
                })
            })
        },
        deleteUser(userId) {
            console.log(userId)
            return new Promise((resolve, reject) => {
                axios.get(`http://127.0.0.1:8000/api/admin/delete/${userId}`,
                    {headers: {Authorization: 'Bearer ' + localStorage.getItem('token')}})
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
                console.log(userId)
                axios.get(`http://127.0.0.1:8000/api/admin/edit/${userId}`,
                    {headers: {Authorization: 'Bearer ' + localStorage.getItem('token')}})
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
                console.log(id)
                await axios.put(`http://127.0.0.1:8000/api/admin/update/${id}`, {
                        name: form.name,
                        email: form.email,
                    },
                    {
                        headers: {Authorization: 'Bearer ' + localStorage.getItem('token')}
                    })
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
