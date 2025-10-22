<template>
  <v-app>
    <Navbar />   <!-- seu header -->
    <v-main>
      <v-container>
        <router-view />  <!-- as páginas do Vue Router -->
      </v-container>
    </v-main>
    <Footer /> <!-- footer bonito que vamos criar -->
  </v-app>
</template>

<script>
import Navbar from './components/Navbar.vue'
import Footer from './components/Footer.vue'

export default {
  components: {
    Navbar,
    Footer
  }
}
</script>
