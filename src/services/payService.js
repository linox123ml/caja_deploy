import { httpAdmition } from "../utils/https";

const searchPostulant = async (document) => {
  try {
    let res = await httpAdmition.get(
      `https://inscripciones.admision.unap.edu.pe/api/get-ingresante/${document}/4`
    );
    console.log(res.data);
  } catch (error) {
    console.log(error);
  }
};

export { searchPostulant };
