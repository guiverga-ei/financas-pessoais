<template>
  <div class="app-layout">
    <!-- Sidebar -->
    <nav class="sidebar">
      <div class="sidebar-logo">MyFinance</div>
      <ul class="sidebar-list">
        <li><router-link to="/"><i class="fas fa-home"></i>  Dashboard</router-link></li>
        <li><router-link to="/transactions"><i class="fas fa-exchange-alt"></i> Transactions</router-link></li>
        <li><router-link to="/categories"><i class="fas fa-list"></i> Categories</router-link></li>
      </ul>
    </nav>

    <!-- Conteúdo principal -->
    <div class="main-section">
      <!-- Barra de utilizador -->
      <UserBar />

      <!-- Main content -->
      <div class="main-content">
        <router-view></router-view>
      </div>
    </div>
  </div>
</template>

<script>
import UserBar from './components/UserBar.vue';

export default {
  name: 'App',
  components: {
    UserBar // Barra de utilizador
  }
}
</script>

<style scoped>
/* Estrutura de layout */
.app-layout {
  display: flex;
  height: 100vh;
}

/* Sidebar ajustada para ocupar toda a altura da página */
.sidebar {
  background-color: #42b983;
  color: white;
  width: 250px;
  height: 100vh; /* Ocupa 100% da altura da janela */
  position: fixed; /* Fixa a sidebar no lado esquerdo */
  top: 0;
  left: 0;
  padding: 20px;
}

.sidebar-logo {
  font-size: 1.5rem;
  font-weight: bold;
  margin-bottom: 40px;
}

.sidebar-list {
  list-style-type: none;
  padding: 0;
}

.sidebar-list li {
  margin-bottom: 20px;
}

.sidebar-list a {
  color: white;
  text-decoration: none;
  font-weight: bold;
  /* display: flex; */
  align-items: center;
}

.sidebar-list i {
  margin-right: 8px;
}

/* Layout da secção principal */
.main-section {
  margin-left: 250px; /* Compensar a largura da sidebar */
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100vh;
}

/* Barra de utilizador */
.user-bar {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  background-color: #42b983;
  padding: 10px;
  border-bottom: 1px solid #ddd;
}

/* Conteúdo principal */
.main-content {
  flex-grow: 1;
  padding: 20px;
  background-color: #f9f9f9;
  overflow-y: auto;
}
</style>
