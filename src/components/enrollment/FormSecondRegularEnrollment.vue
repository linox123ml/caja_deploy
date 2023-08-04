<template>
  <v-card
    :loading="postulantLoading"
    class="mx-auto mb-5 border"
    style="max-width: 600px"
  >
    <v-container>
      <v-form ref="formSearch" @submit.prevent="searchStudent">
        <div class="text-subtitle-1 mb-1">Ingrese codigo de matricula</div>
        <v-text-field
          v-model="search"
          density="compact"
          placeholder="Buscar"
          hide-details="auto"
          variant="outlined"
          :rules="rules"
          counter
          maxlength="6"
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
  </v-card>

  <v-row v-if="form.person">
    <v-col cols="12" md="8" class="mx-auto">
      <v-card class="border">
        <v-card-title class="font-weight-bold bg-grey">
          {{
            `${form.person.codigo_matricula} | ${form.person.paterno}  ${form.person.materno} , ${form.person.nombre}`
          }}
        </v-card-title>
        <v-divider></v-divider>

        <v-list-item
          v-for="(item, index) in form.details"
          :key="index"
          class="border"
        >
          <v-list-item-title class="text-h6 font-weight-bold">
            {{ item.title }}
          </v-list-item-title>
          <v-list-item-subtitle class="text-h6">
            <v-switch
              v-if="item.options"
              v-for="option in item.options"
              :value="option.price"
              density="compact"
              color="primary"
              hide-details
              class="px-7 text-black font-weight-bold"
              v-model="item.price"
              :label="option.title"
              :readonly="item.price === option.price"
            ></v-switch>

            <v-text-field
              label="Ingrese el monto"
              class="text-black w-25 my-3"
              color="primary"
              prefix="S/. "
              v-if="item.isEdit"
              v-model="item.price"
            />

            <v-chip
              v-else
              class="ma-2 text-blue-darken-5 py-2"
              size="x-large"
              label
            >
              <strong>
                {{ "S/. " + Number.parseFloat(item.price).toFixed(2) }}
              </strong>
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
                :disabled="item.price === 0"
                @click="savePay(item, index)"

              >
                Pagar
              </v-btn>
            </template>
          </template>
        </v-list-item>
      </v-card>
    </v-col>
  </v-row>
</template>
<script setup>
import { ref, watch } from "vue";
import { AdmitionService, PayService } from "@/services/";
import { useMagicKeys } from "@vueuse/core";

const baseUrl = import.meta.env.VITE_APP_BASE_URL;

const { escape } = useMagicKeys();

const emit = defineEmits(["onSuccess", "showMessage"]);

const payService = new PayService();

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

const postulantLoading = ref(false);

const conceptItemsDefault = [
  {
    value: "0091",
    title: "Pago por creditos",
    detail: "CRÉDITOS POR SEGUNDA CARRERA",
    price: 0.0,
    hasPrint: false,
    payPrint: null,
    isEdit: true,
  },

  {
    value: "0091",
    codeBN: 26,
    title: "Matriculas SEGUNDA CARRERA",
    detail: "Matriculas SEGUNDA CARRERA",
    price: 50.0,
    options: [
      {
        title: "Colegio Estatal",
        price: 50.0,
      },
      {
        title: "Colegio Particular",
        price: 100.0,
      },
    ],
  },
];

const conceptItems = ref(conceptItemsDefault);

const defaultPerson = {
  codigo_matricula: null,
  nombre: null,
  materno: null,
  paterno: null,
};

const form = ref({
  person: null,
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
  isBlocked.value = false;

  search.value = null;
  inputSearch.value.focus();
};

const urlBase = import.meta.env.VITE_APP_BASE_URL;

const urlPrint = ref(null);

const isBlocked = ref(false);
const isBlockedData = ref(null);

const printPDF = (item) => {
  urlPrint.value = urlBase + "php/pdf_papeleta.php?id=" + item.payPrint.idpadre;
  let pdfUrl = urlPrint.value;
  let iframe = document.getElementById("pdfFrame");

  iframe.src = pdfUrl;
  iframe.onload = function () {
    if (iframe.contentWindow) {
      iframe.contentWindow.print();
    } else {
      iframe.contentDocument.print();
    }
  };
};

const isInvictus = ref(false);

const searchStudent = async () => {
  postulantLoading.value = true;
  isBlocked.value = false;
  isBlockedData.value = null;

  const { valid } = await formSearch.value.validate();
  if (!valid) {
    snakbar.value.show = true;
    snakbar.value.title = "Error";
    snakbar.value.text = "Ingrese un Codigo valido (6 digitos )";
    snakbar.value.type = "red";
    postulantLoading.value = false;

    emit("showMessage", snakbar.value);
    return;
  }

  let res = await payService.getRegularStudent(search.value);

  console.log(res.data);

  if (res.ok) {
    if (res.success) {
      form.value.person = null;
      form.value.details = null;
      form.value.person = defaultPerson;
      form.value.person = res.data;
      form.value.details = JSON.parse(JSON.stringify(conceptItems.value));
    } else {
      snakbar.value.show = true;
      snakbar.value.title = "Datos incorrectos";
      snakbar.value.text = res.message;
      snakbar.value.type = "red";
      emit("showMessage", snakbar.value);
    }
  } else {
    snakbar.value.show = true;
    snakbar.value.title = "Error:";
    snakbar.value.text = res.message;
    snakbar.value.type = "red";
    emit("showMessage", snakbar.value);
  }
  postulantLoading.value = false;
};

const savePay = async (item, index) => {
  form.value.details[index].loading = true;

  let person = {
    codigo_ingreso: form.value.person.codigo_matricula,
    nombres: form.value.person.nombre,
    primer_apellido: form.value.person.paterno,
    segundo_apellido: form.value.person.materno,
  };

  let data = {
    details: [item],
    person: person,
  };

  let res = await payService.savePayMat(data);

  if (res.success) {
    form.value.details[index].hasPrint = true;
    form.value.details[index].payPrint = res.data;
    snakbar.value.show = true;
    snakbar.value.title = "Exito.";
    snakbar.value.text = res.message;
    snakbar.value.type = "green";
    emit("showMessage", snakbar.value);
  } else {
    snakbar.value.show = true;
    snakbar.value.title = "Ocurrion un error";
    snakbar.value.text = res.message;
    snakbar.value.type = "red";
    emit("showMessage", snakbar.value);
  }

  form.value.details[index].loading = false;
};
</script>
