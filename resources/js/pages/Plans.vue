<script>
import axios from "../axios-configured.js";
import Swal from "sweetalert2";

export default {
    data() {
        return {
            createData: {
                name: ''
            },
            errors:[],
            plans: [],
        }
    },
    methods: {
        async addPlan() {
            try {
              const {data} = await axios.get('/admin/plans')
                if(!data.success) {
                    throw new Error(data.message)
                }

                this.plans = data.data
            } catch (e) {
                console.log(e)
            }
        },
        async createPlan() {
            try {
                const {data} = await axios.post('/admin/plans')
                if(!data.success) {
                    throw new Error(data.message)
                }

                this.addPlan()
            } catch (e) {
                console.log(e)
            }
        },
        async updatePlan(plan) {
            this.errors = [];
            try {
                const {data} = await axios.put(`/admin/plans/${plan.id}`, {...plan})
                if(!data.success) {
                    throw new Error(data.message)
                }
                this.addPlan()
            } catch (e) {
                this.errors[plan.id] = e.response.data.details
            }
        },
        async confirmDeletePlan(planId) {
            try {
                const {data} = await axios.delete(`/admin/plans/${planId}`)
                if(!data.success) {
                    throw new Error(data.message)
                }
                Swal.fire({
                    title: "Deleted!",
                    text: "Plan successfully has been deleted.",
                    icon: "success"
                });
                this.addPlan()
            } catch (e) {
                console.log(e)
            }
        },
        async deletePlan(planId) {
            Swal.fire({
                title: "Are you sure?",
                text: "Are you sure you want to delete plan?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: "Yes, delete it!"
            }).then((result) => {
                if (result.isConfirmed) {
                    this.confirmDeletePlan(planId)
                }
            });
        }
    },
    created() {
        this.addPlan()
    }
}
</script>

<template>
    <div>
        <button
            @click="createPlan"
            class="text-black bg-blueGray-800
         active:bg-blueGray-600 text-sm font-bold
         uppercase px-6 py-3 rounded shadow hover:shadow-lg
          outline-none focus:outline-none mr-1 mb-1
          ease-linear transition-all duration-150" type="button"> Add Plan </button>
        <table class="border-collapse table-fixed w-full text-sm mt-[50px]">
            <thead>
            <tr>
                <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 light:text-slate-200 text-left">Name</th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 light:text-slate-200 text-left">Price</th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pr-8 pt-0 pb-3 text-slate-400 light:text-slate-200 text-left">Coins</th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pr-8 pt-0 pb-3 text-slate-400 light:text-slate-200 text-left">Description</th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pr-8 pt-0 pb-3 text-slate-400 light:text-slate-200 text-left">Discount</th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pr-8 pt-0 pb-3 text-slate-400 light:text-slate-200 text-left">Sub Description</th>
                <th class="border-b dark:border-slate-600 font-medium p-4 pr-8 pt-0 pb-3 text-slate-400 light:text-slate-200 text-left">Types</th>
                <th colspan="2" class="border-b dark:border-slate-600 font-medium p-4 pr-8 pt-0 pb-3 text-slate-400 light:text-slate-200 text-left">Actions</th>
            </tr>
            </thead>
            <tbody class="bg-white light:bg-slate-800">
            <tr v-for="plan in plans">
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 light:text-slate-400">
                    <input v-model="plan.name" type="text" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150" placeholder="Name">
                    <p class="text-red-500" v-if="errors[plan.id]?.name"> {{errors[plan.id]?.name[0]}} </p>
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 light:text-slate-400">
                    <input v-model="plan.price" type="number" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150" placeholder="Price">
                    <p class="text-red-500" v-if="errors[plan.id]?.price"> {{errors[plan.id]?.price[0]}} </p>
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400">
                    <input v-model="plan.points" type="number" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150" placeholder="Coins">
                    <p class="text-red-500" v-if="errors[plan.id]?.points"> {{errors[plan.id]?.points[0]}} </p>

                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400">
                    <input v-model="plan.description" type="text" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150" placeholder="Description">
                    <p class="text-red-500" v-if="errors[plan.id]?.description"> {{errors[plan.id]?.description[0]}} </p>
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400">
                    <input v-model="plan.discount" type="number" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150" placeholder="Discount">
                    <p class="text-red-500" v-if="errors[plan.id]?.discount"> {{errors[plan.id]?.discount[0]}}</p>

                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400">
                    <input v-model="plan.sub_description" type="text" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150" placeholder="Sub Description">
                    <p class="text-red-500" v-if="errors[plan.id]?.sub_description"> {{errors[plan.id]?.sub_description[0]}}</p>

                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400">
                    <select class=" rounded border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400"  v-model="plan.type">
                        <option value="one_time" selected>One Time</option>
                        <option value="weekly">Weekly</option>
                        <option  value="yearly">Yearly</option>
                    </select>
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400">
                    <button
                        @click="updatePlan(plan)"
                            class="text-black bg-blueGray-800
                            active:bg-blueGray-600 text-sm font-bold
                            uppercase px-6 py-3 rounded shadow hover:shadow-lg
                            outline-none focus:outline-none mr-1 mb-1
                            ease-linear transition-all duration-150" type="button">Update</button>
                </td>
                <td v-if="plan.id" class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400">
                    <button
                        @click="deletePlan(plan.id)"
                        class="text-white bg-red-700
                            active:bg-blueGray-600 text-sm font-bold
                            uppercase px-6 py-3 rounded shadow hover:shadow-lg
                            outline-none focus:outline-none mr-1 mb-1
                            ease-linear transition-all duration-150" type="button">Delete</button>
                </td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>

</style>
