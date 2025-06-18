import axios from "axios";
import { CONFIG } from "@/helpers/constants.js";


class BaseService {
    constructor() {
        this.apiClient = axios.create({
            baseURL: CONFIG.BASE_URL,
            headers: {
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            withCredentials: true,
        });

        this.apiClient.interceptors.response.use(
            (response) => response,
            (error) => {
                if( error.response ) {
                    switch(error.response.status) {
                        case 400:
                            console.error(
                                //trans("error_400") + ": ",
                                error.response.data.message ||
                                "Invalid request",
                            );
                            break;
                        case 401:
                            //console.error(trans("error_401"));
                            console.error("error_401");
                            window.location.href = "/login";
                            break;
                        case 403:
                            console.error(
                                //trans("error_403") + ": ",
                                error.response.data.message || "Forbidden",
                            );
                            break;
                        case 404:
                            console.error(
                                //trans("error_404") + ": ",
                                error.response.data.message || "Not found",
                            );
                            break;
                        case 422:
                            console.log(
                                //trans('error_422') + ': ',
                                error.response.data.message || 'Unprocessable entity'
                            );
                            break;
                        case 500:
                            console.error(
                                //trans("error_500") + ": ",
                                error.response.data.message ||
                                "Internal server error",
                            );
                            break;
                        default:
                            console.error(
                                //trans("error_default") + ": ",
                                error.response.data.message || "Unknown error",
                            );
                            break;
                    }
                }
                return Promise.reject(error);
            },
        );
    }

    get(url, config = {}) {
        return this.apiClient.get(url, config);
    };

    post(url, data, config = {}) {
        return this.apiClient.post(url, data, config);
    }

    put(url, data, config = {}) {
        return this.apiClient.put(url, data, config);
    }

    //delete(url, config = {}) {
    //    return this.apiClient.delete(url, config);
    //}

    delete(url, config = {}) {
        // Axios DELETE workaround for body
        return this.apiClient.delete(url, {
            ...config,
            data: config.data || {},
        });
    }
}

export default BaseService;
