import axios from "axios";

axios.defaults.baseURL = import.meta.env.VITE_API_BASE_URL
const token =  localStorage.getItem('token')
if (token){
    axios.defaults.headers.common['Authorization'] = `Bearer ${token}`
}
axios.interceptors.response.use(
    response => response,
    error => {
        if (error.response && error.response.status === 401) {
            localStorage.removeItem('token');
        }
        return Promise.reject(error);
    }
);
export default axios;
