import { httpPayment } from "../utils/https";

export default class PaymetService {
  getDebsStudent = async (code) => {
    const formData = new FormData();
    formData.append("username", code);
    try {
      let res = await httpPayment.post("get-student-payment", formData, {
        headers: {
          "Content-Type": "multipart/form-data",
        },
      });
      console.log(res);
      
      return {
        ok: true,
        success: true,
        message: "Consulta exitosa",
        data: res.data,
      };
    } catch (error) {
      return {
        ok: false,
        success: false,
        message: "No se encontro al estudiante",
        data: null,
      };
    }
  };
}
