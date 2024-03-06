<script setup>
import {computed, onMounted, ref} from "vue";
import {useRoute} from 'vue-router';
import {useCategoryStore} from "../../store/categoryStore.js";


const categoryStore = useCategoryStore()
const categories = computed(() => categoryStore?.category);
const route = useRoute();
const categoryId = ref(null);
const form = ref({
    name: '',
    active: null,
})

onMounted(() => {
    categoryId.value = route.params.id;
    getCategory(categoryId.value)
})
const errors = ref({})

const getCategory = async (userId) => {
    try {
        categories.value = await useCategoryStore().getCategory(userId);
        form.value.id = categories.value?.id
        form.value.name = categories.value?.name
        form.value.active = categories.value?.active
    } catch (error) {
        console.error("Error deleting user:", error.message);
    }
};
const UpdateCategoryData = async (categoryId, form) => {
    try {
        await useCategoryStore().updateForm(categoryId, form);
    } catch (error) {
        // console.log(error.errors,'aaddd')
        errors.value = error.errors

        console.error("Error deleting user:", error.message);
    }
};


</script>

<template>
    <div >
        <form action="">
            <div class="heading text-center font-bold text-2xl m-5 text-gray-800"> Category Update</div>
            <div
                class="editor mx-auto w-10/12 flex flex-col text-gray-800 border border-gray-300 p-4 shadow-lg max-w-2xl">
                <div class="text-black"> Name</div>

                <input name="name" v-model="form.name"
                       class="title bg-gray-100 border border-gray-300 p-2 mb-4 outline-none"
                       spellcheck="false" placeholder="Name" type="text">
                <div>
                    <p v-if="errors.name" class="text-red-600 mt-1">{{ errors.name[0] }}</p>

                    <div class="text-black"> Status</div>
                     <select v-model="form.active" id="countries"
                              class="title bg-gray-100 border border-gray-300 p-2 mb-4 outline-none">
                    <option :value="true">Active</option>
                    <option :value="false">Inactive</option>
                </select>
                </div>
                <p v-if="errors.active" class="text-red-600 mt-1">{{ errors.active[0] }}</p>
                <div style="margin-top: 10px" class="buttons flex">
                    <button @click="UpdateCategoryData(categories.id,form)" type="button"
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
