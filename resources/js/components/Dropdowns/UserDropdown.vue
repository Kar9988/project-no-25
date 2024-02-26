<template>
  <div>
    <a
      class="text-blueGray-500 block"
      href="#admin"
      ref="btnDropdownRef"
    >
      <div class="items-center flex">
<!--        <span-->
<!--          class="w-12 h-12 text-sm text-white bg-blueGray-200 inline-flex items-center justify-center rounded-full"-->
<!--        >-->
<!--          <img-->
<!--            alt="..."-->
<!--            class="w-full rounded-full align-middle border-none shadow-lg"-->
<!--            :src="image"-->
<!--          />-->
<!--        </span>-->
      </div>
    </a>
  </div>
</template>

<script>
import { createPopper } from "@popperjs/core";

import image from "../../../assets/img/team-1-800x800.jpg";

export default {
  data() {
    return {
      image: image,
    };
  },
  methods: {

  },
  mounted() {

  }
};
</script>
