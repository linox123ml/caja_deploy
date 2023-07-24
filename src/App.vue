<template>
  <v-app id="inspire">
    <v-app-bar app density="prominent" class="bg-blue-darken-4">
      <div class="pa-2">
        <img src="@/assets/logo.png" width="50" alt="" />
      </div>
      <v-toolbar-title>CAJA - UNA-PUNO</v-toolbar-title>
      <v-toolbar-subtitle>
        <v-chip label> Examen General 2023</v-chip></v-toolbar-subtitle
      >
      <v-spacer></v-spacer>
      <v-btn>
        <a href="/" target="blank" class="text-white"> Ir a caja </a>
      </v-btn>
    </v-app-bar>

    <v-main>
      <v-container>
        <!-- <template v-if="postulant">
        <FormPay :postulant="postulant" />
      </template> -->
        <template v-if="!isLogged">
          <v-row>
            <v-col cols="12" class="text-center">
              <span class="text-sm"> No autorizado </span>
            </v-col>
            <v-col cols="12" class="text-center">
              <span class="text-h4"> Iniciar sesion </span>
            </v-col>
            <v-col cols="12" class="text-center">
              <a href="/" target="blank"> - IR AL LOGIN - </a>
            </v-col>

            <v-col cols="12" class="text-center">
              Ya inicié sesion:
              <v-btn variant="tonal" @click="init">Actualizar</v-btn>
            </v-col>
          </v-row>
        </template>
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
