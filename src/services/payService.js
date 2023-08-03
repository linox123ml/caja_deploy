import { httpService4, server } from "../utils/https";

export default class PayService {
  getRegularStudent = async (code) => {
    try {
      let res = await httpService4.post(`consulta_caja/${code}`);
      return res.data;
    } catch (error) {
      return error.data;
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
