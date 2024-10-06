<template>
  <div>
    <!-- Navbar -->
    <nav class="navbar">
      <ul class="navbar-list">
        <li><router-link to="/">Home</router-link></li>
        <li><router-link to="/transactions">Transactions</router-link></li>
        <li><router-link to="/categories">Categories</router-link></li>
      </ul>
    </nav>

    <!-- Router View -->
    <router-view></router-view>
  </div>
</template>

<script>
export default {
  name: 'App'
}
</script>

<style scoped>
.navbar {
  background-color: #42b983;
  padding: 1rem;
  display: flex;
  justify-content: center;
}

.navbar-list {
  list-style-type: none;
  padding: 0;
  display: flex;
}

.navbar-list li {
  margin: 0 1rem;
}

.navbar-list a {
  color: white;
  text-decoration: none;
  font-weight: bold;
}

.navbar-list a:hover {
  text-decoration: underline;
}
</style>
