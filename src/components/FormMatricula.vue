<template>
  <v-card
    :loading="postulantLoading"
    class="mx-auto mb-5 border"
    style="width: 500px"
  >
    <v-toolbar class="bg-blue-darken-4">
      <v-tabs v-model="typeMat">
        <v-tab value="entrants"> Ingresantes </v-tab>
        <v-tab value="regular"> Regulares </v-tab>
      </v-tabs>
    </v-toolbar>
    <v-window v-model="typeMat">
      <v-window-item value="entrants">
        <v-container>
          <v-form ref="formSearch" @submit.prevent="searchIngresante">
            <div class="text-subtitle-1 mb-1">Buscar / DNI</div>
            <v-text-field
              v-model="search"
              density="compact"
              placeholder="Buscar"
              hide-details="auto"
              variant="outlined"
              :rules="rules"
              counter
              maxlength="8"
              ref="inputSearch"
              :disabled="form.person ? true : false"
            />

            <template v-if="form.person">
              <v-btn
                class="mt-1"
                color="red"
                block
                type="button"
                :loading="postulantLoading"
                @click="restForm()"
              >
                CANCELAR / TERMINATAR <small class="ms-2">[ESC]</small>
              </v-btn>
            </template>

            <template v-else>
              <v-btn
                class="mt-1"
                color="blue"
                block
                type="submit"
                :loading="postulantLoading"
              >
                Buscar postulante <small class="ms-2">[ENTER]</small>
              </v-btn>
            </template>
          </v-form>
        </v-container>
      </v-window-item>
      <v-window-item value="regular">
        <v-container>
          <v-form ref="formSearchRegular" @submit.prevent="searchRegular">
            <div class="text-subtitle-1 mb-1">Buscar / Cod. Matricula</div>
            <v-text-field
              v-model="search"
              density="compact"
              placeholder="Buscar"
              hide-details="auto"
              variant="outlined"
              :rules="rules"
              counter
              maxlength="8"
              ref="inputSearch"
              :disabled="form.person ? true : false"
            />

            <template v-if="form.person">
              <v-btn
                class="mt-1"
                color="red"
                block
                type="button"
                :loading="postulantLoading"
                @click="restForm()"
              >
                CANCELAR / TERMINATAR <small class="ms-2">[ESC]</small>
              </v-btn>
            </template>

            <template v-else>
              <v-btn
                class="mt-1"
                color="blue"
                block
                type="submit"
                :loading="postulantLoading"
              >
                Buscar postulante <small class="ms-2">[ENTER]</small>
              </v-btn>
            </template>
          </v-form>
        </v-container>
      </v-window-item>
    </v-window>
  </v-card>

  <v-row v-if="form.person">
    <v-col cols="12" md="8" class="mx-auto">
      <v-card class="border">
        <v-card-title class="font-weight-bold">
          {{
            `${form.person.nro_documento} |   ${form.person.primer_apellido}  ${form.person.segundo_apellido} , ${form.person.nombres}`
          }}
        </v-card-title>
        <v-divider></v-divider>

        <v-card-item>
          <v-list-item v-for="(item, index) in form.details" :key="index">
            <v-list-item-title class="text-h5">
              {{ item.title }}
            </v-list-item-title>
            <v-list-item-subtitle class="text-h6">
              <v-chip class="ma-2 text-blue-darken-5" size="x-large" label>
                <strong>
                  {{
                    "S/. " + Number.parseFloat(item.price).toFixed(2)
                  }}</strong
                >
              </v-chip>
            </v-list-item-subtitle>
            <template v-slot:append>
              <template v-if="item.hasPrint">
                <v-btn
                  block
                  variant="flat"
                  color="orange"
                  @click="printPDF(item, index)"
                >
                  imprimir
                </v-btn>
              </template>
              <template v-else>
                <v-btn
                  block
                  variant="flat"
                  color="success"
                  :loading="item.loading"
                  @click="savePay(item, index)"
                >
                  Pagar
                </v-btn>
              </template>
            </template>
            <v-divider v-if="index === 0" class="mt-4"></v-divider>
          </v-list-item>
        </v-card-item>
        <v-divider></v-divider>
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
import { ref, watch } from "vue";
import { AdmitionService, PayService } from "../services/";
import { useMagicKeys } from "@vueuse/core";
import { VWindowItem } from "vuetify/lib/components/index.mjs";

const { escape, space, p, i } = useMagicKeys();

