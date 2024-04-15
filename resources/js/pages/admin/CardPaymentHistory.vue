<script setup>
import {defineProps, onMounted, ref} from "vue";

const payments = ref(null)
const props = defineProps({
    paymentHistory: Object,
});

onMounted(() => {
    payments.value = props.paymentHistory;
})
</script>

<template>
    <table class="border-collapse table-fixed w-full text-sm mt-[50px]">
        <thead>
        <tr>
            <th class="border-b dark:border-slate-600 font-medium p-4 pl-8 pt-0 pb-3 text-slate-400 light:text-slate-200 text-left">
                User Name
            </th>
            <th class="border-b dark:border-slate-600 font-medium p-4 pt-0 pb-3 text-slate-400 light:text-slate-200 text-left">
                Plan Name
            </th>
            <th class="border-b dark:border-slate-600 font-medium p-4 pr-8 pt-0 pb-3 text-slate-400 light:text-slate-200 text-left">
                Price
            </th>
            <th class="border-b dark:border-slate-600 font-medium p-4 pr-8 pt-0 pb-3 text-slate-400 light:text-slate-200 text-left">
                Points
            </th>
            <th class="border-b dark:border-slate-600 font-medium p-4 pr-8 pt-0 pb-3 text-slate-400 light:text-slate-200 text-left">
                Payment Method
            </th>
            <th class="border-b dark:border-slate-600 font-medium p-4 pr-8 pt-0 pb-3 text-slate-400 light:text-slate-200 text-left">
                Plan Type
            </th>
        </tr>
        </thead>
        <tbody v-for="payment in props.paymentHistory" class="bg-white light:bg-slate-800">
        <tr>
            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pl-8 text-slate-500 light:text-slate-400">
                <p class=""> {{ payment.userName }}</p>
            </td>
            <td class="border-b border-slate-100 dark:border-slate-700 p-4 text-slate-500 light:text-slate-400">
                <p class=""> {{ payment.planName }} </p>
            </td>
            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400">
                <p class=""> {{ payment.planPrice }} </p>

            </td>
            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400">
                <p class=""> {{ payment.planPoints }}</p>
            </td>
            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400">
                <p class=""> {{ payment.type }}</p>
            </td>
            <td class="border-b border-slate-100 dark:border-slate-700 p-4 pr-8 text-slate-500 light:text-slate-400">
                <p class=""> {{ payment.plan_type }}</p>
            </td>
        </tr>
        </tbody>
    </table>
</template>

<style scoped>

</style>
