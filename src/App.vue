<template>
  <v-app id="inspire">
    <v-app-bar app density="prominent" class="bg-blue-darken-4">
      <div class="pa-2">
        <img src="@/assets/logo.png" width="50" alt="" />
      </div>
      <v-toolbar-title>CAJA - UNA-PUNO</v-toolbar-title>

      <v-chip label> Examen CEPRE 2024</v-chip>
      <v-spacer></v-spacer>
      <v-btn>
        <a
          :href="baseUrl"
          target="blank"
          class="text-decoration-none text-white"
        >
          Ir a caja
        </a>
      </v-btn>
    </v-app-bar>

    <template v-if="!isLogged">
      <v-row>
        <v-col cols="12" class="text-center">
          <span class="text-sm"> No autorizado </span>
        </v-col>
        <v-col cols="12" class="text-center">
          <span class="text-h4"> Iniciar sesion </span>
        </v-col>
        <v-col cols="12" class="text-center">
          <a :href="baseUrl" target="blank"> - IR AL LOGIN - </a>
        </v-col>

        <v-col cols="12" class="text-center">
          Ya inicié sesion:
          <v-btn variant="tonal" @click="init">Actualizar</v-btn>
        </v-col>
      </v-row>
    </template>
    <template v-else>
      <v-main>
        <v-toolbar color="blue-darken-3">
          <v-tabs v-model="tab" color="orange">
            <v-tab value="inscription"> Inscripciones </v-tab>
            <v-tab value="enrollment" disabled> Matriculas </v-tab>
            <v-tab value="others" disabled> Otros </v-tab>
          </v-tabs>
        </v-toolbar>
        <v-window v-model="tab">
          <v-window-item value="inscription">
            <v-container>
              <WindowInscriptions />
            </v-container>
          </v-window-item>
          <v-window-item value="enrollment">
            <v-container>
              <WindowEnrollment />
            </v-container>
          </v-window-item>
          <v-window-item value="others">
            <WindowOther />
          </v-window-item>
        </v-window>
      </v-main>
    </template>

    <v-footer app absolute border>
      <div class="text-center w-100 py-2">
        {{ new Date().getFullYear() }} — <strong>OTI - UNA PUNO</strong>
      </div>
    </v-footer>
  </v-app>
</template>
<script setup>
import { ref } from "vue";
import { AuthService } from "./services/index";
import WindowInscriptions from "./components/inscriptions/WindowInscriptions.vue";
import WindowEnrollment from "./components/enrollment/WindowEnrollment.vue";
import WindowOther from "./components/others/WindowOther.vue";

const baseUrl = import.meta.env.VITE_APP_BASE_URL;

const tab = ref("inscription");

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
