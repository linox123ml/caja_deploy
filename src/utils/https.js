import axios from "axios";

const server = axios.create({
  baseURL: import.meta.env.VITE_APP_BASE_URL_SERVER,
});

const httpAdmition = axios.create({
  baseURL: import.meta.env.VITE_APP_BASE_URL_API_ADMITION,
});

export { server, httpAdmition };
