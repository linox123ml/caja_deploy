<template>
  <div class="app-wrapper">
    <v-container>
      <template v-if="postulant">
        <FormPay :postulant="postulant" />
      </template>

      <template v-else>
        <SearchPostulant @on-success="setPostulant" />
      </template>
    </v-container>
  </div>
</template>
<script setup>
import { ref } from "vue";
import SearchPostulant from "@/components/SearchPostulant.vue";
import FormPay from "@/components/FormPay.vue";

const postulant = ref(null);

const setPostulant = (e) => {
  postulant.value = e;
};
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
