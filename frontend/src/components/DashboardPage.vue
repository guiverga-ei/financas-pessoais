<template>
  <div class="dashboard-container">
    <h1>Dashboard Financeiro</h1>

    <!-- Card para Saldo Total -->
    <div class="card balance-card">
      <h3>Saldo Total</h3>
      <p class="balance">{{ dashboardData.total_balance }}€</p>
    </div>

    <!-- Resumo do Mês -->
    <div class="card summary-card">
      <h3>Resumo do Mês</h3>
      <p>
        <strong>Receitas:</strong> <span class="income">{{ dashboardData.monthly_income }}€</span>
      </p>
      <p>
        <strong>Despesas:</strong> <span class="expense">{{ dashboardData.monthly_expense }}€</span>
      </p>
      <p><strong>Saldo:</strong> {{ dashboardData.monthly_balance }}€</p>
    </div>

    <!-- Despesas por Categoria -->
    <div class="card category-card">
      <h3>Despesas por Categoria</h3>
      <ul>
        <li v-for="category in dashboardData.expenses_by_category" :key="category.category_id">
          {{ category.category.name }}: <span class="expense">{{ category.total }}€</span>
        </li>
      </ul>
    </div>

    <!-- Últimas Transações -->
    <div class="card transactions-card">
      <h3>Últimas Transações</h3>
      <ul>
        <li v-for="transaction in dashboardData.latest_transactions" :key="transaction.id">
          {{ transaction.description }}
          <span :class="getTransactionClass(transaction.amount)">{{ transaction.amount }}€</span>
          ({{ transaction.category.name }})
        </li>
      </ul>
    </div>

    <!-- Saldos por contas -->
    <div class="card accounts-card">
      <h3>Saldos das Contas</h3>
      <ul>
        <li v-for="account in dashboardData.balances_by_account" :key="account.id">
          {{ account.name }}:
          <span :class="getTransactionClass(account.balance)">
            {{ account.balance }}
          </span>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      dashboardData: {
        total_balance: 0,
        monthly_income: 0,
        monthly_expense: 0,
        monthly_balance: 0,
        expenses_by_category: [],
        latest_transactions: [],
        accounts: [], // Adiciona contas aqui
        balances_by_account: [], // Saldo por conta
        latest_transactions_by_account: [] // Últimas transações por conta
      }
    }
  },
  mounted() {
    this.fetchDashboardData()
  },
  methods: {
    fetchDashboardData() {
      axios
        .get('http://127.0.0.1:8000/api/dashboard')
        .then((response) => {
          this.dashboardData = response.data
        })
        .catch((error) => {
          console.error('Erro ao buscar dados do dashboard:', error)
        })
    },
    getTransactionClass(amount) {
      return amount >= 0 ? 'income' : 'expense'
    }
  }
}
</script>

<style scoped>
/* Container principal para o dashboard */
.dashboard-container {
  display: grid;
  grid-template-columns: 1fr 1fr;
  grid-gap: 20px;
  padding: 20px;
}

/* Estilo para os cards */
.card {
  background-color: #f9f9f9;
  border-radius: 10px;
  padding: 20px;
  box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
}

/* Estilo específico para o saldo */
.balance-card {
  grid-column: span 2; /* Faz com que o saldo ocupe toda a largura */
}

.balance {
  font-size: 2em;
  font-weight: bold;
  color: #42b983;
}

/* Estilos para receitas e despesas */
.income {
  color: green;
}

.expense {
  color: red;
}

/* Estilo para a tipografia dentro dos cards */
h1 {
  color: #42b983;
  /* text-align: center; */
  grid-column: span 2;
}

h3 {
  color: #42b983;
}

ul {
  list-style-type: none;
  padding: 0;
}

li {
  margin-bottom: 10px;
  font-size: 1.1em;
}

p {
  margin: 0;
}

.transactions-card ul {
  padding-left: 0;
}

.transactions-card li {
  display: flex;
  justify-content: space-between;
}

.transactions-card li div {
  min-width: 50px; /* Ajusta este valor conforme necessário */
}
</style>
