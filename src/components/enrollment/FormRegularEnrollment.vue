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
          :rules="searchRules"
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
              class="text-black w-50"
              color="primary"
              label="Cantidad"
              v-model="item.quantity"
              v-if="item.hasQuantity"
              @input="() => (item.price = item.quantity * 15)"
              variant="filled"
            />

            <v-text-field
              class="text-black w-50"
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

    <v-col cols="12" md="8" class="mx-auto">
      <v-card class="border" title="Detalle">
        <v-divider></v-divider>

        <v-list-item v-for="item in form.person.resultDetail">
          <v-list-item-title class="text-h6 font-weight-bold">
            <small>
              {{
                item.name === "RESERVA DE MATRÍCULA (PAGO SEMESTRAL)"
                  ? "Por haber deja de estudiar (RESERVA DE MATRÍCULA)"
                  : item.name
              }}
            </small>
          </v-list-item-title>
          <v-list-item-subtitle class="text-h6">
            Cantiad: {{ Number.parseInt(item.quantity) }} - Costo Unitario: S/.
            {{ Number.parseFloat(item.unitCost).toFixed(2) }}
          </v-list-item-subtitle>
          <template #append>
            <v-chip label variant="flat" color="secondary" size="x-large">
              {{ "S/. " + Number.parseFloat(item.cost).toFixed(2) }}
            </v-chip>
          </template>
        </v-list-item>
      </v-card>
    </v-col>
  </v-row>

  <v-dialog v-model="isBlocked">
    <v-card theme="dark" class="mx-auto" width="600px" rounded="lg">
      <v-container>
        <h3><small> Codido:</small> {{ isBlockedData[0].id }}</h3>
        <h3><small> Nombre:</small> {{ isBlockedData[0].fullname }}</h3>
        <h3><small> Escuela:</small> {{ isBlockedData[0].academyData }}</h3>
        <div class="w-full d-flex py-3">
          <v-chip label class="text-h6 text-uppercase mx-auto" color="red">{{
            isBlockedData[0].status
          }}</v-chip>
        </div>
      </v-container>
    </v-card>
  </v-dialog>

  <v-dialog v-model="isInvictus">
    <v-card class="mx-auto" width="600px" rounded="lg">
      <v-card-title>
        Realice la operacion con el Sistema Anterior
      </v-card-title>
      <v-divider></v-divider>
      <v-container>
        El codigo ingresado no tiene regitrado una deuda, por favor realice la
        operacion con el sistema anterior.
      </v-container>
      <v-container>
        <v-btn block>
          <a
            :href="baseUrl"
            target="blank"
            class="text-decoration-none text-white"
          >
            Arbir sistmea anterior
          </a>
        </v-btn>
      </v-container>
    </v-card>
  </v-dialog>
</template>
<script setup>
import { ref, watch } from "vue";
import { PaymetService, PayService } from "@/services/";
import { useMagicKeys } from "@vueuse/core";

const baseUrl = import.meta.env.VITE_APP_BASE_URL;

const { escape } = useMagicKeys();

const emit = defineEmits(["onSuccess", "showMessage"]);

const payService = new PayService();
const paymetService = new PaymetService();

const formSearch = ref(null);
const inputSearch = ref(null);

const search = ref(null);

const snakbar = ref({
  show: false,
  title: null,
  text: null,
  type: null,
});

//rules input 6 digitos number
const searchRules = ref([
  (v) => !!v || "Ingrese el codigo",
  (v) => (v && v.length === 6) || "El codigo debe tener 6 digitos",
  //solo numeros
  (v) => /^[0-9]*$/.test(v) || "Solo numeros",
]);
const postulantLoading = ref(false);

const conceptItemsDefault = [
  {
    value: "0091",
    title: "Por creditos desaprobados",
    detail: "CREDITOS DESAPROBADOS",
    price: 0.0,
    hasPrint: false,
    payPrint: null,
    isEdit: true,
    quantity: 1,
    hasQuantity: false,
  },

  {
    value: "0091",
    title: "Reserva de matricula",
    detail: "RESERVA DE MATRICULA",
    price: 33.0,
    hasPrint: false,
    payPrint: null,
    quantity: 1,
    hasQuantity: false,
  },
  {
    value: "0225",
    title: "Carné Universitario",
    price: 12.5,
    hasPrint: false,
    payPrint: null,
    quantity: 1,
    hasQuantity: false,
  },

  {
    value: "0091",
    title: "Pago por creditos segunda carrera",
    detail: "CRÉDITOS POR SEGUNDA CARRERA",
    price: 15.0,
    hasPrint: false,
    payPrint: null,
    isEdit: false,
    quantity: 1,
    hasQuantity: true,
  },

  //   {
  //     value: "0225",
  //     title: "Amnistia para continuar estudios",
  //     price: 200.0,
  //     hasPrint: false,
  //     payPrint: null,
  //     isEdit: true,
  //   },
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
  const { valid } = await formSearch.value.validate();
  if (!valid) return;
  postulantLoading.value = true;

  let rest = await paymetService.getDebsStudent(search.value);

  console.log(rest);

  if (rest.success) {
    form.value.person = null;
    form.value.details = null;
    form.value.person = defaultPerson;
    form.value.person = rest.data[0];
    form.value.details = JSON.parse(JSON.stringify(conceptItems.value));
    form.value.details[0].price = rest.data[0].amount;
  } else {
    snakbar.value.show = true;
    snakbar.value.title = "Error:";
    snakbar.value.text = rest.message;
    snakbar.value.type = "red";
    isInvictus.value = true;
    emit("showMessage", snakbar.value);
  }

  postulantLoading.value = false;

  return;

  let res = await payService.getConditionStudent(search.value);

  if (res.ok) {
    if (res.success) {
      form.value.person = null;
      form.value.details = null;
      form.value.person = defaultPerson;
      form.value.person = res.data[0];
      form.value.details = JSON.parse(JSON.stringify(conceptItems.value));
      form.value.details[0].price = res.data[0].amount;
    } else {
      isBlocked.value = true;
      isBlockedData.value = res.data;
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
    isInvictus.value = true;
    emit("showMessage", snakbar.value);
  }
  postulantLoading.value = false;
};

const savePay = async (item, index) => {
  form.value.details[index].loading = true;

  let person = {
    codigo_ingreso: form.value.person.id,
    nombres: form.value.person.firstname,
    primer_apellido: form.value.person.lastname,
    segundo_apellido: form.value.person.mlastname,
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
