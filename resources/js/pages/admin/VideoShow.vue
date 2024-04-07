<script setup>


import {computed, onMounted, ref} from "vue";
import {useVideoStore} from "../../store/videoStore.js";
import {useRoute} from "vue-router";
import router from "../../router/index.js";

const videoStore = useVideoStore()
const route = useRoute()
import {useCategoryStore} from "../../store/categoryStore.js";
import UploadChunk from "./UploadChunk.vue";
const categoryStore = useCategoryStore();
const categories = computed(() => categoryStore?.categories);

const video = ref({})
const errors = ref({})
const fileUrl = ref({})
const episodeSkilleton = ref({
    title: null,
    duration: null,
    cover_img: null,
    source: null,
    position: null,
    price:null,
})
const addEpisode = () => {
    episodeSkilleton.value.position = video.value.episodes.length + 1
    video.value.episodes.push({...episodeSkilleton.value});
};
onMounted(() => {
    categoryStore.getCategories()

    videoStore.getVideo(route.params.id).then(() => {
        video.value = videoStore.video
        fileUrl.value = computed(() => video.value.cover_img ? URL.createObjectURL(video.value.cover_img) : '')
        video.value.episodes.forEach((episode) => {
            // urlByFile(episode, episode.source, 'source_src')
            // urlByFile(episode, episode.thumb, 'thumb_src')
        })
    })
})
const urlByFile = async (episode, fileName, field) => {
    if (typeof fileName === 'string') {
        let response = await fetch(`${window.location.origin}/storage/${fileName}`);
        let data = await response.blob();

        let file = new File([data], fileName);
        if (file) {
            episode[field] = URL.createObjectURL(file);
        }
    } else if (fileName !== undefined) {
        return URL.createObjectURL(fileName);
    }
}
const onReplaceEmisodeThumbnail = (e, index) => {
    video.value.episodes[index].thumb = null;
    setTimeout(() => document.querySelector(`#episode-thumbnail-input-${index}`).click())
}
const onReplaceEmisodeSource = (e, index) => {
    video.value.episodes[index].source = null;
    setTimeout(() => document.querySelector(`#episode-source-input-${index}`).click())
}
const changeVideoName = (filename, index) => {
    video.value.episodes[index].source_src = filename
}
const removeVideo = (filename, index) => {
    video.value.episodes[index].source_src = null
}
const onEpisodeSourceChoosed = (e, index) => {
    video.value.episodes[index].source_src = URL.createObjectURL(e.target.files[0])
    video.value.episodes[index].source = e.target.files[0]
}

const onEpisodeThumbnailChoosed = (e, index) => {
    video.value.episodes[index].thumb_src = URL.createObjectURL(e.target.files[0])
    video.value.episodes[index].thumb = e.target.files[0]
}
const changeViewsCount = (id, data) => {
    const form = {
        episode_id: id,
        views_count: data,
        duration: 1,
    }
    videoStore.changeViews(form)
}
const deleteViewsCount = (id, data) => {
    const form = {
        episode_id: id,
        views_count: data,
        duration: 1
    }
    videoStore.deleteViews(form)
}

const changeEpisodesLikesCount = (id, data) => {
    const form = {
        episode_id: id,
        likes_count: data,
    }

    videoStore.changeEpisodesLikes(form)
}
const deleteEpisodesLikesCount = (id, data) => {
    const form = {
        episode_id: id,
        likes_count: data,
    }
    videoStore.deleteEpisodesLikesCount(form)
}

const changeLikesCount = (id, data) => {
    const form = {
        video_id: id,
        likes_count: data,
    }

    videoStore.changeLikes(form)
}
const deleteLikesCount = (id, data) => {
    const form = {
        video_id: id,
        likes_count: data,
    }
    videoStore.deleteLikes(form)
}


const submitHandler = () => {
    const form = new FormData;
    Object.keys(video.value).forEach((index) => {
        if (index !== 'cover_img' && index !== 'episodes' && typeof video.value[index] === 'object') {
            video.value[index].forEach((episodeKey) => {
                form.append(index, video.value[index][episodeKey])
            });
        } else {
            if (index !== 'cover_img' || typeof video.value[index] !== 'string') {
                form.append(index, video.value[index])
            }
        }
    })
    video.value.episodes.forEach((episode, episodeKey) => {
        console.log(episode, episodeKey)
        if (typeof episode.thumb !== 'string') {
            form.append(`episodes[${episodeKey}][thumb]`, episode.thumb)
        }
        if (episode.source_src) {
            form.append(`episodes[${episodeKey}][source]`, episode.source_src)
        }
        if (episode.id) {
            form.append(`episodes[${episodeKey}][id]`, episode.id)
        }
        form.append(`episodes[${episodeKey}][title]`, episode.title)
        form.append(`episodes[${episodeKey}][price]`, episode.price)
        form.append(`episodes[${episodeKey}][position]`, episode.position)
    })
    videoStore.updateVideo(video.value.id, form).then(() => {
        errors.value = {}
    }).catch(e => {
        errors.value = e.details
    })
}

