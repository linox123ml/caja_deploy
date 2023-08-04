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
            `${form.person.id} | ${form.person.lastname}  ${form.person.mlastname} , ${form.person.firstname}`
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
            <v-text-field
              class="text-black w-25"
              color="primary"
              prefix="S/. "
              v-if="item.isEdit"
              v-model="item.price"
              variant="filled"
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

const { escape } = useMagicKeys();

const emit = defineEmits(["onSuccess"]);

const admitionService = new AdmitionService();
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

const postulant = ref(null);
const postulantLoading = ref(false);

const conceptItemsDefault = [
  {
    value: "0091",
    title: "Por creditos desaprobados",
    price: 0.0,
    hasPrint: false,
    payPrint: null,
    // isEdit: true,
  },

  //   {
  //     value: "0225",
  //     title: "Amnistia para continuar estudios",
  //     price: 200.0,
  //     hasPrint: false,
  //     payPrint: null,
  //     isEdit: true,
  //   },
  {
    value: "0091",
    title: "Reserva de matricula",
    price: 33.0,
    hasPrint: false,
    payPrint: null,
  },
];

const conceptItems = ref(conceptItemsDefault);

const defaultPerson = {
  id: null,
  firstname: null,
  lastname: null,
  mlastname: null,
  academyData: null,
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

  search.value = null;
  inputSearch.value.focus();
};

const urlBase = import.meta.env.VITE_APP_BASE_URL;

const urlPrint = ref(null);

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

const searchStudent = async () => {
  postulantLoading.value = true;

  const { valid } = await formSearch.value.validate();
  if (!valid) {
    snakbar.value.show = true;
    snakbar.value.title = "Error";
    snakbar.value.text = "Ingrese un Codigo valido (6 digitos )";
    snakbar.value.type = "red";
    postulantLoading.value = false;
    return;
  }

  let res = await payService.getConditionStudent(search.value);

  console.log(res);

  if (res.ok) {
    if (res.success) {
      form.value.person = null;
      form.value.details = null;
      form.value.person = defaultPerson;
      form.value.person = res.data[0];
      form.value.details = JSON.parse(JSON.stringify(conceptItems.value));
      form.value.details[0].price = res.data[0].amount;
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
  } else {
    snakbar.value.show = true;
    snakbar.value.title = "Ocurrion un error";
    snakbar.value.text = res.message;
    snakbar.value.type = "red";
  }

  form.value.details[index].loading = false;
};
</script>
