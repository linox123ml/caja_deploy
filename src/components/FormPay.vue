<template>
  <v-card class="w-100">
    <v-toolbar>
      <v-toolbar-title>
        Papeleta de Pago :
        <strong>
          {{
            `  ${postulant.nro_doc} | ${postulant.nombres}  ${postulant.primer_apellido}  ${postulant.segundo_apellido}`
          }}
        </strong>
      </v-toolbar-title>
    </v-toolbar>

    <v-container>
      <v-form ref="formPay" @submit.prevent="submit">
        <v-row>
          <v-col cols="12" md="3" class="py-1">
            <div class="text-subtitle-1 text-medium-emphasis">Tipo</div>
            <v-select
              density="compact"
              hide-details
              placeholder="Tipo"
              variant="outlined"
              :items="typeItems"
            >
            </v-select>
          </v-col>

          <v-col cols="12" md="3" class="py-1">
            <div class="text-subtitle-1 text-medium-emphasis">Numero</div>
            <v-text-field
              density="compact"
              placeholder="Numero"
              hide-details
              variant="outlined"
            ></v-text-field>
          </v-col>

          <v-col cols="12" md="3" class="py-1">
            <div class="text-subtitle-1 text-medium-emphasis">Fecha</div>
            <v-text-field
              density="compact"
              placeholder="Fecha"
              hide-details
              variant="outlined"
            ></v-text-field>
          </v-col>

          <v-col cols="12" md="3" class="py-1">
            <div class="text-subtitle-1 text-medium-emphasis">clave</div>
            <v-text-field
              density="compact"
              placeholder="clave"
              hide-details
              variant="outlined"
            ></v-text-field>
          </v-col>

          <v-col cols="12">
            <div class="text-subtitle-1 text-bold-emphasis">Observaciones</div>
            <v-textarea
              density="compact"
              placeholder="Observaciones"
              hide-details
              variant="outlined"
              rows="2"
            >
            </v-textarea>
          </v-col>
          <v-col cols="12">
            <v-card variant="tonal">
              <v-toolbar title="Conceptos de Pago" />
              <v-card-item>
                <v-table>
                  <thead>
                    <tr>
                      <th class="text-left" style="width: 50px">#</th>
                      <th class="text-left">Detalle</th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr v-for="(item, index) in 2" :key="index">
                      <td>{{ index + 1 }}</td>
                      <td>
                        <v-row no-gutters>
                          <v-col cols="8" class="pa-3">
                            <v-select
                              label="Concepto"
                              hide-details
                              density="compact"
                              variant="outlined"
                              :items="conceptItems"
                            />
                          </v-col>
                          <v-col cols="2" class="pa-3">
                            <v-select
                              value="1"
                              hide-details
                              label="Cantidad"
                              density="compact"
                              variant="outlined"
                              :items="[1, 2, 3, 4, 5, 6, 7, 8, 9, 10]"
                            />
                          </v-col>
                          <v-col cols="2" class="pa-3">
                            <v-text-field
                              density="compact"
                              placeholder="Total"
                              readonly
                              hide-details
                              variant="outlined"
                            ></v-text-field>
                          </v-col>
                        </v-row>
                      </td>
                    </tr>
                  </tbody>
                </v-table>
              </v-card-item>
            </v-card>
          </v-col>
        </v-row>
        <v-btn> Guardar </v-btn>
      </v-form>
    </v-container>
  </v-card>
</template>
<script setup>
import { ref } from "vue";

const props = defineProps({
  postulant: Object,
});

const formPay = ref(null);

const typeItems = ref([
  { value: 1, title: "Docente" },
  { value: 1, title: "Administrativo" },
  { value: 1, title: "Estudiante" },
  { value: 1, title: "Otros" },
]);

const conceptItems = ref([
  {
    value: "0075",
    title: "Derecho de Admisión",
    price: 20.0,
  },
  {
    value: "0075",
    title: "Derecho de Admisión - REZAGADOS",
    price: 80.0,
  },

  {
    value: "0219",
    title: "Servicio Medico",
    price: 30.0,
  },

  {
    value: "0075",
    title: "Duplicado de constancia de inscripcion modificada",
    price: 80.0,
  },

  {
    value: "0269",
    title: "Rezagados (para el cambio de postulacion de programa de estudio.)",
  },

  {
    value: "0269",
    title:
      "Rezagados (al control de identificacion personal y recepcion de documentos, solo para postulantes aptos.)",
  },
]);
//"Atención Médica (Revisión Médica Est. Ingresantes, Post. a Ex. Admisión) - Administración",
//"Derechos Examen de Admisión",
//"Otros Derechos de Examen de Admision (duplicado de ficha, carnet, otros)",
const rules = ref([
  (value) => {
    if (value) return true;
    return "Ingrese el DNI.";
  },
]);

const submit = async () => {
  const { valid } = await formSearch.value.validate();
  if (!valid) return;
  console.log("Guardando....");
};
</script>
