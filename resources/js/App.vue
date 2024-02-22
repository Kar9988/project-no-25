<template>
    <div id="app">
        <router-view/>
    </div>
</template>
<script setup>
import {useAdminStore} from "./store/adminStore.js";
import {onMounted} from "vue";
const adminUserStore = useAdminStore();

onMounted(() => {
    adminUserStore.getAuthUser()
})
</script>