const admitionService = new AdmitionService();
const payService = new PayService();

const emit = defineEmits(["onSuccess"]);

const typeMat = ref("entrants");

const formSearch = ref(null);
const inputSearch = ref(null);

const search = ref(null);

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

const conceptItemsDefault = [
  {
    value: "0091",
    title: "Matricula",
    price: 75.0,
    hasPrint: false,
    payPrint: null,
  },
  {
    value: "0225",
    title: "Carné Universitario",
    price: 11.5,
    hasPrint: false,
    payPrint: null,
  },
];

const conceptItemsRegular = [
  {
    value: "0091",
    title: "Matricula",
    price: 75.0,
    hasPrint: false,
    payPrint: null,
  },
  {
    value: "0225",
    title: "Carné Universitario",
    price: 11.5,
    hasPrint: false,
    payPrint: null,
  },
];

const conceptItems = ref(conceptItemsDefault);

const form = ref({
  person: postulant.value,
  details: [],
});

watch(escape, (val) => {
  if (val) {
    restForm();
  }
});

const restForm = () => {
  form.value.person = null;
  form.value.details = [];

  search.value = null;
  inputSearch.value.focus();
};

const urlBase = import.meta.env.VITE_APP_BASE_URL;

const urlPrint = ref(null);

const printPDF = (item) => {
  urlPrint.value = urlBase + "php/pdf_papeleta.php?id=" + item.payPrint.idpadre;
  window.open(urlPrint.value);
};

const searchIngresante = async () => {
  postulantLoading.value = true;

  const { valid } = await formSearch.value.validate();
  if (!valid) {
    snakbar.value.show = true;
    snakbar.value.title = "Error";
    snakbar.value.text = "Ingrese un DNI valido (8 digitos )";
    snakbar.value.type = "red";
    postulantLoading.value = false;
    return;
  }
  let res = await admitionService.getEntrantsPayMat(search.value);

  if (res.ok) {
    if (res.status) {
      form.value.person = null;
      form.value.details = null;
      form.value.person = res.data;

      form.value.details = JSON.parse(JSON.stringify(conceptItems.value));
      // conceptItems.value.forEach((item) => {
      //   form.value.details.push(item);
      // });
    } else {
      snakbar.value.show = true;
      snakbar.value.title = "Datos incorrectos";
      snakbar.value.text = res.message;
      snakbar.value.type = "red";
    }
  } else {
    snakbar.value.show = true;
    snakbar.value.title = "Error:";
    snakbar.value.text = "(a)  Error Desconocido";
    snakbar.value.type = "red";
  }
  postulantLoading.value = false;
};

const searchRegular = async () => {
  postulantLoading.value = true;

  const { valid } = await formSearch.value.validate();
  if (!valid) {
    snakbar.value.show = true;
    snakbar.value.title = "Error";
    snakbar.value.text = "Ingrese un codigo de  matricula valido (6 digitos)";
    snakbar.value.type = "red";
    postulantLoading.value = false;
    return;
  }
  let res = await payService.getRegularStudent(search.value);

  if (res.ok) {
    if (res.status) {
      form.value.person = null;
      form.value.details = null;
      form.value.person = res.data;
      form.value.details = JSON.parse(JSON.stringify(conceptItems.value));
    } else {
      snakbar.value.show = true;
      snakbar.value.title = "Datos incorrectos";
      snakbar.value.text = res.message;
      snakbar.value.type = "red";
    }
  } else {
    snakbar.value.show = true;
    snakbar.value.title = "Error:";
    snakbar.value.text = "(a)  Error Desconocido";
    snakbar.value.type = "red";
  }
  postulantLoading.value = false;
};

const savePay = async (item, index) => {
  form.value.details[index].loading = true;

  let data = {
    details: [item],
    person: form.value.person,
  };

  let res = await payService.savePayMat(data);

  if (res.success) {
    form.value.details[index].hasPrint = true;
    form.value.details[index].payPrint = res.data;

    snakbar.value.show = true;
    snakbar.value.title = "Exito.";
    snakbar.value.text = res.message;
    snakbar.value.type = "green";
    // payPrint.value = res.data;
    //hasPrint.value = true;
  } else {
    snakbar.value.show = true;
    snakbar.value.title = "Ocurrion un error";
    snakbar.value.text = res.message;
    snakbar.value.type = "red";
  }

  form.value.details[index].loading = false;
};
</script>
