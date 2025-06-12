import axios from "axios";
//import { trans } from "laravel-vue-i18n";
import { CONFIG } from "@/helpers/constants";

class BaseService {
    /**
     * Szolgáltatási alaposztályú konstruktor
     *
     * @constructor
     */
    constructor() {
        /**
         * Axios példány alapértelmezett konfigurációval
         *
         * @type {import("axios").AxiosInstance}
         */
        this.apiClient = axios.create({
            /**
             * Az API alap URL-je
             *
             * @type {string}
             */
            baseURL: CONFIG.BASE_URL,
            /**
             * Alapértelmezett fejlécek
             *
             * @type {Object}
             */
            headers: {
                /**
                 * Content-Type fejléc
                 *
                 * @type {string}
                 */
                "Content-Type": "application/json",
                /**
                 * Fejléc elfogadása
                 *
                 * @type {string}
                 */
                Accept: "application/json",
            },
            /**
             * Megbízólevelekkel
             *
             * @type {boolean}
             */
            withCredentials: true,
        });

        /**
         * Axios elfogó a hibareakciók kezelésére
         */
        this.apiClient.interceptors.response.use(
            /**
             * Sikeres válasz
             *
             * @param {import("axios").AxiosResponse} response
             */
            (response) => response,
            /**
             * Hiba válasz
             *
             * @param {import("axios").AxiosError} error
             */
            (error) => {
                if (error.response) {
                    switch (error.response.status) {
                        /**
                         * Rossz kérés
                         */
                        case 400:
                            console.error(
                                trans("error_400") + ": ",
                                error.response.data.message ||
                                    "Invalid request",
                            );
                            break;
                        /**
                         * Jogosulatlan
                         */
                        case 401:
                            console.error(trans("error_401"));
                            window.location.href = "/login";
                            break;
                        /**
                         * Tiltott
                         */
                        case 403:
                            console.error(
                                trans("error_403") + ": ",
                                error.response.data.message || "Forbidden",
                            );
                            break;
                        /**
                         * Nem található
                         */
                        case 404:
                            console.error(
                                trans("error_404") + ": ",
                                error.response.data.message || "Not found",
                            );
                            break;
                        /**
                         *
                         */
                        case 422:
                            console.log(
                                trans('error_422') + ': ',
                                error.response.data.message || 'Unprocessable entity'
                            );
                            break;
                        /**
                         * Belső szerver hiba
                         */
                        case 500:
                            console.error(
                                trans("error_500") + ": ",
                                error.response.data.message ||
                                    "Internal server error",
                            );
                            break;
                        /**
                         * Alapértelmezett hibakezelés
                         */
                        default:
                            console.error(
                                trans("error_default") + ": ",
                                error.response.data.message || "Unknown error",
                            );
                            break;
                    }
                }
                return Promise.reject(error);
            },
        );
    }

    /**
     * GET kérés az API-hoz
     *
     * @param {string} url - Az API URL-je
     * @param {Object} config - További konfigurációs beállítások
     * @return {Promise} A válasz egy Promise-nyi
     */
    get(url, config = {}) {
        return this.apiClient.get(url, config);
    }
    /**
     * POST kérés az API-hoz
     *
     * @param {string} url - Az API URL-je
     * @param {Object} data - Az elküldend  adatok
     * @param {Object} config - További konfigurációs beállítások
     * @return {Promise} A válasz egy Promise-nyi
     */
    post(url, data, config = {}) {
        return this.apiClient.post(url, data, config);
    }

    /**
     * PUT kérés az API-hoz
     *
     * @param {string} url - Az API URL-je
     * @param {Object} data - Az elküldend  adatok
     * @param {Object} config - További konfigurációs beállítások
     * @return {Promise} A válasz egy Promise-nyi
     */
    put(url, data, config = {}) {
        return this.apiClient.put(url, data, config);
    }

    /**
     * DELETE kérés az API-hoz
     *
     * @param {string} url - Az API URL-je
     * @param {Object} config - További konfigurációs beállítások
     * @return {Promise} A válasz egy Promise-nyi
     */
    delete(url, config = {}) {
        return this.apiClient.delete(url, config);
    }
}

export default BaseService;
