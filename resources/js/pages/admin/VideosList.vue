<script setup>

import {useVideoStore} from "../../store/videoStore.js";
import {computed, onMounted} from "vue";

const videoStore = useVideoStore();
const videos = computed(() => videoStore?.videos);

import {useCategoryStore} from "../../store/categoryStore.js";

const categoryStore = useCategoryStore();
const categories = computed(() => categoryStore?.categories);


import {ref} from "vue";
import ModalComponent from "../../components/Modal/Modal.vue";
import router from "../../router/index.js";

const isModalOpened = ref(false);
const video = ref({
    title: '',
    description: '',
    cover_img: '',
    category_id: '',
    episodes: []
})
const openModal = () => {
    isModalOpened.value = true;
};

const addEpisode = () => {
    video.value.episodes.push({...episodeSkilleton.value});
};
const closeModal = () => {
    isModalOpened.value = false;
};
const episodeSkilleton = ref({
    title: null,
    duration: null,
    cover_img: null,
    source: null,
    category_id: null,
})
const errors = ref({})
const submitHandler = () => {
    const form = new FormData;
    Object.keys(video.value).forEach((index) => {
        if (index !== 'cover_img' && index !== 'episodes' && typeof video.value[index] === 'object') {
            video.value[index].forEach((episodeKey) => {
                form.append(index, video.value[index][episodeKey])
            });
        } else {
            form.append(index, video.value[index])
        }
    })
    video.value.episodes.forEach((episode, episodeKey) => {
        form.append(`episodes[${episodeKey}][thumb]`, episode.thumb)
        form.append(`episodes[${episodeKey}][source]`, episode.source)
        form.append(`episodes[${episodeKey}][title]`, episode.title)
    })
    videoStore.createVideo(form).then(() => {
        errors.value = {}
        closeModal()
    }).catch(e => {
        errors.value = e.details
    })
}
const fileUrl = computed(() => video.value.cover_img ? URL.createObjectURL(video.value.cover_img): '')
const onFileChoosed = (e) => {
    video.value.cover_img = e.target.files[0]
}
const onReplaceFile = (e) => {
    video.value.cover_img = null
    setTimeout(() => {
        coverFileUpload.value.click()
    }, 300)
}

const onReplaceEmisodeThumbnail = (e, index) => {
    video.value.episodes[index].thumb = null;
    setTimeout(() => document.querySelector(`#episode-thumbnail-input-${index}`).click())
}
const onReplaceEmisodeSource = (e, index) => {
    video.value.episodes[index].source = null;
    setTimeout(() => document.querySelector(`#episode-source-input-${index}`).click())
}

const onEpisodeSourceChoosed = (e, index) => {
    video.value.episodes[index].source = e.target.files[0]
}

const onEpisodeThumbnailChoosed = (e, index) => {
    video.value.episodes[index].thumb = e.target.files[0]
}

const urlByFile = (file) => {
    return file ? URL.createObjectURL(file): ''
}
const coverFileUpload = ref(null)
const fetchVideos = (pageVal) => {
    page.value = pageVal
    videoStore.getVideos()
}
onMounted(() => {
    videoStore.getVideos()
    categoryStore.getCategories()
})
const deleteVideo = async (videoId) => {
    const confirmDelete = window.confirm("Are you sure you want to delete this video?");
    if (!confirmDelete) {
        return;
    }
    try {
        await useVideoStore().deleteVideo(videoId);

    } catch (error) {
        console.error("Error deleting video:", error.message);
    }
};

const page = computed({
    get() {
        return videoStore.page
    },
    set(val) {
        videoStore.page = val
    }
})
</script>

