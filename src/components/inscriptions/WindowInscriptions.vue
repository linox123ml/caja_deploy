<!-- <template>
  <FormInscriptions/>
</template>
<script setup>
import FormInscriptions from "./FormInscriptions.vue";
</script> -->

<template>
  <v-card class="mx-auto border" style="max-width: 600px">
    <v-toolbar class="bg-blue-darken-4">
      <v-tabs v-model="typeMat" color="white">
        <!-- <v-tab value="normal"> Inscripciones </v-tab> -->
        <v-tab value="fast"> Inscripciones OP2</v-tab>
      </v-tabs>
    </v-toolbar>
  </v-card>

  <v-window v-model="typeMat">
    <!-- <v-window-item value="rezagado">
      <FormRezagado @showMessage="snakbar = $event" />
    </v-window-item> -->
    <!-- <v-window-item value="normal">
      <FormInscriptions @showMessage="snakbar = $event" />
    </v-window-item> -->

    <v-window-item value="fast">
      <FormInscriptionsFast @showMessage="snakbar = $event" />
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
// import FormRezagado from "./FormRezagado.vue";
import FormInscriptions from "./FormInscriptions.vue";
import FormInscriptionsFast from "./FormInscriptionsFast.vue";

const typeMat = ref("fast");
const snakbar = ref({
  show: false,
  title: null,
  text: null,
  type: null,
});
</script>
./FormInscriptionsFast.vue
