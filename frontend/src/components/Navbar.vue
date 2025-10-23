<template>
  <v-app-bar app flat color="white" class="navbar">
    <v-container class="d-flex align-center justify-space-between">
      <div class="d-flex align-center logo">
        <span class="logo-text">onfly</span>
        <v-icon color="primary" size="20" class="ml-1">mdi-earth</v-icon>

        <v-btn
          v-if="userStore.isLoggedIn"
          variant="text"
          prepend-icon="mdi-chart-box-outline"
          class="ml-12 dashboard-link"
          @click="router.push('/dashboard')"
        >
          Dashboard
        </v-btn>
      </div>

      <div class="d-flex align-center">
        <template v-if="userStore.isLoggedIn">
          <span class="mr-4 user-greeting">Olá, {{ userStore.user.name }}</span>
          <v-btn variant="text" color="primary" rounded="lg" @click="logout">
            <v-icon start size="18">mdi-logout</v-icon>
            Logout
          </v-btn>
        </template>

        <template v-else>
          <v-menu open-on-hover offset-y>
            <template v-slot:activator="{ props }">
              <v-btn 
                color="primary" 
                rounded="lg" 
                v-bind="props" 
                append-icon="mdi-chevron-down"
              >
                <v-icon start size="18">mdi-account</v-icon>
                {{ authButtonText }}
              </v-btn>
            </template>
            
            <v-list density="compact" elevation="2">
              <v-list-item 
                to="/login" 
                :active="route.path === '/login'"
              >
                <v-list-item-title>Login</v-list-item-title>
              </v-list-item>
              <v-list-item 
                to="/register" 
                :active="route.path === '/register'"
              >
                <v-list-item-title>Registrar</v-list-item-title>
              </v-list-item>
            </v-list>
          </v-menu>
        </template>
      </div>
    </v-container>
  </v-app-bar>
</template>

<script setup>
import { computed } from 'vue'
import { useUserStore } from '../stores/user'
import { useRouter, useRoute } from 'vue-router'

const userStore = useUserStore()
const router = useRouter()
const route = useRoute()

const authButtonText = computed(() => {
  return route.path === '/register' ? 'Registrar' : 'Login'
})

const logout = () => {
  userStore.logout()
  router.push('/login')
}
</script>

<style scoped>
.navbar {
  box-shadow: none;
  border-bottom: 1px solid #eee;
}

.logo-text {
  font-weight: 700;
  color: #007bff;
  font-size: 1.4rem;
}

.menu-links .menu-link {
  color: #333;
  text-transform: none;
  font-weight: 500;
  font-size: 0.95rem;
}

.menu-links .menu-link:hover {
  color: #007bff;
  background-color: transparent;
}

.v-btn {
  text-transform: none;
}

.v-btn--rounded {
  font-weight: 600;
}

.user-greeting {
  color: #333;
  font-weight: 500;
}

.dashboard-link {
  font-weight: 500;
  color: #333;
}

.dashboard-link:hover {
  color: #007bff;
}
</style>