<template>
    <div
        class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded">
        <div class="rounded-t mb-0 px-4 py-3 border-0">
            <div class="flex flex-wrap items-center">
                <div class="relative w-full px-4 max-w-full flex-grow flex-1">
                    <h3 class="font-semibold text-lg " >Videos list
                    </h3>
                </div>
            </div>
        </div>
        <pre></pre>
        <div class="block w-full overflow-x-auto px-4">
            <div>
                <button @click="openModal">
                    <div class="flex gap-2">Add Video
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
                                    Title
                                </label>
                                <input v-model="video.title"
                                       type="email"
                                       class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                       placeholder="Name"
                                />
                                <p v-if="errors.title" class="text-red-600 mt-1">{{ errors.title[0] }}</p>
                            </div>
                            <div  >
                                <div  class="text-black"> Categories</div>
                                <select v-model="video.category_id"  id="countries"
                                        class="title bg-gray-100 border border-gray-300 p-2 mb-4 outline-none">
                                    <option v-for="category in categories.data" :key="category.id" :value="category.id">{{category.name}}</option>
                                </select>
                            </div>


                            <div class="relative w-full mb-3">
                                <label
                                    class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                    htmlFor="grid-password"
                                >
                                    Description
                                </label>
                                <textarea
                                    v-model="video.description"
                                    type="email"
                                    class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                    placeholder="Description"
                                />
                                <p v-if="errors.description" class="text-red-600 mt-1">{{ errors.description[0] }}</p>

                            </div>

                            <div class="relative w-full mb-3">
                                <div class="">
                                    <label for="" class="block uppercase text-blueGray-600 text-xs font-bold mb-2">Thumbnail</label>
                                    <div class="hover:bg-gray-100 relative max-w-max group flex items-center justify-center cursor-pointer">
                                        <span class="absolute hidden group-hover:inline-block font-bold cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="red" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                        </span>
                                        <img @click="onReplaceFile" class="max-w-[200px] group-hover:opacity-50 bg-black" :src="fileUrl" alt="">
                                    </div>
                                    <label  v-show="!fileUrl" for="dropzone-file" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer ">
                                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                            <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                            </svg>
                                            <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF</p>
                                        </div>
                                        <input ref="coverFileUpload" id="dropzone-file" type="file" class="hidden" @change="onFileChoosed($event)"/>
                                    </label>
                                    <p v-if="errors.cover_img" class="text-red-600 mt-1">{{ errors.cover_img[0] }}</p>

                                </div>
                            </div>
                            <div v-if="video.episodes.length">
                                <h3 class="font-bold mb-4">Episodes</h3>
                                <div v-for="(episode, index) in video.episodes" :key="index" class="pl-8 " :class="video.episodes[index+1] ? 'border-b-2 pb-3 mb-3' : ''">
                                    <div class="relative w-full mb-3">
                                        <label
                                            class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                                            htmlFor="grid-password"
                                        >
                                            Title
                                        </label>
                                        <input v-model="episode.title"
                                               type="email"
                                               class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                               placeholder="Name"
                                        />
                                    </div>

                                    <div class="relative w-full mb-3">
                                        <div>
                                            <label for="" class="block uppercase text-blueGray-600 text-xs font-bold mb-2">Thumbnail img</label>
                                            <div class="hover:bg-gray-100 relative max-w-max group flex items-center justify-center cursor-pointer" @click="onReplaceEmisodeThumbnail($event, index)">
                                                <span class="absolute hidden group-hover:inline-block font-bold cursor-pointer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="red" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                    </svg>
                                                </span>
                                                <img v-if="episode.thumb" class="max-w-[200px] group-hover:opacity-50 bg-black mb-2" :src="urlByFile(episode.thumb)" alt="">
                                            </div>
                                            <label v-show="!episode.thumb" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer ">
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                                    </svg>
                                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                                </div>
                                                <input type="file" class="hidden" :id="`episode-thumbnail-input-${index}`" @change="onEpisodeThumbnailChoosed($event, index)"/>
                                            </label>
                                        </div>
                                    </div>

                                    <div class="relative w-full mb-3">
                                        <div :id="`episode-source-${index}`">
                                            <label for="" class="block uppercase text-blueGray-600 text-xs font-bold mb-2">Video</label>
                                            <div class="relative max-w-max group flex items-center justify-center cursor-pointer" @click="onReplaceEmisodeSource($event, index)">
                                                <span class="absolute hidden group-hover:inline-block font-bold cursor-pointer"  >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="red" class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                    </svg>
                                                </span>
                                                <video autoplay v-if="episode.source"class="max-w-[200px] group-hover:opacity-50 bg-black mb-2" :src="urlByFile(episode.source)" alt=""/>
                                            </div>
                                            <label v-show="!episode.source" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer ">
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                    <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                                    </svg>
                                                    <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop </p>
                                                </div>
                                                <input type="file" class="hidden" :id="`episode-source-input-${index}`" @change="onEpisodeSourceChoosed($event, index)"/>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="p-5 bg-slate-400 rounded-2xl cursor-pointer">
                                <div class="flex items-center justify-between" @click="addEpisode">
                                    Add episode
                                    <span class="mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                    </span>
                                </div>
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
                    >Title
                    </th>
                    <th
                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left "
                        >Thumbnail

                    </th>
                    <th
                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left "
                        > Description

                    </th>
                    <th
                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left "
                        > Episodes count

                    </th>
                    <th
                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left "
                    > Category

                    </th>


                    <th
                        class="px-6 align-middle border border-solid py-3 text-xs uppercase border-l-0 border-r-0 whitespace-nowrap font-semibold text-left "
                        > Actions

                    </th>

                </tr>
                </thead>
                <tbody v-for="video in videos.videos" :key="video.id">
                <tr>
                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                        {{ video.id }}
                    </td>
                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4">
                        {{ video.title }}
                    </td>
                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left">
                            <span class="ml-3 font-bold ">{{ video.cover_img }}</span>
                    </td>
                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left">
                        {{ video.description?.substring(0, 40) }}
                    </td>
                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left">
                            <span class="ml-3 font-bold ">{{ video.episodes.length }}</span>
                    </td>
                    <td class="border-t-0 px-6 align-middle border-l-0 border-r-0 text-xs whitespace-nowrap p-4 text-left">
                        <span class="ml-3 font-bold ">{{ video.category }}</span>
                    </td>

                    <td>
                        <th class="flex p-[10px]">
                            <div class="pr-[8px]">
                                <button @click="deleteVideo(video.id)">
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
                                <router-link :to="{name: 'video.edit', params: {id: video.id}}">
                                    <!--                        <button @click="updateVideo(video.id)">-->
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
            <button :disabled="!videoStore.prevPage"
                    @click="fetchVideos(page-1)"
                    class="flex items-center gap-2 px-6 py-3 font-sans text-xs font-bold text-center text-gray-900 uppercase align-middle transition-all rounded-lg select-none hover:bg-gray-900/10 active:bg-gray-900/20 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                    type="button">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                     aria-hidden="true" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"></path>
                </svg>
                Previous
            </button>
            <div class="flex items-center gap-2">
                <div v-for="pageItem in videoStore.totalPages" :key="pageItem" @click="fetchVideos(pageItem)">
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
                :disabled="!videoStore.nextPage"
                @click="fetchVideos(page+1)"
                class="flex items-center gap-2 px-6 py-3 font-sans text-xs font-bold text-center text-gray-900 uppercase align-middle transition-all rounded-lg select-none hover:bg-gray-900/10 active:bg-gray-900/20 disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                type="button">
                Next
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"
                     aria-hidden="true" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"></path>
                </svg>
            </button>
        </div>

    </div>

</template>

<style scoped>

</style>
