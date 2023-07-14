<template>
  <v-card  class="mx-auto" style="width: 500px">
    <v-container>
      <v-form ref="formSearch" @submit.prevent="searchPostulant">
        <v-row>
          <v-col cols="12" class="py-1">
            <div class="text-subtitle-1 mb-1">Buscar / Ingese el DNI</div>
            <v-text-field
              v-model="search"
              density="compact"
              placeholder="Buscar"
              hide-details="auto"
              variant="outlined"
              :rules="rules"
              counter
            />
          </v-col>
          <v-col cols="12">
            <v-btn color="blue" block type="submit"> Buscar postulante </v-btn>
          </v-col>
        </v-row>
      </v-form>
    </v-container>

    <v-card v-if="postulant" variant="tonal">
      <v-card-item>
        <v-list-item>
          <v-list-item-subtitle>
            Derecho de Admision (Reglamento)
          </v-list-item-subtitle>

          <v-list-item-title>
            {{
              `${postulant.nombres}  ${postulant.primer_apellido}  ${postulant.segundo_apellido}`
            }}
          </v-list-item-title>
          <v-list-item-subtitle>
            {{ postulant.nro_doc }}
          </v-list-item-subtitle>

          <template v-slot:append>
            <v-chip class="ma-2" size="x-large" label>
              <strong> {{ "S/. " + postulant.Monto }}</strong>
            </v-chip>
          </template>
        </v-list-item>
      </v-card-item>
      <v-card-actions>
        <v-btn variant="flat" color="red"> Cancelar </v-btn>
        <v-spacer></v-spacer>
        <v-btn variant="flat" color="success" @click="payPostulant">
          Pagar
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-card>
</template>
<script setup>
import { ref } from "vue";
import { AdmitionService } from "../services/";

const emit = defineEmits(["onSuccess"]);
const admitionService = new AdmitionService();
const formSearch = ref(null);
const search = ref("71576906");

const form = ref({
  tipo: 4,
  fecha: "",
  idtipo: "",
  idcodigo: "",
  codigo: "",
  clave: "",
  obs: "",
  detalle: [
    {
      idtarifa: "",
      cantidad: "",
      precio: "",
      detalle: "",
      idpadre: "",
      subtotal: "",
    },
  ],
});

const rules = ref([
  (value) => {
    if (value) return true;
    return "Ingrese el DNI.";
  },
]);

const postulant = ref(null);

const searchPostulant = async () => {
  const { valid } = await formSearch.value.validate();
  if (!valid) return;
  let res = await admitionService.searchPostulant(search.value);

  console.log(res);
  if (res.ok) {
    if (res.status) {
      postulant.value = res.data;
      //   emit("onSuccess", res.data);
    } else {
      //show error: Postulante no encontrado
    }
  } else {
    //* show error:  Admision
  }
};

const payPostulant = async () => {
  emit("onSuccess", postulant.value);
};
</script>
