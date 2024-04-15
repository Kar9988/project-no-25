<script setup>
import {onMounted, ref} from "vue";
import axios from "../../axios-configured.js";
import CardPaymentHistory from "./CardPaymentHistory.vue";
const paymentHistory = ref([]);
onMounted(()=>{
    axios.get('/payment-history').then((res)=>{
        paymentHistory.value  = res.data
    })
})
</script>

<template>
    <CardPaymentHistory :payment-history="paymentHistory"/>
</template>

<style scoped>

</style>
