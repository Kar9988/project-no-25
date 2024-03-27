<script setup>


import {useUserStore} from "../../store/userStore.js";
import {computed, onMounted} from "vue";
import {useAdminStore} from "../../store/adminStore.js";

const adminStore = useAdminStore()
const userStore = useUserStore();
const users = computed(() => userStore?.users);
import {ref} from "vue";
import ModalComponent from "../../components/Modal/Modal.vue";
import router from "../../router/index.js";

const isModalOpened = ref(false);
const user = ref({
    name: '',
    email: '',
    password: '',
    role: 3,
})
const openModal = () => {
    isModalOpened.value = true;
};
const closeModal = () => {
    isModalOpened.value = false;
};

const submitHandler = () => {
    const form = {
        name:user.value.name,
        email: user.value.email,
        password: user.value.password,
        role_id: user.value.role
    }
    adminStore.createUser(form).then(() => {
        isModalOpened.value = false
        userStore.getUsers()
    }).catch(error => {
        console.log(error, 'error')
    })
}
onMounted(() => {
    userStore.getUsers()
})
const deleteUser = async (userId) => {
    const confirmDelete = window.confirm("Are you sure you want to delete this user?");
    if (!confirmDelete) {
        return;
    }
    try {
        await useUserStore().deleteUser(userId);

    } catch (error) {
        console.error("Error deleting user:", error.message);
    }
};


</script>

<template>
    <div
        class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded">
        <div class="rounded-t mb-0 px-4 py-3 border-0">
            <div class="flex flex-wrap items-center">
                <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                    <h3
                        class="font-semibold text-lg"
                        :class="[color === 'light' ? 'text-blueGray-700' : 'text-white']"
                    >Users list
                    </h3>
                </div>
            </div>
        </div>
        <div class="block w-full overflow-x-auto px-4">
            <div>
                <button @click="openModal">
                    <div class="flex gap-2">Add User
                        <span>
                            <svg class="w-6 h-6 text-gray-800 dark:text-white" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd" d="M9 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4H7Zm8-1c0-.6.4-1 1-1h1v-1a1 1 0 1 1 2 0v1h1a1 1 0 1 1 0 2h-1v1a1 1 0 1 1-2 0v-1h-1a1 1 0 0 1-1-1Z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                    </div>
                </button>
            </div>
            <ModalComponent class="text-black" :size-class="'w-1/2'" :isOpen="isModalOpened" @modal-close="closeModal"
                            name="first-modal">
                <template #header>
                    <h1 class="p-[20px] text-center">Create User</h1>
                </template>
                <template #content class="">
                    <div class="mt-[15px] p-[20px] gap-[10px]">
                        <form class="p-[24px]">
                            <div class="relative w-full mb-3">
                                <label
                                    class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                    htmlFor="grid-password"
                                >
                                    Name
                                </label>
                                <input v-model="user.name"
                                    type="email"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                    placeholder="Name"
                                />
                            </div>

                            <div class="relative w-full mb-3">
                                <label
                                    class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                    htmlFor="grid-password"
                                >
                                    Email
                                </label>
                                <input
                                    v-model="user.email"
                                    type="email"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                    placeholder="Email"
                                />
                            </div>

                            <div class="relative w-full mb-3">
                                <label
                                    class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                    htmlFor="grid-password"
                                >
                                    Password
                                </label>
                                <input
                                    v-model="user.password"
                                    type="password"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                    placeholder="Password"
                                />
                            </div>
                            <div>
                                <div class="text-black">Selected: {{ user.role}}</div>
                                <select v-model="user.role">
                                    <option disabled value="">Please select one</option>
                                    <option value="1">Admin</option>
                                    <option value="2">Moderator</option>
                                    <option value="3">User</option>
                                </select>
                            </div>
                            <div class="text-center mt-6">
                                <button @click="submitHandler"
                                    class="w-[200px] text-black bg-blueGray-800 active:bg-blueGray-600 text-sm font-bold uppercase px-6 py-3 rounded shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1 w-full ease-linear transition-all duration-150"
                                    type="button"
                                >
                                    Create Account
                                </button>
                            </div>
                        </form>
                    </div>
                </template>
                <template #footer> <h3 class="text-center"> create user  </h3></template>
            </ModalComponent>
            <div class="mb-[24px]">

            </div>
            <table class="items-center  w-full bg-transparent border-collapse">
                <thead>
                <tr>
                    <th
                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left"
                    >Name
                    </th>
                    <th
                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left"
                    >Email

                    </th>
                    <th
                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left"
                    > Balance

                    </th>
                    <th
                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left"
                    > Bonus
                    </th>
                    <th
                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left"
                    > settings

                    </th>
                </tr>
                </thead>
                <tbody v-for="user in users.users" :key="user.id">

                <tr>
                    <td
                        class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4"
                    >
                        {{ user.name }}
                    </td>

                    <th
                        class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left flex items-center"
                    >
                        <span
                            class="ml-3 font-bold"
                        >{{ user.email }}</span>
                    </th >
                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs items-center">
                        {{ user.balance }}
                    </td>
                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs items-center">
                        {{ user.bonus }}
                    </td>
                    <td>
                        <th class="flex p-[10px]">
                            <div class="pr-[8px]">
                                <button @click="deleteUser(user.id)">
                                    <svg class="w-6 h-6 text-gray-800"
                                         aria-hidden="true"
                                         xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                              d="M8.6 2.6A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4c0-.5.2-1 .6-1.4ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="pl-[8px]">
                                <router-link :to="{name: 'admin.edit', params: {id: user.id}}">
                                    <!--                        <button @click="updateUser(user.id)">-->
                                    <svg class="w-6 h-6 text-gray-800" aria-hidden="true"
                                         xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                              d="M11.3 6.2H5a2 2 0 0 0-2 2V19a2 2 0 0 0 2 2h11c1.1 0 2-1 2-2.1V11l-4 4.2c-.3.3-.7.6-1.2.7l-2.7.6c-1.7.3-3.3-1.3-3-3.1l.6-2.9c.1-.5.4-1 .7-1.3l3-3.1Z"
                                              clip-rule="evenodd"/>
                                        <path fill-rule="evenodd"
                                              d="M19.8 4.3a2.1 2.1 0 0 0-1-1.1 2 2 0 0 0-2.2.4l-.6.6 2.9 3 .5-.6a2.1 2.1 0 0 0 .6-1.5c0-.2 0-.5-.2-.8Zm-2.4 4.4-2.8-3-4.8 5-.1.3-.7 3c0 .3.3.7.6.6l2.7-.6.3-.1 4.7-5Z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </router-link>
                            </div>
                        </th>
                    </td>
                </tr>

                </tbody>
            </table>
        </div>
    </div>

</template>

<style scoped>

</style>
