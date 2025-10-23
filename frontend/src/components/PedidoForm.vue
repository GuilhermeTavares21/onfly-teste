<template>
  <v-form @submit.prevent="submit">
    <v-text-field label="Destino" v-model="destino" required></v-text-field>
    <v-text-field label="Data Ida" v-model="data_ida" type="date" required></v-text-field>
    <v-text-field label="Data Volta" v-model="data_volta" type="date" required></v-text-field>
    <v-btn type="submit" color="primary">Criar Pedido</v-btn>
  </v-form>
</template>

<script setup>
import { ref } from 'vue';
import axios from 'axios';
import { useUserStore } from '../stores/user';

const destino = ref('');
const data_ida = ref('');
const data_volta = ref('');
const userStore = useUserStore();

async function submit() {
  try {
    await axios.post('http://localhost:8000/api/pedidos', {
      destino: destino.value,
      data_ida: data_ida.value,
      data_volta: data_volta.value,
    }, { withCredentials: true });

    destino.value = '';
    data_ida.value = '';
    data_volta.value = '';
    alert('Pedido criado com sucesso!');
    emit('pedido-criado');
  } catch (err) {
    alert('Erro ao criar pedido: ' + err.response?.data?.error || err.message);
  }
}
</script>
