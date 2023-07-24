<template>
  <v-app id="inspire">
    <v-app-bar app density="prominent" class="bg-blue-darken-4">
      <div class="pa-2">
        <img src="@/assets/logo.png" width="50" alt="" />
      </div>
      <v-toolbar-title>CAJA - UNA-PUNO</v-toolbar-title>
      <v-toolbar-subtitle>Examen General</v-toolbar-subtitle>
      <v-spacer></v-spacer>
      <v-btn>
        Salir
      </v-btn>
    </v-app-bar>

    <v-main>
      <v-container>
        <!-- <template v-if="postulant">
        <FormPay :postulant="postulant" />
      </template> -->
        <template v-if="!isLogged"> no autorizado </template>
        <template v-else>
          <SearchPostulant />
        </template>
      </v-container>
    </v-main>
  </v-app>
</template>
<script setup>
import { ref } from "vue";
import SearchPostulant from "@/components/SearchPostulant.vue";
import { AuthService } from "./services/index";

const authService = new AuthService();

const isLogged = ref(true);

const mode = import.meta.env.VITE_APP_MODE;

const init = async () => {
  if (mode === "production") {
    let res = await authService.validateAuth();
    isLogged.value = res.success;
  }
};

init();
</script>

<style>
.app-wrapper {
  min-height: 100vh;
  width: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  background-color: azure;
}
</style>
