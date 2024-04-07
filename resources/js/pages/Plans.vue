<script>
import axios from "../axios-configured.js";
import Swal from "sweetalert2";

export default {
    data() {
        return {
            createData: {
                name: ''
            },
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
            try {
                const {data} = await axios.put(`/admin/plans/${plan.id}`, {...plan})
                if(!data.success) {
                    throw new Error(data.message)
                }

                this.addPlan()
            } catch (e) {
                console.log(e)
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
        <h1>Plans</h1>
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
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 light:text-slate-400">
                    <input v-model="plan.price" type="text" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150" placeholder="Price">
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400">
                    <input v-model="plan.points" type="text" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150" placeholder="Coins">
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400">
                    <input v-model="plan.description" type="text" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150" placeholder="Description">
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400">
                    <input v-model="plan.discount" type="text" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150" placeholder="Discount">
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400">
                    <input v-model="plan.sub_description" type="text" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150" placeholder="Sub Description">
                </td>
                <td class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400">
                    <select class=" rounded border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400"  v-model="plan.type">
                        <option value="one_time">One Time</option>
                        <option value="weekly">Weekly</option>
                        <option  value="yearly">Yearly</option>
                    </select>
<!--                    <input v-model="plan.type" type="text" class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150" placeholder="type">-->
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
