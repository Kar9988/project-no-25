<template>
    <div class="uploadFile" ref="uploadFile" :class="{'hidden': uploaded}">
        <label v-show="!props.episode.source" class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-300 border-dashed rounded-lg cursor-pointer ">
            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                <svg class="w-8 h-8 mb-4 text-gray-500 dark:text-gray-400" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 20 16">
                    <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2"/>
                </svg>
                <p class="mb-2 text-sm text-gray-500 dark:text-gray-400"><span class="font-semibold">Click to upload</span> or drag and drop </p>
            </div>
            <input class="max-w-[200px] group-hover:opacity-50 mb-2"  type="file" @change="handleFileChange" />
        </label>
    </div>
    <radial-progress-bar v-if="progress !== null" class="text-[18px] m-auto p-[0px]" :diameter="200"
                         :completed-steps="completedSteps"
                         :total-steps="totalSteps">
        <p>Uploaded: {{ progress }} %</p>
    </radial-progress-bar>
    <button v-if="progress !== null" class="bg-transparent hover:bg-red-500 text-black-700 rounded shadow font-semibold hover:text-white py-2 px-4 border border-black-500 hover:border-transparent rounded">
        Remove
    </button>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import RadialProgressBar from "vue3-radial-progress"
const emit = defineEmits(['filename'])


const completedSteps = ref(0);
const totalSteps = ref(10);
        const file = ref(null);
        const progress = ref(null);
        const uploaded = ref(false);

        const handleFileChange = event => {
            file.value = event.target.files[0];
            upload(event)
        };
        const props = defineProps(['episode']);
        const upload = async (e) => {
            e.preventDefault()
            const chunkSize = 4 * 1024 * 1024; // 40MB
            const totalChunks = Math.ceil(file.value.size / chunkSize);
            let startByte = 0;

            for (let chunkIndex = 0; chunkIndex < totalChunks; chunkIndex++) {
                const endByte = Math.min(startByte + chunkSize, file.value.size);
                const chunk = file.value.slice(startByte, endByte);

                const formData = new FormData();
                formData.append('file', chunk);
                formData.append('chunkIndex', chunkIndex);
                formData.append('totalChunks', totalChunks);

                try {
                   const res = await axios.post('/test', formData, {
                        headers: {
                            'Content-Type': 'multipart/form-data',
                        },
                        onUploadProgress: progressEvent => {
                            progress.value = Math.round(
                                ((chunkIndex * chunkSize + progressEvent.loaded) * 100) / file.value.size
                            );
                            uploaded.value = true
                        },
                    });
                    startByte += chunkSize;
                    emit('filename', res.data.fileName);
                } catch (error) {
                    console.error('Error uploading file chunk', error);
                    return;
                }
            }

            console.log('File uploaded successfully');


};
</script>
