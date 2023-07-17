<template>
  <v-card class="mx-auto mb-5" style="width: 500px">
    <v-container>
      <v-form ref="formSearch" @submit.prevent="searchPostulant">
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

        <v-btn
          class="mt-1"
          color="blue"
          block
          type="submit"
          :loading="postulantLoading"
        >
          Buscar postulante
        </v-btn>
      </v-form>
    </v-container>
  </v-card>

  <v-row v-if="form.person">
    <v-col cols="12" md="6">
      <v-card>
        <v-card-title>Conceptos de pago </v-card-title>
        <v-divider></v-divider>

        <v-list-item
          density="compact"
          v-for="(item, index) in conceptItems"
          :key="index"
          :title="item.title"
        >
          <template v-slot:append>
            <v-switch
              :value="item"
              v-model="form.details"
              inset
              hide-details
              color="blue"
            ></v-switch>
          </template>
        </v-list-item>
      </v-card>
    </v-col>
    <v-col cols="12" md="6">
      <v-card>
        <v-card-title>
          {{
            `${form.person.nro_doc} | ${form.person.nombres}  ${form.person.primer_apellido}  ${form.person.segundo_apellido}`
          }}
        </v-card-title>
        <v-divider></v-divider>
        <v-card-item>
          <v-list-item v-for="(item, index) in form.details" :key="index">
            <v-list-item-subtitle>
              {{ item.title }}
            </v-list-item-subtitle>

            <v-list-item-title> </v-list-item-title>
            <v-list-item-subtitle>
              {{ "S/. " + item.price }}
            </v-list-item-subtitle>

            <template v-slot:append>
              <v-chip class="ma-2" size="x-large" label>
                <strong> {{ "S/. " + item.price }}</strong>
              </v-chip>
            </template>
          </v-list-item>

          <v-divider></v-divider>
          <v-list-item>
            <v-list-item-subtitle class="text-end me-4">
              <strong>Total a pagar</strong>
            </v-list-item-subtitle>

            <v-list-item-title> </v-list-item-title>

            <template v-slot:append>
              <v-chip class="ma-2" size="x-large" label>
                <strong> {{ "S/. " + total }}</strong>
              </v-chip>
            </template>
          </v-list-item>
        </v-card-item>
        <v-divider></v-divider>
        <v-card-actions class="">
          <template v-if="hasPrint">
            <v-btn block variant="flat" color="orange" @click="printPDF">
              imprimir {{ payPrint.idpadre }}
            </v-btn>
          </template>
          <template v-else>
            <v-btn variant="flat" color="red"> Cancelar </v-btn>
            <v-spacer></v-spacer>
            <v-btn variant="flat" color="success" @click="savePay">
              Pagar
            </v-btn>
          </template>
        </v-card-actions>
      </v-card>
    </v-col>
  </v-row>
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
</template>
<script setup>
import { computed, ref } from "vue";
import { AdmitionService, PayService } from "../services/";

const admitionService = new AdmitionService();
const payService = new PayService();

const emit = defineEmits(["onSuccess"]);

const formSearch = ref(null);
const search = ref("71576906");

const rules = ref([
  (value) => {
    if (value) return true;
    return "Ingrese el DNI.";
  },
]);

const snakbar = ref({
  show: false,
  title: null,
  text: null,
  type: null,
});

const postulant = ref(null);
const postulantLoading = ref(false);

const conceptItems = [
  {
    value: "0075",
    title: "Derecho de Admisión",
    price: 20.0,
  },

  {
    value: "0219",
    title: "Servicio Medico",
    price: 30.0,
  },
  {
    value: "0075",
    title: "Derecho de Admisión - REZAGADOS",
    price: 80.0,
  },
  {
    value: "0269",
    title: "Duplicado de constancia de inscripcion modificada",
    price: 80.0,
  },

  {
    value: "0269",
    title: "Rezagados (para el cambio de postulacion de programa de estudio.)",
    price: 100.0,
  },

  {
    value: "0269",
    title:
      "Rezagados (al control de identificacion personal y recepcion de documentos, solo para postulantes aptos.)",
    price: 100.0,
  },
];

const form = ref({
  code: "", //back dni
  person: postulant.value,
  details: [{ ...conceptItems[0] }],
});

const total = computed(() =>
  form.value.details.reduce((total, item) => {
    return total + item.price;
  }, 0)
);

const payPrint = ref(null);
const hasPrint = ref(false);

const printPDF = () => {
  let urlPrint =
    "http://una-caja.test/php/pdf_papeleta.php?id=" + payPrint.value.idpadre;
  window.open(urlPrint, "_blank");
};

const savePay = async () => {
  payPrint.value = null;
  let res = await payService.savePay(form.value);

  console.log(res);

  if (res.success) {
    // form.value.person = res.data;
    snakbar.value.show = true;
    snakbar.value.title = "Exito.";
    snakbar.value.text = res.message;
    snakbar.value.type = "green";

    payPrint.value = res.data;

    hasPrint.value = true;
  } else {
    snakbar.value.show = true;
    snakbar.value.title = "Ocurrion un error";
    snakbar.value.text = res.message;
    snakbar.value.type = "red";
  }
};

const searchPostulant = async () => {
  postulantLoading.value = true;
  const { valid } = await formSearch.value.validate();
  if (!valid) return;
  let res = await admitionService.searchPostulant(search.value);
  console.log(res);
  if (res.ok) {
    if (res.status) {
      form.value.person = res.data;
      snakbar.value.show = true;
      snakbar.value.title = "Datos correctos";
      snakbar.value.text = "Postulante encontrado";
      snakbar.value.type = "green";
      //   emit("onSuccess", res.data);
    } else {
      snakbar.value.show = true;
      snakbar.value.title = "Datos incorrectos";
      snakbar.value.text = "Postulante no encontrado";
      snakbar.value.type = "red";
      //show error:
    }
  } else {
    snakbar.value.show = true;
    snakbar.value.title = "Error:";
    snakbar.value.text = "(a)  Error Desconocido";
    snakbar.value.type = "red";
  }
  postulantLoading.value = false;
};
</script>
