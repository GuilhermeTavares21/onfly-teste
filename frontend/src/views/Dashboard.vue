<template>
  <v-app>
    <v-snackbar
      v-model="snackbar.show"
      :color="snackbar.color"
      :timeout="3000"
      location="top right"
      rounded="lg"
    >
      {{ snackbar.text }}
      <template v-slot:actions>
        <v-btn icon="mdi-close" variant="text" @click="snackbar.show = false"></v-btn>
      </template>
    </v-snackbar>

    <v-dialog v-model="dialogConfirm.show" max-width="450" persistent>
      <v-card rounded="lg">
        <v-card-title class="text-h5 font-weight-bold d-flex align-center">
          <v-icon :icon="dialogConfirm.icon" :color="dialogConfirm.iconColor" start></v-icon>
          {{ dialogConfirm.title }}
        </v-card-title>
        <v-card-text>
          {{ dialogConfirm.message }}
        </v-card-text>
        <v-card-actions>
          <v-spacer></v-spacer>
          <v-btn
            variant="text"
            @click="onCancelAction"
          >
            Cancelar
          </v-btn>
          <v-btn
            :color="dialogConfirm.iconColor"
            variant="flat"
            @click="onConfirmAction"
          >
            Confirmar
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

    <v-main class="dashboard-container">
      <v-container fluid class="pa-6 pa-md-8">

        <v-row align="center" justify="space-between" class="mb-6">
          <v-col cols="auto">
            <h1 class="text-h4 font-weight-bold text-grey-darken-3 d-flex align-center">
              <v-icon start size="28" color="primary">mdi-monitor-dashboard</v-icon>
              Painel de Pedidos
            </h1>
          </v-col>
          <v-col cols="auto">
            <v-btn 
              color="primary" 
              rounded="lg" 
              size="large"
              elevation="2"
              prepend-icon="mdi-plus" 
              @click="dialogNovo = true"
            >
              Novo Pedido
            </v-btn>
          </v-col>
        </v-row>

        <v-card class="pa-4 mb-6" variant="outlined" rounded="lg">
          <v-card-title class="text-subtitle-1 font-weight-medium pb-4">
            <v-icon start>mdi-filter-variant</v-icon>
            Filtros
          </v-card-title>
          <v-card-text class="pa-0">
            <v-row dense>
              <v-col cols="12" md="4" v-if="user.is_admin">
                <v-text-field
                  v-model="filters.usuario"
                  label="Filtrar por usuário"
                  variant="outlined"
                  density="compact"
                  clearable
                  hide-details
                />
              </v-col>

              <v-col cols="12" md="4">
                <v-text-field
                  v-model="filters.destino"
                  label="Filtrar por destino"
                  variant="outlined"
                  density="compact"
                  clearable
                  hide-details
                />
              </v-col>

              <v-col cols="12" md="4">
                <v-select
                  v-model="filters.status"
                  :items="statusOptions"
                  label="Filtrar por status"
                  variant="outlined"
                  density="compact"
                  clearable
                  hide-details
                  @update:modelValue="fetchPedidos" 
                />
              </v-col>
            </v-row>
          </v-card-text>
        </v-card>

        <v-card elevation="2" rounded="lg" class="overflow-hidden">
          <v-data-table
            :headers="headers"
            :items="pedidos"
            :loading="loading"
            class="elevation-0"
            no-data-text="Nenhum pedido encontrado"
            items-per-page-text="Itens por página"
          >

            <template v-slot:item.status="{ item }">
              <v-chip :color="statusColor(item.status)" variant="flat" size="small" label>
                {{ item.status }}
              </v-chip>
            </template>

            <template v-slot:item.data_ida="{ item }">
              {{ formatDate(item.data_ida) }}
            </template>

            <template v-slot:item.data_volta="{ item }">
              {{ formatDate(item.data_volta) }}
            </template>

            <template v-slot:item.actions="{ item }">
              <v-progress-circular
                v-if="updatingItemId === item.id"
                indeterminate
                size="24"
                color="primary"
              ></v-progress-circular>
              
              <template v-else>
                <v-menu v-if="user.is_admin" offset-y>
                  <template v-slot:activator="{ props }">
                    <v-btn icon="mdi-dots-vertical" variant="text" v-bind="props"></v-btn>
                  </template>
                  <v-list density="compact">
                    <v-list-item
                      v-for="status in statusOptions"
                      :key="status"
                      @click="promptUpdateStatus(item.id, status)"
                      :disabled="item.status === status"
                    >
                      <v-list-item-title class="d-flex align-center">
                        <v-icon :color="statusColor(status)" start size="small">mdi-circle</v-icon>
                        {{ status }}
                      </v-list-item-title>
                    </v-list-item>
                  </v-list>
                </v-menu>
                <span v-else>—</span>
              </template>
            </template>

          </v-data-table>
        </v-card>

        <v-dialog v-model="dialogNovo" max-width="550" persistent>
          <v-card rounded="lg">
            <v-card-title class="text-h6 font-weight-bold pa-4 bg-primary d-flex align-center">
              <v-icon start>mdi-plus-box</v-icon>
              Criar Novo Pedido
            </v-card-title>
            <v-card-text class="pt-6">
              <v-form @submit.prevent="criarPedido">
                <v-text-field 
                  v-model="novoPedido.destino" 
                  label="Destino" 
                  required 
                  variant="outlined"
                  class="mb-4"
                />
                <v-row>
                  <v-col cols="12" sm="6">
                    <v-text-field
                      v-model="novoPedido.data_ida"
                      label="Data de ida"
                      type="date"
                      required
                      variant="outlined"
                      class="mb-4"
                    />
                  </v-col>
                  <v-col cols="12" sm="6">
                    <v-text-field
                      v-model="novoPedido.data_volta"
                      label="Data de volta"
                      type="date"
                      required
                      variant="outlined"
                      class="mb-4"
                    />
                  </v-col>
                </v-row>
              </v-form>
            </v-card-text>
            <v-card-actions class="pa-4">
              <v-spacer></v-spacer>
              <v-btn variant="text" @click="dialogNovo = false">Cancelar</v-btn>
              <v-btn color="primary" variant="flat" @click="criarPedido">Salvar Pedido</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>

      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import api from '../axios'
