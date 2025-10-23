<template>
  <v-app>
    <v-main class="dashboard-container">
      <v-container class="py-4">
        <v-row class="mb-4" align="center">
          <v-col cols="12" md="6">
            <v-text-field
              v-model="filters.destino"
              label="Filtrar por destino"
              clearable
              @input="fetchPedidos"
            />
          </v-col>
          <v-col cols="12" md="6">
            <v-select
              v-model="filters.status"
              :items="statusOptions"
              label="Filtrar por status"
              clearable
              @change="fetchPedidos"
            />
          </v-col>
        </v-row>

        <v-data-table
          :headers="headers"
          :items="pedidos"
          :loading="loading"
          class="elevation-1"
        >
          <template v-slot:item.status="{ item }">
            <v-chip
              :color="statusColor(item.status)"
              dark
              small
            >
              {{ item.status }}
            </v-chip>
          </template>

          <template v-slot:item.actions="{ item }">
            <v-menu v-if="user.is_admin" offset-y>
              <template v-slot:activator="{ props }">
                <v-btn icon v-bind="props">
                  <v-icon>mdi-dots-vertical</v-icon>
                </v-btn>
              </template>
              <v-list>
                <v-list-item
                  v-for="status in statusOptions"
                  :key="status"
                  @click="updateStatus(item.id, status)"
                >
                  <v-list-item-title>{{ status }}</v-list-item-title>
                </v-list-item>
              </v-list>
            </v-menu>
          </template>
        </v-data-table>
      </v-container>
    </v-main>
  </v-app>
</template>

<script>
import api from '../axios'
import { ref, onMounted } from 'vue'
import { computed } from 'vue'
import { useUserStore } from '../stores/user'

export default {
  setup() {
    const userStore = useUserStore()
    const user = computed(() => userStore.user)
    const pedidos = ref([])
    const loading = ref(false)
    const filters = ref({ destino: '', status: '' })
    const statusOptions = ['solicitado', 'aprovado', 'cancelado']

    const headers = [
      { text: 'ID', value: 'id' },
      { text: 'Solicitante', value: 'nome_solicitante' },
      { text: 'Destino', value: 'destino' },
      { text: 'Data Ida', value: 'data_ida' },
      { text: 'Data Volta', value: 'data_volta' },
      { text: 'Status', value: 'status' },
      { text: 'Ações', value: 'actions', sortable: false },
    ]

    const fetchPedidos = async () => {
      loading.value = true
      try {
        const res = await api.get('/pedidos', { params: filters.value })
        pedidos.value = res.data
      } catch (err) {
        console.error(err)
        alert('Erro ao carregar pedidos')
      } finally {
        loading.value = false
      }
    }

    const updateStatus = async (id, status) => {
      if (!confirm(`Deseja alterar o status para "${status}"?`)) return
      try {
        await api.patch(`/pedidos/${id}/status`, { status })
        fetchPedidos()
        alert('Status atualizado com sucesso')
      } catch (err) {
        console.error(err)
        alert(err.response?.data?.error || 'Erro ao atualizar status')
      }
    }

    const statusColor = (status) => {
      switch (status) {
        case 'aprovado': return 'green'
        case 'cancelado': return 'red'
        default: return 'blue'
      }
    }

    onMounted(fetchPedidos)

    return {
      pedidos,
      loading,
      filters,
      headers,
      statusOptions,
      fetchPedidos,
      updateStatus,
      statusColor,
      user,
    }
  },
}
</script>

<style scoped>
.v-data-table thead th {
  background-color: #009efb;
  color: white;
}
.dashboard-container {
  height: 20%;
}
</style>
