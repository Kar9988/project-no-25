<script setup>


import {useCategoryStore} from "../../store/categoryStore.js";
import {computed, onMounted} from "vue";

const categoryStore = useCategoryStore();
const categories = computed(() => categoryStore?.categories);
import {ref} from "vue";
import ModalComponent from "../../components/Modal/Modal.vue";
import router from "../../router/index.js";

const isModalOpened = ref(false);
const category = ref({
    name: '',
    active: null,
})
const openModal = () => {
    isModalOpened.value = true;
};
const closeModal = () => {
    isModalOpened.value = false;
};
const errors = ref({})
const submitHandler = () => {
    const form = {
        name: category.value.name,
        active: category.value.active,
    }
    categoryStore.createCategory(form).then(() => {
        errors.value = {}
        closeModal()
    }).catch(e => {
        errors.value = e.errors
    })
}

const fetchVideos = (pageVal) => {
    page.value = pageVal
    categoryStore.getCategories()
}

onMounted(() => {
    categoryStore.getCategories()
})
const deleteCategory = async (videoId) => {
    const confirmDelete = window.confirm("Are you sure you want to delete this video?");
    if (!confirmDelete) {
        return;
    }
    try {
        await useCategoryStore().deleteCategory(videoId);
    } catch (error) {
        console.error("Error deleting video:", error.message);
    }
};

const page = computed({
    get() {
        return categoryStore.page
    },
    set(val) {
        categoryStore.page = val
    }
})
</script>

