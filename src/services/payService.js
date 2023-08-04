import { httpService4, server, httpService2 } from "../utils/https";

export default class PayService {
  getRegularStudent = async (code) => {
    try {
      let res = await httpService4.get(`consulta_caja/${code}`);

      if (res.data.status) {
        return {
          ok: true,
          success: true,
          message: res.data.message,
          data: res.data.data,
        };
      } else {
        return {
          ok: true,
          success: false,
          message: res.data.message,
          data: null,
        };
      }
    } catch (error) {
      return {
        ok: false,
        success: false,
        message: "Ocurrió un error en el servicio, comunicarse con OTI.",
        data: null,
      };
    }
  };

  getConditionStudent = async (code) => {
    try {
      let res = await httpService2.get(`CONDITIONS/?w=${code}`);
      let data = res.data.data;

      if (data.length === 0) {
        let debts = await this.getDebtsStudent(code);

        if (debts === null) {
          return {
            ok: false,
            success: false,
            message: "Codigo ingresado no encontrado (No presenta deudas)",
            data: null,
          };
        }

        return {
          ok: true,
          success: true,
          message: "Estudiante apto",
          data: debts,
        };
      } else {
        return {
          ok: true,
          success: false,
          message: "Estudiante NO apto",
          data: data,
        };
      }
    } catch (error) {
      return {
        ok: false,
        success: false,
        message: "Ocurrió un error en el servicio, comunicarse con OTI.",
        data: null,
      };
    }
  };

  getDebtsStudent = async (code) => {
    let res = await httpService2.get(`DEBTS/?w=${code}`);
    let data = res.data;
    if (data.debts.length === 0) {
      return null;
    } else {
      return data.debts;
    }
  };

  getOtherPerson = async (term) => {
    try {
      let res = await server.get(`otrapersona/?term=${term}`);
      return res.data;
    } catch (error) {
      return error.data;
    }
  };

  savePay = async (data) => {
    try {
      let formData = new FormData();
      formData.append("person", JSON.stringify(data.person));
      formData.append("details", JSON.stringify(data.details));
      let res = await server.post("papeleta/", formData);
      return res.data;
    } catch (error) {
      return res.data;
    }
  };

  savePayMat = async (data) => {
    try {
      let formData = new FormData();
      formData.append("person", JSON.stringify(data.person));
      formData.append("details", JSON.stringify(data.details));
      let res = await server.post("papeletamatricula/", formData);
      return res.data;
    } catch (error) {
      return error.data;
    }
  };
}
