<script>
import axios from "../axios-configured.js";

export default {
    data() {
        return {
            createData: {
                name: 'asdasd'
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

                this.plans.push(data.plan)
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

                this.plans.push(data.plan)
            } catch (e) {
                console.log(e)
            }
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
                        <option value="One Time">One Time</option>
                        <option value="Weekly">Weekly</option>
                        <option  value="Yearly">Yearly</option>
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
            </tr>
            </tbody>
        </table>
    </div>
</template>

<style scoped>

</style>