import { ref, onMounted, computed, reactive, watch } from 'vue'
import { useUserStore } from '../stores/user'

export default {
  setup() {
    const userStore = useUserStore()
    const user = computed(() => userStore.user)

    const pedidos = ref([])
    const loading = ref(false)
    const dialogNovo = ref(false)
    const filters = ref({ usuario: '', destino: '', status: '' })
    const statusOptions = ['solicitado', 'aprovado', 'cancelado']
    
    const updatingItemId = ref(null)

    const snackbar = reactive({
      show: false,
      text: '',
      color: 'success',
    })

    function showSnackbar(text, color = 'success') {
      snackbar.text = text
      snackbar.color = color
      snackbar.show = true
    }

    const dialogConfirm = reactive({
      show: false,
      title: '',
      message: '',
      icon: 'mdi-help-circle',
      iconColor: 'primary',
      onConfirm: () => {},
    })

    const itemToUpdate = ref({ id: null, status: null })

    function onConfirmAction() {
      if (itemToUpdate.value.id !== null) {
        executeUpdateStatus(itemToUpdate.value.id, itemToUpdate.value.status)
      }
      resetConfirmDialog()
    }

    function onCancelAction() {
      resetConfirmDialog()
    }

    function resetConfirmDialog() {
      dialogConfirm.show = false
      dialogConfirm.title = ''
      dialogConfirm.message = ''
      itemToUpdate.value = { id: null, status: null }
    }

    const novoPedido = ref({
      destino: '',
      data_ida: '',
      data_volta: '',
    })

    const headers = [
      { title: 'ID', key: 'id', align: 'start', width: '80px' },
      { title: 'Solicitante', key: 'nome_solicitante' },
      { title: 'Destino', key: 'destino' },
      { title: 'Data Ida', key: 'data_ida' },
      { title: 'Data Volta', key: 'data_volta' },
      { title: 'Status', key: 'status' },
      { title: 'Ações', key: 'actions', sortable: false, align: 'center' },
    ]

    const debounceTimer = ref(null)

    const fetchPedidos = async () => {
      loading.value = true
      try {
        const params = { ...filters.value }
        Object.keys(params).forEach(key => {
          if (params[key] === null || params[key] === '') {
            delete params[key]
          }
        });
        
        if (!user.value.is_admin) params.somente_meus = true
        const res = await api.get('/pedidos', { params })
        pedidos.value = res.data
      } catch (err)
      {
        console.error(err)
        showSnackbar('Erro ao carregar pedidos', 'error')
      } finally {
        loading.value = false
      }
    }
    watch([() => filters.value.usuario, () => filters.value.destino], () => {
      clearTimeout(debounceTimer.value)
      debounceTimer.value = setTimeout(() => {
        fetchPedidos()
      }, 500)
    })

    const promptUpdateStatus = (id, status) => {
      itemToUpdate.value = { id, status }
      dialogConfirm.title = 'Confirmar Alteração'
      dialogConfirm.message = `Deseja realmente alterar o status para "${status}"?`
      dialogConfirm.icon = 'mdi-alert-circle-outline'
      dialogConfirm.iconColor = statusColor(status)
      dialogConfirm.show = true
    }

    const executeUpdateStatus = async (id, status) => {
      updatingItemId.value = id
      try {
        await api.patch(`/pedidos/${id}/status`, { status })
        fetchPedidos()
        showSnackbar('Status atualizado com sucesso', 'success')
      } catch (err) {
        console.error(err)
        showSnackbar(err.response?.data?.error || 'Erro ao atualizar status', 'error')
      } finally {
        updatingItemId.value = null
      }
    }

    const criarPedido = async () => {
      if (!novoPedido.value.destino || !novoPedido.value.data_ida || !novoPedido.value.data_volta) {
        showSnackbar('Por favor, preencha todos os campos.', 'error')
        return
      }
      try {
        await api.post('/pedidos', novoPedido.value)
        dialogNovo.value = false
        novoPedido.value = { destino: '', data_ida: '', data_volta: '' }
        fetchPedidos()
        showSnackbar('Pedido criado com sucesso!', 'success')
      } catch (err) {
        console.error(err)
        showSnackbar(err.response?.data?.error || 'Erro ao criar pedido', 'error')
      }
    }

    const statusColor = (status) => {
      switch (status) {
        case 'aprovado': return 'green-darken-1'
        case 'cancelado': return 'red-darken-1'
        case 'solicitado': return 'blue-darken-1'
        default: return 'grey'
      }
    }

    const formatDate = (dateString) => {
      if (!dateString) return '—'
      try {
        const [year, month, day] = dateString.split('-')
        return `${day}/${month}/${year}`
      } catch (e) {
        return dateString
      }
    }

    onMounted(fetchPedidos)

    return {
      pedidos,
      loading,
      dialogNovo,
      filters,
      headers,
      statusOptions,
      fetchPedidos,
      promptUpdateStatus,
      criarPedido,
      statusColor,
      formatDate,
      novoPedido,
      user,
      snackbar,
      dialogConfirm,
      onConfirmAction,
      onCancelAction,
      updatingItemId,
    }
  },
}
</script>

<style scoped>
.dashboard-container {
  min-height: 100vh;
  background-color: #f8f9fa;
}

:deep(.v-data-table-header) {
  background-color: #009efb;
}

:deep(.v-data-table-header th .v-data-table-header__content span) {
  color: white;
  font-weight: bold;
}

:deep(.v-data-table-header th .v-data-table-header__content i) {
  color: white !important;
}

.v-card {
  border-radius: 12px;
}

.v-btn {
  text-transform: none;
  letter-spacing: 0.5px;
}
</style>

