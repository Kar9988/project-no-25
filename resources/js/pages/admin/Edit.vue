<script setup>
import {useAdminStore} from "../../store/adminStore.js";
import {useUserStore} from "../../store/userStore.js";
import {computed, onMounted, ref} from "vue";
import {useRoute} from 'vue-router';
import {useCategoryStore} from "../../store/categoryStore.js";


const adminStore = useAdminStore()
const userStore = useUserStore();
const user = computed(() => userStore?.user);
const route = useRoute();
const userId = ref(null);
const form = ref({
    id: '',
    name: '',
    email: '',
})

onMounted(() => {
    userId.value = route.params.id;
    getUser(userId.value)
})


const getUser = async (userId) => {
    try {
        user.value = await useUserStore().getUser(userId);
        form.value.id = user.value?.id
        form.value.name = user.value?.name
        form.value.email = user.value?.email
    } catch (error) {
        console.error("Error deleting user:", error.message);
    }
};
const UpdateUserData = async (userId, form) => {
    console.log(form, 'form')
    try {
        console.log(userId)

        await useCategoryStore().updateForm(userId, form);
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
