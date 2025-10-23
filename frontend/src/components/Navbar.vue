<template>
  <v-app-bar app color="primary" dark>
    <v-toolbar-title>Onfly</v-toolbar-title>
    <v-spacer></v-spacer>
    <div v-if="userStore.isLoggedIn">
      <span class="mr-4">Olá, {{ userStore.user.name }}</span>
      <v-btn text @click="logout">Logout</v-btn>
    </div>
    <div v-else>
      <v-btn text @click="$router.push('/login')">Login</v-btn>
    </div>
  </v-app-bar>
</template>

<script setup>
import { useUserStore } from '../stores/user';
import { useRouter } from 'vue-router';

const userStore = useUserStore();
const router = useRouter();

const logout = () => {
  userStore.logout();
  router.push('/login');
}
</script>

<style scoped>
.v-toolbar-title {
  font-weight: bold;
}
</style>
