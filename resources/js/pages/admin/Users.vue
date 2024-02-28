<script setup>

import TableDropdown from "../../components/Dropdowns/TableDropdown.vue";

import {useUserStore} from "../../store/userStore.js";
import {computed, onMounted} from "vue";
import {useAdminStore} from "../../store/adminStore.js";

const adminStore = useAdminStore()
const userStore = useUserStore();
const users = computed(() => userStore?.users);

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
        class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded"
        :class="[color === 'light' ? 'bg-white' : 'bg-emerald-900 text-white']">
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
            <table class="items-center w-full bg-transparent border-collapse">
                <thead>
                <tr>
                    <th
                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left"
                        :class="[
                color === 'light'
                  ? 'bg-blueGray-50 text-blueGray-500 border-blueGray-100'
                  : 'bg-emerald-800 text-emerald-300 border-emerald-700',
              ]"
                    >Name
                    </th>
                    <th
                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left"
                        :class="[
                color === 'light'
                  ? 'bg-blueGray-50 text-blueGray-500 border-blueGray-100'
                  : 'bg-emerald-800 text-emerald-300 border-emerald-700',
              ]"
                    >Email

                    </th>
                    <th
                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left"
                        :class="[
                color === 'light'
                  ? 'bg-blueGray-50 text-blueGray-500 border-blueGray-100'
                  : 'bg-emerald-800 text-emerald-300 border-emerald-700',
              ]"
                    >

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
                            :class="[
            color === 'light' ? 'text-blueGray-600' : 'text-white']"
                        >{{ user.email }}</span>
                    </th>
                    <th>
                        <button @click="deleteUser(user.id)">
                            <svg class="w-6 h-6 text-gray-800 dark:text-[#e3342f]"
                                 aria-hidden="true"
                                 xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                      d="M8.6 2.6A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4c0-.5.2-1 .6-1.4ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </button>
                    </th>
                    <th>
                        <router-link :to="{name: 'admin.edit', params: {id: user.id}}">
                            <!--                        <button @click="updateUser(user.id)">-->
                            <svg class="w-6 h-6 text-gray-800 dark:text-[blue]" aria-hidden="true"
                                 xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                      d="M11.3 6.2H5a2 2 0 0 0-2 2V19a2 2 0 0 0 2 2h11c1.1 0 2-1 2-2.1V11l-4 4.2c-.3.3-.7.6-1.2.7l-2.7.6c-1.7.3-3.3-1.3-3-3.1l.6-2.9c.1-.5.4-1 .7-1.3l3-3.1Z"
                                      clip-rule="evenodd"/>
                                <path fill-rule="evenodd"
                                      d="M19.8 4.3a2.1 2.1 0 0 0-1-1.1 2 2 0 0 0-2.2.4l-.6.6 2.9 3 .5-.6a2.1 2.1 0 0 0 .6-1.5c0-.2 0-.5-.2-.8Zm-2.4 4.4-2.8-3-4.8 5-.1.3-.7 3c0 .3.3.7.6.6l2.7-.6.3-.1 4.7-5Z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </router-link>
                        <!--                        </button>-->
                    </th>
                </tr>
                </tbody>
            </table>
        </div>
    </div>

</template>

<style scoped>

</style>