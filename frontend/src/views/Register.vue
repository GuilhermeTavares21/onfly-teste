<template>
  <v-container class="register-container">
    <v-row class="fill-height d-flex align-center justify-center">
      <v-col cols="12" md="8" lg="7">
        <v-card class="register-card" elevation="8">
          <v-row no-gutters>
            <v-col cols="12" md="6" class="form-section d-flex flex-column justify-center pa-8">
              <h2 class="register-title">Crie sua conta 🚀</h2>
              <p class="register-subtitle mb-6">
                Preencha os campos abaixo para começar a usar o sistema.
              </p>

              <v-text-field
                label="Nome"
                v-model="name"
                variant="outlined"
                color="primary"
                density="comfortable"
                class="mb-4"
              ></v-text-field>

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
                class="mb-4"
              ></v-text-field>

              <v-text-field
                label="Confirmar Senha"
                v-model="passwordConfirmation"
                type="password"
                variant="outlined"
                color="primary"
                density="comfortable"
                class="mb-4"
              ></v-text-field>

              <v-checkbox
                v-model="isAdmin"
                color="primary"
                label="Será administrador?"
                class="mb-6"
              ></v-checkbox>

              <v-btn
                color="primary"
                block
                size="large"
                rounded
                @click="register"
              >
                Registrar
                <v-icon class="ml-2" right size="18">mdi-arrow-right-circle</v-icon>
              </v-btn>

              <div class="mt-4 text-center">
                <small class="text-secondary">
                  Já tem uma conta?
                  <span class="link" @click="router.push('/login')">Fazer login</span>
                </small>
              </div>
            </v-col>

            <v-col cols="12" md="6" class="image-section d-none d-md-flex">
              <v-img
                src="/register.png"
                alt="Imagem de registro"
                contain
                class="register-image"
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

    <PlaneLoading v-model="loadingRegister" text="Criando sua conta..." />
  </v-container>
</template>

<script setup>
import api from '../axios'
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useUserStore } from '../stores/user'
import PlaneLoading from '../components/PlaneLoading.vue'

const router = useRouter()
const userStore = useUserStore()

const name = ref('')
const email = ref('')
const password = ref('')
const passwordConfirmation = ref('')
const isAdmin = ref(false)

const loadingRegister = ref(false)

const snackbar = reactive({
  show: false,
  message: '',
  color: 'red',
})

async function register() {
  loadingRegister.value = true
  try {
    if (password.value !== passwordConfirmation.value) {
      snackbar.message = 'As senhas não são iguais.'
      snackbar.color = 'red'
      snackbar.show = true
      return
    }

    const payload = {
      name: name.value,
      email: email.value,
      password: password.value,
      password_confirmation: passwordConfirmation.value,
      is_admin: isAdmin.value,
    }

    const response = await api.post('/register', payload)
    userStore.setUser(response.data.user, response.data.token)
    router.push('/dashboard')
  } catch (err) {
    snackbar.message = err.response?.data?.message || err.message
    snackbar.color = 'red'
    snackbar.show = true
  } finally {
    loadingRegister.value = false
  }
}
</script>

<style scoped>
.register-container {
  height: 85vh;
  min-width: 1400px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.register-card {
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
  overflow: hidden;
  position: relative;
}

.register-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  object-position: center;
}

.register-title {
  font-weight: 700;
  color: #007bff;
  font-size: 1.8rem;
}

.register-subtitle {
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
