<template>
  <div>
    <sidebar />
    <div class="relative md:ml-64 bg-blueGray-100">
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
export default {
  name: "admin-layout",
  components: {
    AdminNavbar,
    Sidebar,
    HeaderStats,
    FooterAdmin,
  },
};
</script>
