<template>
    <div id="app">
        <router-view/>
    </div>
</template>
<script setup>
import {useAdminStore} from "./store/adminStore.js";
import {useUserStore} from "./store/userStore.js";
import {onMounted} from "vue";
const adminUserStore = useAdminStore();
const userStore = useUserStore();


onMounted(() => {
    adminUserStore.getAuthUser()
        userStore.getUsers()
})
</script>
