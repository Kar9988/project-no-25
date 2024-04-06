<script setup>
import {useAdminStore} from "../../store/adminStore.js";
import {useUserStore} from "../../store/userStore.js";
import {computed, onMounted, ref} from "vue";
import {useRoute} from 'vue-router';
import {useCategoryStore} from "../../store/categoryStore.js";
import axios from "../../axios-configured.js";


const adminStore = useAdminStore()
const userStore = useUserStore();
const user = computed(() => userStore?.user);
const route = useRoute();
const userId = ref(null);
const plans = ref(null)
const form = ref({
    id: '',
    name: '',
    email: '',
    amount: '',
    bonus: '',
    balanceId:'',
    planName:'',
    subId:'',
})

const  addPlan = () => {
 axios.get('/admin/plans').then((res)=>{
     plans.value = res.data.data
 })
};
onMounted(() => {
    addPlan();
    userId.value = route.params.id;
    getUser(userId.value)

})

const getUser = async (userId) => {
    try {
        user.value = await useUserStore().getUser(userId);
        form.value.id = user.value?.id
        form.value.name = user.value?.name
        form.value.email = user.value?.email
        form.value.amount = user.value?.user_balance.amount
        form.value.bonus = user.value?.user_balance.bonus
        form.value.balanceId = user.value?.user_balance.id
        form.value.planId = user.value?.get_active_subscription.plan.id
        form.value.subId  = user.value?.get_active_subscription.id
    } catch (error) {
        console.error("Error deleting user:", error.message);
    }
};
const UpdateUserData = async (userId, form) => {
    try {
        await useUserStore().updateForm(userId, form);
    } catch (error) {
        console.error("Error deleting user:", error.message);
    }
};


</script>

<template>
    <div class="border-t-[100px]">
        <form action="">
            <div class="heading text-center font-bold text-2xl m-5 text-gray-800">Update</div>
            <div
                class="editor mx-auto w-10/12 flex flex-col text-gray-800 border border-gray-300 p-4 shadow-lg max-w-2xl">
                <input name="name" v-model="form.name"
                       class="title bg-gray-100 border border-gray-300 p-2 mb-4 outline-none"
                       spellcheck="false" placeholder="Name" type="text">
                <input name="email" v-model="form.email"
                       class="title bg-gray-100 border border-gray-300 p-2 mb-4 outline-none"
                       spellcheck="false" placeholder="Email" type="text">
                <input name="bonus" v-model="form.bonus"
                       class="title bg-gray-100 border border-gray-300 p-2 mb-4 outline-none"
                       spellcheck="false" placeholder="Bonus" type="number">
                <input name="amount" v-model="form.amount"
                       class="title bg-gray-100 border border-gray-300 p-2 mb-4 outline-none"
                       spellcheck="false" placeholder="Amount" type="number">
                <select v-model="form.planId"  name="" id="">
                    <option  v-for="plan in plans"  :value="plan.id">name {{ plan.name ?? '' }}  type{{ plan.type ?? '' }}</option>
                </select>
                <!--            <p style="color: red" v-if="err.name">{{ err.name }}</p>-->
                <div style="margin-top: 10px" class="buttons flex">
                    <button @click="UpdateUserData(user.id,form)" type="button"
                            class="btn border border-indigo-500 p-1 px-4 font-semibold cursor-pointer text-gray-200 ml-2 bg-indigo-500">
                        Update
                    </button>
                </div>
            </div>
        </form>
    </div>
</template>

<style scoped>

</style>
