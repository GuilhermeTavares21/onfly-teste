<template>
  <v-overlay
    :model-value="modelValue"
    class="custom-loading-overlay"
    persistent
    contained
  >
    <div class="plane-loader">
      <v-icon size="64" color="primary" class="plane-icon">mdi-airplane</v-icon>
    </div>
    <p class="loading-text">{{ text }}</p>
  </v-overlay>
</template>

<script setup>
defineProps({
  modelValue: { type: Boolean, required: true },
  text: { type: String, default: 'Carregando...' },
})
</script>

<style scoped>
.custom-loading-overlay {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  backdrop-filter: blur(3px);
  background-color: rgba(255, 255, 255, 0.7);
}

.plane-loader {
  position: relative;
  width: 100px;
  height: 100px;
  animation: spin 2.5s linear infinite;
}

.plane-icon {
  position: absolute;
  top: 0;
  left: 50%;
  transform: translate(-50%, 0);
  animation: flyTilt 1.2s ease-in-out infinite alternate;
}

.loading-text {
  color: #1976d2;
  font-weight: 600;
  font-size: 1.1rem;
  margin-top: 1.5rem;
}

@keyframes spin {
  from { transform: rotate(0deg); }
  to { transform: rotate(360deg); }
}

@keyframes flyTilt {
  from { transform: translate(-50%, 0) rotate(-15deg); }
  to { transform: translate(-50%, 0) rotate(15deg); }
}
</style>
