<template>
  <v-toolbar >
    <v-tabs v-model="tabs">
      <v-tab value="student">Estudiantes</v-tab>
      <v-tab value="teacher">Docentes</v-tab>
      <v-tab value="admin">Administrativos</v-tab>
      <v-tab value="other">otros</v-tab>
    </v-tabs>
  </v-toolbar>

  <v-container>
    <v-window v-model="tabs">
      <v-window-item value="student"> 

        <FormStudent ></FormStudent>
      </v-window-item>
      <v-window-item value="teacher">
        <FormTeacher></FormTeacher>
      </v-window-item>
      <v-window-item value="admin">
        <FormAdmin></FormAdmin>
      </v-window-item>
      <v-window-item value="other"> 
        <FormOther></FormOther>
      </v-window-item>
    </v-window>
  </v-container>
</template>

<script setup>
import { ref } from "vue";
import FormStudent from './FormStudent.vue'
import FormTeacher from './FormTeacher.vue'
import FormAdmin from './FormAdmin.vue'
import FormOther from './FormOther.vue'

const tabs = ref("student");
</script>
