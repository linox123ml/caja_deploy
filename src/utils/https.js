import axios from "axios";

const server = axios.create({
  baseURL: import.meta.env.VITE_APP_BASE_URL_SERVER,
});

const httpAdmition = axios.create({
  baseURL: import.meta.env.VITE_APP_BASE_URL_API_ADMITION,
});

const httpService4 = axios.create({
  baseURL: import.meta.env.VITE_APP_BASE_URL_API_SERVICE4,
});

const httpService2 = axios.create({
  baseURL: import.meta.env.VITE_APP_BASE_URL_API_SERVICE2,
});
//VITE_APP_API_PAYMET_API
const httpPayment = axios.create({
  baseURL: import.meta.env.VITE_APP_API_PAYMET_API,
});

export { server, httpAdmition, httpService4, httpService2, httpPayment };
