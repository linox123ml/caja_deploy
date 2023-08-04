<template>
  <v-card
    :loading="postulantLoading"
    class="mx-auto mb-5 border"
    style="width: 500px"
  >
    <v-card-item>
      <v-btn block variant="tonal" color="blue" @click="dialogPostulant = true">
        Sin Pre-inscripcion
      </v-btn>
    </v-card-item>
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
          :disabled="form.person ? true : false"
          maxlength="8"
          ref="inputSearch"
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
            CANCELAR / TERMINAR <small class="ms-2">[ESC]</small>
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
    <v-col cols="12" md="6">
      <v-card class="border">
        <v-card-title>Conceptos de pago </v-card-title>
        <v-divider></v-divider>

        <v-list-item
          density="compact"
          v-for="(item, index) in conceptItems"
          :key="index"
          :title="item.title"
        >
          <v-switch
            v-if="item.options"
            v-for="option in item.options"
            :value="option.price"
            density="compact"
            color="black"
            hide-details
            class="px-7 text-black font-weight-bold"
            v-model="item.price"
            :label="option.title"
            :readonly="item.price === option.price"
          ></v-switch>
          <template v-slot:append>
            <v-switch
              :value="item"
              v-model="form.details"
              inset
              @update:modelValue="validateDetails()"
              hide-details
              color="blue"
            ></v-switch>
          </template>
        </v-list-item>
      </v-card>
    </v-col>
    <v-col cols="12" md="6">
      <v-card class="border">
        <v-card-title class="font-weight-bold">
          <small>
            {{ `${form.person.nro_doc} | ${form.person.nombres}` }}
          </small>
        </v-card-title>
        <v-divider></v-divider>

        <v-card-item>
          <v-list-item v-for="(item, index) in form.details" :key="index">
            <v-list-item-title>
              {{ item.title }}
            </v-list-item-title>
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

            <v-list-item-title class="text-end me-4 text-red">
              <strong>{{ detalleError }}</strong>
            </v-list-item-title>

            <template v-slot:append>
              <v-chip
                class="ma-2"
                size="x-large"
                label
                :class="detalleError !== null ? 'text-red' : ''"
              >
                <strong :class="detalleError !== null ? 'text-red' : ''">
                  {{ "S/. " + total }}</strong
                >
              </v-chip>
            </template>
          </v-list-item>
        </v-card-item>
        <v-divider></v-divider>
        <v-card-actions class="">
          <template v-if="hasPrint">
            <v-btn block variant="flat" color="orange" @click="printPDF">
              imprimir <small class="ms-3">[ ESPACIO + I]</small>
            </v-btn>
          </template>
          <template v-else>
            <v-btn block variant="flat" color="success" @click="savePay">
              Pagar <small class="ms-3">[ ESPACIO + P ]</small>
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

  <v-dialog v-model="dialogPostulant" width="500px">
    <v-card title="Registrar nueva persona">
      <v-card-text>
        <v-form ref="personFormRef">
          <v-text-field
            v-model="formPerson.nro_doc"
            density="compact"
            placeholder="DNI"
            hide-details="auto"
            variant="outlined"
            :rules="rulesF"
            counter
            maxlength="8"
            class="my-3"
          />

          <v-text-field
            v-model="formPerson.apellidos"
            density="compact"
            placeholder="Apellidos"
            hide-details="auto"
            variant="outlined"
            :rules="rulesF"
            class="my-3"
          />

          <v-text-field
            v-model="formPerson.nombres"
            density="compact"
            placeholder="Nombres"
            hide-details="auto"
            variant="outlined"
            :rules="rulesF"
            class="my-3"
          />
        </v-form>
      </v-card-text>
      <v-card-actions>
        <v-btn color="red" @click="dialogPostulant = false"> cancelar </v-btn>
        <v-spacer></v-spacer>
        <v-btn color="blue-darken-4" variant="tonal" @click="savePerson" :loading="loadingPay">
          Guardar
        </v-btn>
      </v-card-actions>
    </v-card>
  </v-dialog>

  <iframe id="pdfFrame" style="display: none"></iframe>
</template>
<script setup>
import { computed, ref, watch, watchEffect } from "vue";
import { AdmitionService, PayService } from "@/services/";
import { useMagicKeys } from "@vueuse/core";

const { escape, space, p, i } = useMagicKeys();

const admitionService = new AdmitionService();
const payService = new PayService();

const emit = defineEmits(["onSuccess"]);

const formPerson = ref({
  nro_doc: null,
  apellidos: null,
  nombres: null,
  pagos: [],
});
const dialogPostulant = ref(false);
const personFormRef = ref(null);

const formSearch = ref(null);
const inputSearch = ref(null);
const search = ref(null);
const rules = ref([
  (value) => {
    if (value) return true;
    return "Ingrese el DNI.";
  },
]);

