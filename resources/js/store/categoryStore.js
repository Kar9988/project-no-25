import {defineStore} from "pinia";
import axios from "../axios-configured.js";
import Swal from "sweetalert2";

export const useCategoryStore = defineStore('categoryStore', {
    state: () => ({
        categories: [],
        category: {},
        page: 1,
        total: 0,
        nextPage: null,
        prevPage: null,
        totalPages: 0
    }),
    actions: {
        getCategories() {
            return new Promise((resolve, reject) => {
                axios.get(`/admin/categories`)
                    .then(({data}) => {
                        this.categories = data
                        resolve(data)
                    }).catch(e => {
                    reject(e)
                })
            })
        },
        deleteCategory(categoryId) {
            return new Promise((resolve, reject) => {
                axios.delete(`/admin/categories/${categoryId}`,)
                    .then(response => {
                        if (Array.isArray(this.categories)) {
                            this.categories = this.categories.filter(category => category.id !== categoryId)
                        } else {
                            this.getCategories().then(() => {
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
        getCategory(categoryId) {
            return new Promise((resolve, reject) => {
                axios.get(`/admin/categories/${categoryId}`,)
                    .then(response => {
                        this.category = response.data.categories;
                        resolve(response.data);
                        return response.data.use
                    }).catch((e) => {
                    reject(e);
                });
            })
        },
        createCategory(payload) {
            return new Promise((resolve, reject) => {
                axios.post(`/admin/categories`, payload)
                    .then(response => {
                        Swal.fire({
                            position: "top",
                            icon: "success",
                            title: "Category created successfully",
                            showConfirmButton: false,
                            timer: 1500
                        });
                        resolve(response.data);
                    }).catch((e) => {
                    reject(e.response.data);
                });
            })
        },
        updateForm(id, payload) {
            return new Promise((resolve, reject) => {
                axios.put(`/admin/categories/${id}`, payload)
                    .then((response) => {
                        Swal.fire({
                            position: "top",
                            icon: "success",
                            title: "Category updated successfully",
                            showConfirmButton: false,
                            timer: 1500
                        });
                        resolve(response.data);
                    }).catch((e) => {
                    reject(e.response.data);
                });
            })
        },
    }
})
