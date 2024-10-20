<template>
  <div>
    <!-- Mensagem de boas-vindas -->
    <h1>Welcome Guilherme</h1>
    <p>Navigate through the transactions and categories using the menu.</p>

    <!-- Componente do Dashboard reutilizado -->
    <Dashboard />
  </div>
</template>

<script>
// Importa o componente Dashboard
import Dashboard from './DashboardPage.vue'

export default {
  name: 'Home',
  components: {
    Dashboard // Registra o componente Dashboard para uso no template
  }
}
</script>

<style scoped>
h1 {
  color: #42b983;
}
</style>