const rulesF = ref([
  (value) => {
    if (value) return true;
    return "Obligatorio.";
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

const conceptItems = ref([
  {
    value: "0075",
    codeBN: 26,
    title: "Derecho de Admisión",
    price: 200.0,
    options: [
      {
        title: "Colegio Estatal",
        price: 200.0,
      },
      {
        title: "Colegio Particular",
        price: 350.0,
      },
      {
        title: "Colegio Extranjero",
        price: 450.0,
      },
    ],
  },
  {
    value: "0219",
    codeBN: 39,
    title: "Servicio Medico",
    price: 30.0,
  },
  {
    value: "0269",
    codeBN: 25,
    title: "Duplicado de constancia de inscripcion modificada",
    price: 30.0,
  },

  // {
  //   value: "0075",
  //   title: "Derecho de Admisión - REZAGADOS",
  //   price: 80.0,
  // },

  // {
  //   value: "0269",
  //   title: "Rezagados (para el cambio de postulacion de programa de estudio.)",
  //   price: 100.0,
  // },

  // {
  //   value: "0269",
  //   title:
  //     "Rezagados (al control de identificacion personal y recepcion de documentos, solo para postulantes aptos.)",
  //   price: 100.0,
  // },
]);

const form = ref({
  code: "",
  person: postulant.value,
  details: [],
});

const total = computed(() =>
  form.value.details.reduce((total, item) => {
    return total + item.price;
  }, 0)
);

watch(escape, (val) => {
  if (val) {
    restForm();
  }
});

watchEffect(async () => {
  if (space.value && p.value && form.value.person) {
    await savePay();
  }

  if (space.value && i.value && hasPrint.value) {
    printPDF();
  }
});

const restForm = () => {
  form.value.code = null;
  form.value.person = null;
  form.value.details = [];
  formPerson.value.nro_doc = null;
  formPerson.value.apellidos = null;
  formPerson.value.nombres = null;
  formPerson.value.pagos = [];
  payPrint.value = null;
  hasPrint.value = false;
  search.value = null;
  inputSearch.value.focus();
};

const payPrint = ref(null);
const hasPrint = ref(false);

const urlBase = import.meta.env.VITE_APP_BASE_URL;
const urlPrint = ref(null);

const printPDF = () => {
  urlPrint.value =
    urlBase + "php/pdf_papeleta.php?id=" + payPrint.value.idpadre;

  let pdfUrl = urlPrint.value; // Replace this with your PDF URL
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

const detalleError = ref(null);

const validateDetails = async () => {
  if (form.value.details.length < 1) {
    detalleError.value = "Seleccion al menos un concepto.";
    return false;
  } else {
    detalleError.value = null;
    return true;
  }
};

const savePerson = async () => {
  const { valid } = await personFormRef.value.validate();

  if (!valid) {
    snakbar.value.show = true;
    snakbar.value.title = "Error";
    snakbar.value.text = "Todos los campos son obligatorios";
    snakbar.value.type = "red";

    return;
  }

  form.value.person = null;

  form.value.person.nro_doc = formPerson.value.nro_doc;
  form.value.person.nombres =
    formPerson.value.apellidos + " " + formPerson.value.nombres;
  form.value.details = [];

  dialogPostulant.value = false;
};

const searchOtherPerson = async (term) => {
  let res = await payService.getOtherPerson(term);
  if (res.success) {
    form.value.person = { nro_doc: '', nombres: '' };
    form.value.details = [];
    let person = res.data;
    form.value.person.nro_doc = person.codigo;
    form.value.person.nombres = person.nombre;
  } else {
    snakbar.value.show = true;
    snakbar.value.title = res.message;
    snakbar.value.text =
      "Postulante no PRE-INSCRITO ... Buscando en OTRAS PERSONAS";
    snakbar.value.type = "warning";
  }
};

const searchPostulant = async () => {
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

  let res = await admitionService.searchPostulant(search.value);

  if (res.ok) {
    if (res.status) {
      form.value.person = { nro_doc: null, nombres: null };
      form.value.details = [];
      form.value.person.nro_doc = res.data?.nro_doc;
      form.value.person.nombres =
        res.data.primer_apellido +
        " " +
        res.data.segundo_apellido +
        " " +
        res.data.nombres;

      res.data.pagos.forEach((item) => {
        let pago = conceptItems.value.find(
          (element) => item.cod === element.codeBN
        );
        if (pago) {
   
          form.value.details.push(pago);
        }
      });
      postulantLoading.value = false;
      return;
    } else {
      snakbar.value.show = true;
      snakbar.value.title = res.message;
      snakbar.value.text =
        "Postulante no PRE-INSCRITO ... Buscando en OTRAS PERSONAS";
      snakbar.value.type = "warning";
    }
  } else {
    snakbar.value.show = true;
    snakbar.value.title = "(a) Error Desconocido:";
    snakbar.value.text = " ... Buscando en OTRAS PERSONAS";
    snakbar.value.type = "red";
  }

  await searchOtherPerson(search.value);

  postulantLoading.value = false;
};


const loadingPay =  ref(false);

const savePay = async () => {
  payPrint.value = null;

  loadingPay.value = true;

  let validDetails = await validateDetails();
  if (!validDetails) {
    snakbar.value.show = true;
    snakbar.value.title = "Error";
    snakbar.value.text = "Seleccion al menos un concepto de pago";
    snakbar.value.type = "red";
    return;
  }

  let res = await payService.savePay(form.value);

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

  loadingPay.value = false;

};
</script>
