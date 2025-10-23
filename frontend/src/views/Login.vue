<template>
  <v-container class="login-container">
    <v-row class="fill-height d-flex align-center justify-center">
      <v-col cols="12" md="8" lg="7">
        <v-card class="login-card" elevation="8">
          <v-row no-gutters>
            <v-col cols="12" md="6" class="form-section d-flex flex-column justify-center pa-8">
              <h2 class="login-title">Bem-vindo 👋</h2>
              <p class="login-subtitle mb-6">
                Faça login para acessar o seu painel e continuar de onde parou.
              </p>

              <v-text-field
                label="Email"
                v-model="email"
                variant="outlined"
                color="primary"
                density="comfortable"
                class="mb-4"
              ></v-text-field>

              <v-text-field
                label="Senha"
                v-model="password"
                type="password"
                variant="outlined"
                color="primary"
                density="comfortable"
                class="mb-6"
              ></v-text-field>

              <v-btn
                color="primary"
                block
                size="large"
                rounded
                @click="login"
              >
                Entrar
                <v-icon class="ml-2" right size="18">mdi-arrow-right-circle</v-icon>
              </v-btn>

              <div class="mt-4 text-center">
                <small class="text-secondary">
                  Ainda não tem conta?
                  <span class="link" @click="router.push('/register')">Cadastre-se</span>
                </small>
              </div>
            </v-col>

            <v-col cols="12" md="6" class="image-section d-none d-md-flex">
              <v-img
                src="/login.webp"
                alt="Imagem do login"
                contain
                class="login-image"
              ></v-img>
            </v-col>
          </v-row>
        </v-card>
      </v-col>
    </v-row>

    <v-snackbar v-model="snackbar.show" :color="snackbar.color" top right>
      {{ snackbar.message }}
      <template #actions>
        <v-btn text @click="snackbar.show = false">Fechar</v-btn>
      </template>
    </v-snackbar>
    <PlaneLoading v-model="loadingLogin" text="Autenticando..." />
  </v-container>
</template>

<script setup>
import PlaneLoading from '../components/PlaneLoading.vue'
import api from '../axios'
import { ref, reactive } from 'vue'
import { useUserStore } from '../stores/user'
import { useRouter } from 'vue-router'

const email = ref('')
const password = ref('')
const userStore = useUserStore()
const router = useRouter()

const loadingLogin = ref(false)

const snackbar = reactive({
  show: false,
  message: '',
  color: 'red',
})

async function login() {
  if (!email.value || !password.value) {
    snackbar.message = 'Preencha email e senha.'
    snackbar.show = true
    return
  }

  loadingLogin.value = true
  try {
    const response = await api.post('/login', { 
      email: email.value, 
      password: password.value 
    })

    userStore.setUser(response.data.user, response.data.token)
    router.push('/dashboard')
  } catch (err) {
    snackbar.message = (err.response?.data?.message || err.message)
    snackbar.show = true
  } finally {
    loadingLogin.value = false
  }
}
</script>

<style scoped>
.login-container {
  height: 85vh;
  display: flex;
  align-items: center;
  justify-content: center;
}

.login-card {
  border-radius: 16px;
  overflow: hidden;
}

.form-section {
  background-color: #fff;
}

.image-section {
  background-color: #f9fbff;
  align-items: center;
  justify-content: center;
}

.login-image {
  max-width: 90%;
  margin: auto;
}

.login-title {
  font-weight: 700;
  color: #007bff;
  font-size: 1.8rem;
}

.login-subtitle {
  color: #666;
  font-size: 0.95rem;
}

.v-text-field {
  font-size: 0.95rem;
}

.v-btn {
  text-transform: none;
  font-weight: 600;
  letter-spacing: 0.3px;
}

.link {
  color: #009efb;
  cursor: pointer;
  font-weight: 500;
}

.link:hover {
  text-decoration: underline;
}
</style>