<template>
    <div
        class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded">
        <div class="rounded-t mb-0 px-4 py-3 border-0">
            <div class="flex flex-wrap items-center">
                <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                    <h3 class="font-semibold text-lg ">Category list
                    </h3>
                </div>
            </div>
        </div>
        <div class="block w-full overflow-x-auto px-4">
            <div>
                <button @click="openModal">
                    <div class="flex gap-2">Add Categories
                        <span>
                            <svg class="w-6 h-6 text-gray-800 dark:" aria-hidden="true"
                                 xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                      d="M9 4a4 4 0 1 0 0 8 4 4 0 0 0 0-8Zm-2 9a4 4 0 0 0-4 4v1c0 1.1.9 2 2 2h8a2 2 0 0 0 2-2v-1a4 4 0 0 0-4-4H7Zm8-1c0-.6.4-1 1-1h1v-1a1 1 0 1 1 2 0v1h1a1 1 0 1 1 0 2h-1v1a1 1 0 1 1-2 0v-1h-1a1 1 0 0 1-1-1Z"
                                      clip-rule="evenodd"/>
                            </svg>
                        </span>
                    </div>
                </button>
            </div>
            <ModalComponent class="text-black" :size-class="'w-1/2'" :isOpen="isModalOpened" @modal-close="closeModal"
                            name="first-modal">
                <pre>{{ errors }}</pre>
                <template #header>
                    <h1 class="p-[20px] text-center">Create Video</h1>
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
                                <input v-model="category.name"
                                       type="email"
                                       class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                       placeholder="Name"
                                />
                                <p v-if="errors.name" class="text-red-600 mt-1">{{ errors.name[0] }}</p>
                            </div>
                            <div>
                                <select v-model="category.active">
                                    <option disabled value="">Please select one</option>
                                    <option :value="true">Active</option>
                                    <option :value="false">Inactive</option>
                                </select>
                                <p v-if="errors.active" class="text-red-600 mt-1">{{ errors.active[0] }}</p>
                            </div>
                        </form>
                    </div>
                </template>
                <template #footer>
                    <div class="text-center mt-6">
                        <button @click="submitHandler"
                                class="w-[200px] text-black bg-blueGray-800 active:bg-blueGray-600 text-sm font-bold uppercase px-6 py-3 rounded shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1 w-full ease-linear transition-all duration-150"
                                type="button"
                        >
                            Create Video
                        </button>
                    </div>
                </template>
            </ModalComponent>
            <div class="mb-[24px]">

            </div>
            <table class="items-center  w-full bg-transparent border-collapse">
                <thead>
                <tr>
                    <th
                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left "
                    >ID
                    </th>
                    <th
                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left "
                    >Name
                    </th>
                    <th
                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left "
                    > Status

                    </th>
                </tr>
                </thead>

                <tbody v-for="category in categories" :key="category.id">
                <tr>
                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                        {{ category.id }}
                    </td>
                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                        {{ category.name }}
                    </td>
                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                        {{ category.active === 1 ? 'true' : 'false' }}
                    </td>


                    <td>
                        <th class="flex p-[10px]">
                            <div class="pr-[8px]">
                                <button @click="deleteCategory(category.id)">
                                    <svg class="w-6 h-6"
                                         aria-hidden="true"
                                         xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 24 24">
                                        <path fill-rule="evenodd"
                                              d="M8.6 2.6A2 2 0 0 1 10 2h4a2 2 0 0 1 2 2v2h3a1 1 0 1 1 0 2v12a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V8a1 1 0 0 1 0-2h3V4c0-.5.2-1 .6-1.4ZM10 6h4V4h-4v2Zm1 4a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Zm4 0a1 1 0 1 0-2 0v8a1 1 0 1 0 2 0v-8Z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </button>
                            </div>

                            <div class="pl-[8px]">
                                <router-link :to="{name: 'categories.edit', params: {id: category.id}}">
                                    <!--                                                            <button @click="updateCategory(category.id)">-->
                                    <svg class="w-6 h-6" aria-hidden="true"
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
        <div class="flex items-center gap-4 justify-center mb-4">
            <button :disabled="!categoryStore.prevPage"
                    @click="fetchVideos(page-1)"
                    class="flex items-center gap-2 px-6 py-3 font-sans text-xs font-bold text-center text-gray-900 uppercase align-middle transition-all rounded-lg select-none hover:bg-gray-900/10 active:bg-gray-900/20 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                    type="button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                     stroke="currentColor"
                     aria-hidden="true" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                Previous
            </button>
            <div class="flex items-center gap-2">
                <div v-for="pageItem in categoryStore.totalPages" :key="pageItem" @click="fetchVideos(pageItem)">
                    <button
                        v-if="page === pageItem"
                        class="relative h-10 max-h-[40px] w-10 max-w-[40px] select-none text-white rounded-lg bg-gray-900 text-center align-middle font-sans text-xs font-medium uppercase  shadow-md shadow-gray-900/10 transition-all hover:shadow-lg hover:shadow-gray-900/20 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        type="button">
                                  <span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                                    {{ pageItem }}
                                  </span>
                    </button>
                    <button
                        v-else
                        class="relative h-10 max-h-[40px] w-10 max-w-[40px] select-none rounded-lg text-center align-middle font-sans text-xs font-medium uppercase text-gray-900 transition-all hover:bg-gray-900/10 active:bg-gray-900/20 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        type="button">
                                  <span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                                    {{ pageItem }}
                                  </span>
                    </button>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <div v-for="pageItem in categoryStore.totalPages" :key="pageItem" @click="fetchVideos(pageItem)">
                    <button
                        v-if="page === pageItem"
                        class="relative h-10 max-h-[40px] w-10 max-w-[40px] select-none text-white rounded-lg bg-gray-900 text-center align-middle font-sans text-xs font-medium uppercase  shadow-md shadow-gray-900/10 transition-all hover:shadow-lg hover:shadow-gray-900/20 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        type="button">
                                  <span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                                    {{ pageItem }}
                                  </span>
                    </button>
                    <button
                        v-else
                        class="relative h-10 max-h-[40px] w-10 max-w-[40px] select-none rounded-lg text-center align-middle font-sans text-xs font-medium uppercase text-gray-900 transition-all hover:bg-gray-900/10 active:bg-gray-900/20 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                        type="button">
                                  <span class="absolute transform -translate-x-1/2 -translate-y-1/2 top-1/2 left-1/2">
                                    {{ pageItem }}
                                  </span>
                    </button>
                </div>
            </div>
            <button
                :disabled="!categoryStore.nextPage"
                @click="fetchVideos(page+1)"
                class="flex items-center gap-2 px-6 py-3 font-sans text-xs font-bold text-center text-gray-900 uppercase align-middle transition-all rounded-lg select-none hover:bg-gray-900/10 active:bg-gray-900/20 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                type="button">
                Next
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2"
                     stroke="currentColor"
                     aria-hidden="true" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                </svg>
            </button>
        </div>

    </div>

</template>

<style scoped>

</style>
