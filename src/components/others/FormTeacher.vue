<template>
    <v-form>
      <v-row>
        <v-col cols="12" md="4">
          <v-text-field
            class="pt-2"
            label="Buscar"
            prepend-inner-icon="mdi-magnify"
          />
        </v-col>
        <v-col cols="12" md="8">
          <v-text-field class="pt-2" readonly label="Apellidos y nombre" />
        </v-col>
        <v-col cols="12">
          <v-textarea class="pt-2" rows="2" label="Observaciones" />
        </v-col>
        <v-col cols="12">
          <v-card theme="dark" variant="tonal">
            <v-card-title>
              <v-text-field
                class="py-2"
                label="Buscar conceptos"
                prepend-inner-icon="mdi-magnify"
              />
            </v-card-title>
          </v-card>
          <v-row>
            <v-col cols="12">
              <v-table>
                <thead>
                  <th class="text-uppercase py-2 bg-black border">#</th>
                  <th class="text-uppercase py-2 bg-black border">Codigo</th>
                  <th class="text-uppercase py-2 bg-black border">
                    Denominacion
                  </th>
                  <th class="text-uppercase py-2 bg-black border">cantidad</th>
                  <th class="text-uppercase py-2 bg-black border">Precio</th>
                  <th class="text-uppercase py-2 bg-black border">Total</th>
                </thead>
                <tbody>
                  <tr>
                    <td>1</td>
                    <td>2103.99.04</td>
                    <td>
                      Retenciones UNA - Mas Vida (seguro de AccidentesEssalud)
                    </td>
                    <td>2</td>
                    <td>30.33</td>
                    <td>60.66</td>
                  </tr>
                </tbody>
              </v-table>
            </v-col>
          </v-row>
        </v-col>
      </v-row>
    </v-form>
  </template>
  
