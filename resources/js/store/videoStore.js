import {defineStore} from "pinia";
import axios from "../axios-configured.js";
import Swal from 'sweetalert2';


export const useVideoStore = defineStore('videoStore', {
    state: () => ({
        videos: [],
        video: {},
        page: 1,
        total: 0,
        nextPage: null,
        prevPage: null,
        totalPages: 0
    }),
    actions: {
        getVideos() {
            return new Promise((resolve, reject) => {
                axios.get(`/admin/videos?page=${this.page}`)
                    .then(({data}) => {
                        this.videos = data
                        this.total = data.total
                        this.nextPage = data.next_page_url
                        this.prevPage = data.prev_page_url
                        this.totalPages = Math.round(data.total / data.per_page)
                        resolve(data)
                    }).catch(e => {
                    reject(e)
                })
            })
        },
        deleteVideo(videoId) {
            return new Promise((resolve, reject) => {
                axios.delete(`/admin/videos/${videoId}`,)
                    .then(response => {
                        console.log(response)
                        if (Array.isArray(this.videos)) {
                            this.videos = this.videos.filter(video => video.id !== videoId)
                        } else {
                            this.getVideos().then(() => {
                                resolve(response.data);
                            }).catch((e) => {
                                reject(e);
                            });
                        }
                    }).catch((e) => {
                    reject(e);
                });
            })
        },
        getVideo(videoId) {
            return new Promise((resolve, reject) => {
                axios.get(`/admin/videos/${videoId}`,)
                    .then(response => {
                        console.log(response);
                        this.video = response.data.video;
                        resolve(response.data);
                        return response.data.use
                    }).catch((e) => {
                    reject(e);
                });
            })
        },
        createVideo(payload) {

            return new Promise((resolve, reject) => {
                axios.post(`/admin/videos`, payload)
                    .then(response => {
                        Swal.fire({
                            position: "top",
                            icon: "success",
                            title: "Video created successfully",
                            showConfirmButton: false,
                            timer: 1500
                        });
                        resolve(response.data);
                    }).catch((e) => {
                    reject(e.response.data);
                });
            })
        },
        async updateVideo(id, payload) {
            try {
                await axios.post(`/admin/video/${id}`, payload)
                    .then(() => {
                        Swal.fire({
                            position: "top",
                            icon: "success",
                            title: "Video updated successfully",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    })
            } catch (error) {
                console.error('Error posting data:', error);
            }
        },
        async changeViews(data) {
            console.log(data)
            try {
                await axios.post(`/admin/views/`, data)
                    .then(() => {
                        Swal.fire({
                            position: "top",
                            icon: "success",
                            title: "Episode updated successfully",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    })
            } catch (error) {
                console.error('Error posting data:', error);
            }
        },
        async changeLikes(data) {
            console.log(data, 'video')
            try {
                await axios.post(`/admin/likes/`, data)
                    .then(() => {
                        Swal.fire({
                            position: "top",
                            icon: "success",
                            title: "Likes updated successfully",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    })
            } catch (error) {
                console.error('Error posting data:', error);
            }
        },
        async changeEpisodesLikes(data) {
            try {
                await axios.post(`/admin/likes/`, data)
                    .then(() => {
                        Swal.fire({
                            position: "top",
                            icon: "success",
                            title: "Likes updated successfully",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    })
            } catch (error) {
                console.error('Error posting data:', error);
            }
        },

        async deleteViews(data) {
            try {
                await axios.delete(`/admin/views/${data.episode_id}`, {
                    params: {
                        count: data.views_count
                    }
                },)
                    .then(() => {
                        Swal.fire({
                            position: "top",
                            icon: "success",
                            title: "Episode updated successfully",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    })
            } catch (error) {
                console.error('Error posting data:', error);
            }
        },
        async deleteLikes(data) {
            console.log(data, 'esa')
            try {
                await axios.delete(`/admin/likes/${data.video_id}`, {
                    params: {
                        count: data.likes_count,
                        video_id: data.video_id
                    }
                },)
                    .then(() => {
                        Swal.fire({
                            position: "top",
                            icon: "success",
                            title: "Likes updated successfully",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    })
            } catch (error) {
                console.error('Error posting data:', error);
            }
        },
        async deleteEpisodesLikesCount(data) {
            console.log(data, 'esass')
            try {
                await axios.delete(`/admin/likes/${data.episode_id}`, {
                    params: {
                        count: data.likes_count,
                        episode_id: data.episode_id
                    }
                },)
                    .then(() => {
                        Swal.fire({
                            position: "top",
                            icon: "success",
                            title: "Likes updated successfully",
                            showConfirmButton: false,
                            timer: 1500
                        });
                    })
            } catch (error) {
                console.error('Error posting data:', error);
            }
        },
    }
})
