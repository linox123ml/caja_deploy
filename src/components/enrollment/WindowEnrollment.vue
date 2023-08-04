<template>
  <v-card class="mx-auto border" style="max-width: 600px">
    <v-toolbar class="bg-blue-darken-4">
      <v-tabs v-model="typeMat" color="white">
        <v-tab value="entrants"> Ingresantes </v-tab>
        <v-tab value="regular"> Regulares </v-tab>
        <v-tab value="second" disabled> Segunda Carrera </v-tab>
      </v-tabs>
    </v-toolbar>
  </v-card>

  <v-window v-model="typeMat">
    <v-window-item value="entrants">
      <form-entranst-enrollment @showMessage="snakbar =$event"/>
    </v-window-item>
    <v-window-item value="regular">
      <FormRegularEnrollmnet @showMessage="snakbar =$event" />
    </v-window-item>
  </v-window>

  <v-snackbar multiline v-model="snakbar.show" :color="snakbar.type">
    <h4>
      <strong>
        {{ snakbar.title }}
      </strong>
    </h4>
    <p>{{ snakbar.text }}</p>

    <template v-slot:actions>
      <v-btn @click="snakbar.show = false"> x </v-btn>
    </template>
  </v-snackbar>

  <iframe id="pdfFrame" style="display: none"></iframe>
</template>

<script setup>
import { ref } from "vue";
import { VWindowItem } from "vuetify/lib/components/index.mjs";
import FormEntranstEnrollment from "./FormEntranstEnrollment.vue";
import FormRegularEnrollmnet from "./FormRegularEnrollment.vue";
const typeMat = ref("entrants");
const snakbar = ref({
  show: false,
  title: null,
  text: null,
  type: null,
});
</script>
