import axios from "axios";

const instance = axios.create();

instance.interceptors.request.use(
    function (config) {
        // Haz algo antes que la petición sea enviada

        return config;
    }
    // function (error) {
    //     // Haz algo con el error de la petición
    //     return Promise.reject(error);
    // }
);

export default instance;
