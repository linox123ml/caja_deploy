import { server } from "../utils/https";

export default class AuthService {
  validateAuth = async () => {
    try {
      let res = await server.get("auth/");
      return res.data;

    } catch (error) {
        
    return error;
    }
  };
}
