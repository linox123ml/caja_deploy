<template>
  <div class="app-wrapper">
    <v-container>
      <!-- <template v-if="postulant">
        <FormPay :postulant="postulant" />
      </template> -->
      <template v-if="!noAuthorize"> no autorizado </template>
      <template v-else>
        <SearchPostulant />
      </template>
    </v-container>
  </div>
</template>
<script setup>
import { ref } from "vue";
import SearchPostulant from "@/components/SearchPostulant.vue";
import { AuthService } from "./services/index";

const authService = new AuthService();

const noAuthorize = ref(true);

const mode = import.meta.env.VITE_APP_MODE;

const init = async () => {
  if (mode === "production") {
    let res = await authService.validateAuth();
    noAuthorize.value = res.status;
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