</script>

<template>
    <div>
        <div v-if="video.id" class="mt-[15px] p-[20px] gap-[10px]">
            <form class="p-[24px]" @submit.prevent>
                <div class="relative w-full mb-3">
                    <label
                        class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                    >
                        Title
                    </label>
                    <input v-model="video.title"
                           type="text"
                           class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                           placeholder="Name"
                    />
                    <p v-if="errors.title" class="text-red-600 mt-1">{{ errors.title[0] }}</p>
                </div>

                <div class="relative w-full mb-3">
                    <label
                        class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
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
                <div>
                    <div class="text-black"> Categories</div>
                    <select v-model="video.category_id" id="countries"
                            class="title bg-gray-100 border border-gray-300 p-2 mb-4 outline-none">
                        <option v-for="category in categories.data" :key="category.id" :value="category.id">
                            {{ category.name }}
                        </option>
                    </select>
                </div>
                <div class="relative w-full mb-3">
                    <label
                        class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                    >
                        Likes Count
                    </label>
                    <input v-model="video.likes_count"
                           type="text"
                           class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                           placeholder="likes"
                    />
                    <div class="gap-[10px] flex">
                        <button
                            class="p-[8px] py-2 px-5 bg-violet-500 text-white rounded-md mt-[10px] shadow-md hover:bg-violet-700 focus:outline-none focus:ring focus:ring-violet-400 focus:ring-opacity-75"
                            @click="changeLikesCount(video.id, video.likes_count)">Add Likes
                        </button>
                        <button
                            class="p-[8px] py-2 px-5 bg-red-500 text-white rounded-md mt-[10px] shadow-md hover:bg-red-700 focus:outline-none focus:ring focus:ring-red-400 focus:ring-opacity-75"
                            @click="deleteLikesCount(video.id, video.likes_count)">Delete Likes
                        </button>

                    </div>
                </div>
                <div class="relative w-full mb-3">
                    <div class="">
                        <label for="" class="block uppercase text-blueGray-600 text-xs font-bold mb-2">Thumbnail</label>
                        <div
                            class="hover:bg-gray-100 relative max-w-max group flex items-center justify-center cursor-pointer">
                                        <span class="absolute hidden group-hover:inline-block font-bold cursor-pointer">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                                 stroke-width="1.5" stroke="red" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                      d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                            </svg>
                                        </span>
                            <img @click="onReplaceFile" class="max-w-[200px] group-hover:opacity-50 bg-black"
                                 :src="fileUrl" alt="">
                        </div>
                        <label v-show="!fileUrl" for="dropzone-file"
                               class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer ">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                     xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                          stroke-width="2"
                                          d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                </svg>
                                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span>
                                    or drag and drop</p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">SVG, PNG, JPG or GIF</p>
                            </div>
                            <!--                            <input ref="coverFileUpload" id="dropzone-file" type="file" class="hidden" @change="onFileChoosed($event)"/>-->
                        </label>
                        <p v-if="errors.cover_img" class="text-red-600 mt-1">{{ errors.cover_img[0] }}</p>

                    </div>
                </div>
                <div v-if="video.episodes.length">
                    <h3 class="font-bold mb-4">Episodes</h3>
                    <div v-for="(episode, index) in video.episodes" :key="index" class="pl-8 "
                         :class="video.episodes[index+1] ? 'border-b-2 pb-3 mb-3' : ''">
                        <div class="relative w-full mb-3">
                            <label
                                class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                            >
                                Title
                            </label>
                            <input v-model="episode.title"
                                   type="text"
                                   class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                   placeholder="Name"
                            />
                        </div>
                        <div class="relative w-full mb-3">
                            <label
                                class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                            >
                                Price
                            </label>
                            <input v-model="episode.price"
                                   type="text"
                                   class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                   placeholder="Name"
                            />
                        </div>
                        <div class="relative w-full mb-3">
                            <label
                                class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                            >
                                Views Count
                            </label>
                            <input v-model="episode.views_count"
                                   type="text"
                                   class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                   placeholder="views"
                            />
                            <div class="gap-[10px] flex">
                                <button
                                    class="p-[8px] py-2 px-5 bg-violet-500 text-white rounded-md mt-[10px] shadow-md hover:bg-violet-700 focus:outline-none focus:ring focus:ring-violet-400 focus:ring-opacity-75"
                                    @click="changeViewsCount(video.episodes[index].id, episode.views_count)">Add Views
                                </button>
                                <button
                                    class="p-[8px] py-2 px-5 bg-red-500 text-white rounded-md mt-[10px] shadow-md hover:bg-red-700 focus:outline-none focus:ring focus:ring-red-400 focus:ring-opacity-75"
                                    @click="deleteViewsCount(video.episodes[index].id, episode.views_count)">Delete
                                    Views
                                </button>

                            </div>
                        </div>
                        <div class="relative w-full mb-3">
                            <label
                                class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                            >
                                Likes Count
                            </label>
                            <input v-model="episode.likes_count"
                                   type="text"
                                   class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                   placeholder="likes"
                            />
                            <div class="gap-[10px] flex">
                                <button
                                    class="p-[8px] py-2 px-5 bg-violet-500 text-white rounded-md mt-[10px] shadow-md hover:bg-violet-700 focus:outline-none focus:ring focus:ring-violet-400 focus:ring-opacity-75"
                                    @click="changeEpisodesLikesCount(video.episodes[index].id, episode.likes_count)">Add
                                    Likes
                                </button>
                                <button
                                    class="p-[8px] py-2 px-5 bg-red-500 text-white rounded-md mt-[10px] shadow-md hover:bg-red-700 focus:outline-none focus:ring focus:ring-red-400 focus:ring-opacity-75"
                                    @click="deleteEpisodesLikesCount(episode.id, episode.likes_count)">Delete Likes
                                </button>
                            </div>
                        </div>
                        <div class="relative w-full mb-3">
                            <label
                                class="block uppercase text-blueGray-600 text-xs font-bold mb-2"
                            >
                                Position
                            </label>
                            <input v-model="episode.position"
                                   type="number"
                                   :max="video.episodes.length"
                                   class="border-0 px-3 py-3 placeholder-blueGray-300 text-blueGray-600 bg-white rounded text-sm shadow focus:outline-none focus:ring w-full ease-linear transition-all duration-150"
                                   placeholder="Position"
                            />
                        </div>

                        <div class="relative w-full mb-3">
                            <div>
                                <label for="" class="block uppercase text-blueGray-600 text-xs font-bold mb-2">Thumbnail
                                    img</label>
                                <div
                                    class="hover:bg-gray-100 relative max-w-max group flex items-center justify-center cursor-pointer"
                                    @click="onReplaceEmisodeThumbnail($event, index)">
                                                <span
                                                    class="absolute hidden group-hover:inline-block font-bold cursor-pointer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="red"
                                                         class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                                    </svg>
                                                </span>
                                    <img v-if="episode.thumb || episode.thumb_src"
                                         class="max-w-[200px] group-hover:opacity-50 bg-black mb-2"
                                         :src="episode.thumb_src??episode.thumb" alt="">
                                </div>
                                <label v-show="!episode.thumb"
                                       class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer ">
                                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                        <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true"
                                             xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2"
                                                  d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                                        </svg>
                                        <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span
                                            class="font-semibold">Click to upload</span> or drag and drop</p>
                                    </div>
                                    <input type="file" class="hidden" :id="`episode-thumbnail-input-${index}`"
                                           @change="onEpisodeThumbnailChoosed($event, index)"/>
                                </label>
                            </div>
                        </div>

                        <div class="relative w-full mb-3">
                            <div :id="`episode-source-${index}`">
                                <label for=""
                                       class="block uppercase text-blueGray-600 text-xs font-bold mb-2">Video</label>
                                <div class="relative max-w-max group flex items-center justify-center cursor-pointer"
                                     @click="onReplaceEmisodeSource($event, index)">
                                                <span
                                                    class="absolute hidden group-hover:inline-block font-bold cursor-pointer">
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none"
                                                         viewBox="0 0 24 24" stroke-width="1.5" stroke="red"
                                                         class="w-6 h-6">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/>
                                                    </svg>
                                                </span>
                                    <video autoplay v-if="episode.source || episode.source_src"
                                           class="max-w-[200px] group-hover:opacity-50 bg-black mb-2"
                                           :src="episode.source_src??episode.source" alt=""/>
                                </div>
                                <UploadChunk v-if="!episode.source" @removeVideo="removeVideo($event, index)"
                                             @filename="changeVideoName($event, index)"  :episode="episode"/>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="p-5 bg-slate-400 rounded-2xl cursor-pointer" @click="addEpisode">
                    <div class="flex items-center justify-between">
                        Add episode
                        <span class="mr-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                             stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                          <path stroke-linecap="round" stroke-linejoin="round"
                                                d="M12 4.5v15m7.5-7.5h-15"/>
                                        </svg>
                                    </span>
                    </div>
                </div>
                <div class="text-center mt-6">
                    <button @click="submitHandler"
                            class="w-[200px] text-black bg-blueGray-800 active:bg-blueGray-600 text-sm font-bold uppercase px-6 py-3 rounded shadow hover:shadow-lg outline-none focus:outline-none mr-1 mb-1 w-full ease-linear transition-all duration-150"
                            type="button">
                        Update Video
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<style scoped>

</style>
