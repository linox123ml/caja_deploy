import { httpAdmition } from "../utils/https";

class AdmitionService {
  searchPostulant = async (document) => {
    try {
      httpAdmition.defaults.headers["Authorization"] =
        "Bearer " + import.meta.env.VITE_APP_API_ADMITION_TOKEN;
      let res = await httpAdmition.get(`get-postulante-pago/${document}/8`);
      // let res = await httpAdmition.get(`get-ingresante-pago/${document}/${anio}/${ciclo}`);
      return {
        ok: true,
        status: res.data.status,
        message: res.data.mensaje,
        data: res.data?.data,
      };
    } catch (error) {
      return {
        ok: false,
        status: false,
        message: error,
        data: null,
      };
    }
  };

  getEntrantsPayMat = async (document) => {
    try {
      httpAdmition.defaults.headers["Authorization"] =
        "Bearer " + import.meta.env.VITE_APP_API_ADMITION_TOKEN;

      let res = await httpAdmition.get(
        `get-ingresante-pago/${document}/2024/1`
      );

      return {
        ok: true,
        status: res.data.status,
        message: res.data.mensaje,
        data: res.data?.data,
      };
    } catch (error) {
      return {
        ok: false,
        status: false,
        message: error.response.data.mensaje,
        data: null,
      };
    }
  };

  getRegularPayMat = () => {};
}
export default AdmitionService;
