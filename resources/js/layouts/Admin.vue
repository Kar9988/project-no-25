<template>
  <div>
    <sidebar v-if="$route.name != 'admin.PrivacyPolicy'"/>
    <div :class="$route.name != 'admin.PrivacyPolicy' ? 'md:ml-64' : ''" class="relative  bg-blueGray-100">
<!--      <admin-navbar />-->
      <div class="px-4 mt-[60px] md:px-10 mx-auto w-full -m-24 ">
        <router-view />
      </div>
    </div>
  </div>
</template>
<script>
import AdminNavbar from "../components/Navbars/AdminNavbar.vue";
import Sidebar from "../components/Sidebar/Sidebar.vue";
import HeaderStats from "../components/Headers/HeaderStats.vue";
import FooterAdmin from "../components/Footers/FooterAdmin.vue";
import router from "../router/index.js";
export default {
  name: "admin-layout",
    methods: {
        router() {
            return router
        }
    },
  components: {
    AdminNavbar,
    Sidebar,
    HeaderStats,
    FooterAdmin,
  },
};
</script>
