<template>
  <v-form class="mt-15">
    <v-container class="pt-4">
      <v-row>
        <v-col cols="12" md="2" class="py-1">
          <div class="text-subtitle-1 text-medium-emphasis">Tipo</div>
          <v-select
          density="compact"
            hide-details
            placeholder="Tipo"
            variant="outlined"
            :items="['Docente', 'Administrativo', 'Estudiante', 'Otro']"
          >

          </v-select>

        </v-col>

        <v-col cols="12" md="3" class="py-1">
          <div class="text-subtitle-1 text-medium-emphasis">
            Estudiante/Docente ó Admin./Otro
          </div>
          <v-text-field
            density="compact"
            placeholder="Buscar"
            hide-details
            variant="outlined"
          ></v-text-field>
        </v-col>

        <v-col cols="12" md="7" class="py-1">
          <div class="text-subtitle-1 text-medium-emphasis">
            Nobres y apellidos
          </div>
          <v-text-field
            density="compact"
            placeholder="Nobres y apellidos"
            hide-details
            variant="outlined"
          ></v-text-field>
        </v-col>

        <v-col cols="12" md="4" class="py-1">
          <div class="text-subtitle-1 text-medium-emphasis">
            Numero
          </div>
          <v-text-field
            density="compact"
            placeholder="Numero"
            hide-details
            variant="outlined"
          ></v-text-field>
        </v-col>

        <v-col cols="12" md="4" class="py-1">
          <div class="text-subtitle-1 text-medium-emphasis">
            Fecha
          </div>
          <v-text-field
            density="compact"
            placeholder="Fecha"
            hide-details
            variant="outlined"
          ></v-text-field>
        </v-col>

        <v-col cols="12" md="4" class="py-1">
          <div class="text-subtitle-1 text-medium-emphasis">
            clave
          </div>
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
          >

          </v-textarea>

        </v-col>
      </v-row>
    </v-container>
  </v-form>
</template>

<script setup></script>

<style scoped>
.logo {
  height: 6em;
  padding: 1.5em;
  will-change: filter;
  transition: filter 300ms;
}
.logo:hover {
  filter: drop-shadow(0 0 2em #646cffaa);
}
.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}
</style>
