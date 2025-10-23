<template>
  <v-container class="login-container">
    <v-row justify="center" align="center" class="fill-height">
      <v-col cols="12" md="4">
        <v-card>
          <v-card-title>Login</v-card-title>
          <v-card-text>
            <v-text-field label="Email" v-model="email"></v-text-field>
            <v-text-field label="Senha" v-model="password" type="password"></v-text-field>
          </v-card-text>
          <v-card-actions>
            <v-btn color="primary" @click="login">Entrar</v-btn>
          </v-card-actions>
        </v-card>
      </v-col>
    </v-row>

    <!-- Snackbar de erro -->
    <v-snackbar v-model="snackbar.show" :color="snackbar.color" top right>
      {{ snackbar.message }}
      <template #actions>
        <v-btn text @click="snackbar.show = false">Fechar</v-btn>
      </template>
    </v-snackbar>
  </v-container>
</template>

<script setup>
import api from '../axios';
import { ref, reactive } from 'vue';
import axios from 'axios';
import { useUserStore } from '../stores/user';
import { useRouter } from 'vue-router';

const email = ref('');
const password = ref('');
const userStore = useUserStore();
const router = useRouter();

const snackbar = reactive({
  show: false,
  message: '',
  color: 'red',
});

async function login() {
  try {
    const response = await api.post('/login', { 
      email: email.value, 
      password: password.value 
    });

    userStore.setUser(response.data.user, response.data.token);

    router.push('/dashboard');
  } catch (err) {
    snackbar.message = 'Erro ao logar: ' + (err.response?.data?.message || err.message);
    snackbar.show = true;
  }
}

</script>

<style scoped>
.login-container {
  height: 80vh;
}
</style>
