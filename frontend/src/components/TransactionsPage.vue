<template>
  <div class="transactions-container">
    <h2>New Transaction</h2>

    <!-- Formulário para criar nova transação -->
    <form @submit.prevent="createTransaction" class="transaction-form">
      <div class="form-group">
        <label for="account">Account</label>
        <select v-model="newTransactionAccountId" required>
          <option disabled value="">Select Account</option>
          <option v-for="account in accounts" :key="account.id" :value="account.id">
            {{ account.name }}
          </option>
        </select>

        <label for="amount">Amount</label>
        <input v-model.number="newTransactionAmount" type="number" placeholder="Amount" required />

        <label for="category">Category</label>
        <select v-model="newTransactionCategoryId" required>
          <option disabled value="">Select Category</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }}
          </option>
        </select>

        <label for="date">Date</label>
        <input v-model="newTransactionDate" type="date" required />

        <label for="description">Description</label>
        <input v-model="newTransactionDescription" placeholder="Description" required />

        <button type="submit" class="btn-submit">Add Transaction</button>
      </div>
    </form>

    <h2>Existing Transactions</h2>

    <!-- Seletor de mês -->
    <div class="month-selector">
      <label for="month">Select Month: </label>
      <input type="month" v-model="selectedMonth" @change="fetchTransactionsByMonth" />
    </div>

    <!-- Tabela de transações -->
    <table v-if="transactions.length > 0" class="transaction-table">
      <thead>
        <tr>
          <th>Id</th>
          <th>Account</th>
          <th>Date</th>
          <th>Description</th>
          <th>Amount (€)</th>
          <th>Type</th>
          <th>Actions</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(transaction, index) in transactions"
          :key="transaction.id"
          :class="{ 'striped-row': index % 2 === 1 }"
        >
          <td>{{ transaction.id }}</td>
          <td>{{ transaction.account?.name || '---------' }}</td>
          <td>{{ transaction.date }}</td>
          <td>{{ transaction.description }}</td>
          <td :class="getAmountClass(transaction)">{{ transaction.amount }}€</td>
          <td>{{ transaction.category?.type || '---------' }}</td>
          <td>
            <button @click="editTransaction(transaction)" class="action-btn edit-btn">
              <i class="fas fa-edit"></i>
            </button>
            <button @click="deleteTransaction(transaction.id)" class="action-btn delete-btn">
              <i class="fas fa-trash-alt"></i>
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <h2 class="balance-display">Balance: {{ balance }}€</h2>
  </div>
</template>

<script>
import axios from 'axios'

export default {
  data() {
    return {
      transactions: [], // Lista de transações
      categories: [], // Lista de categorias
      balance: 0, // Saldo total
      accounts: [], // Lista de contas
      selectedMonth: '', // Mês selecionado pelo usuário
      newTransactionDescription: '', // Descrição da nova transação
      newTransactionAmount: 0, // Valor da nova transação
      newTransactionCategoryId: '', // Categoria selecionada
      newTransactionAccountId: '',
      newTransactionDate: '', // Data da nova transação
      editingTransaction: {} // Transação sendo editada
    }
  },
  mounted() {
    this.fetchCategories() // Carrega as categorias
    this.fetchBalance() // Carrega o saldo
    this.fetchAccounts() // Carrega as contas
    this.fetchTransactions() // Carrega todas as transações inicialmente
  },
  methods: {
    // Buscar saldo (filtrado por mês, se disponível)
    async fetchBalance() {
      try {
        const query = this.selectedMonth ? `?month=${this.selectedMonth}` : ''
        const response = await axios.get(`http://localhost:8000/api/balance${query}`)
        this.balance = response.data.balance
        //console.log('dashboard:' + response.data.monthly_income);
      } catch (error) {
        console.error('Error fetching balance:', error)
      }
    },

    // Buscar transações filtradas por mês
    async fetchTransactionsByMonth() {
      try {
        const query = this.selectedMonth ? `?month=${this.selectedMonth}` : ''
        const response = await axios.get(`http://localhost:8000/api/transactions${query}`)
        this.transactions = response.data
        this.fetchBalance() // Atualiza o saldo com base nas transações filtradas
      } catch (error) {
        console.error('Error fetching transactions:', error)
      }
    },

    // Criar nova transação
    async createTransaction() {
      try {
        await axios.post('http://localhost:8000/api/transactions', {
          description: this.newTransactionDescription,
          amount: this.newTransactionAmount,
          category_id: this.newTransactionCategoryId,
          date: this.newTransactionDate,
          account_id: this.newTransactionAccountId
        })

        if (this.selectedMonth) {
          this.fetchTransactionsByMonth()
        } else {
          this.fetchTransactions()
        }

        // Limpar campos do formulário
        this.newTransactionDescription = ''
        this.newTransactionAmount = 0
        this.newTransactionCategoryId = ''
        this.newTransactionDate = ''
        this.newTransactionAccountId = ''
      } catch (error) {
        console.error('Error creating transaction:', error)
      }
    },

    // Buscar todas as transações
    async fetchTransactions() {
      try {
        const response = await axios.get('http://localhost:8000/api/transactions')
        this.transactions = response.data
        this.fetchBalance() // Atualiza o saldo com todas as transações
      } catch (error) {
        console.error('Error fetching transactions:', error)
      }
    },

    // Buscar categorias
    async fetchCategories() {
      try {
        const response = await axios.get('http://localhost:8000/api/categories')
        this.categories = response.data
        console.log('categories:' + response.data);
      } catch (error) {
        console.error('Error fetching categories:', error)
      }
    },

    // Buscar accounts
    async fetchAccounts() {
      try {
        const response = await axios.get('http://localhost:8000/api/accounts')
        this.accounts = response.data
        //console.log('accounts:' + response.data);
      } catch (error) {
        console.error('Error fetching accounts:', error)
      }
    },

    // Método para determinar a classe de estilo com base no valor da transação
    getAmountClass(transaction) {
      return transaction.amount >= 0 ? 'amount-income' : 'amount-expense'
    },

    // Habilitar modo de edição para uma transação
    editTransaction(transaction) {
      this.editingTransaction = { ...transaction } // Clonar a transação para edição
    },

    // Salvar a transação editada
    async saveTransaction(transactionId) {
      try {
        await axios.put(
          `http://localhost:8000/api/transactions/${transactionId}`,
          this.editingTransaction
        )

        // Atualizar a transação na lista
        const index = this.transactions.findIndex((trans) => trans.id === transactionId)
        this.transactions[index] = { ...this.editingTransaction }

        // Limpar o modo de edição
        this.editingTransaction = {}
        this.fetchBalance() // Atualiza o saldo após a edição
      } catch (error) {
        console.error('Error saving transaction:', error)
      }
    },

    cancelEdit() {
      this.editingTransaction = {} // Reseta a transação sendo editada
    },

    // Excluir transação
    async deleteTransaction(transactionId) {
      try {
        await axios.delete(`http://localhost:8000/api/transactions/${transactionId}`)
        this.transactions = this.transactions.filter(
          (transaction) => transaction.id !== transactionId
        )
        this.fetchBalance() // Atualiza o saldo após deletar a transação
      } catch (error) {
        console.error('Error deleting transaction:', error)
      }
    }
  }
}
</script>

<style scoped>
.transactions-container {
  padding: 20px;
}
/* Alinhamento e formatação do formulário */
.transaction-form {
  display: flex;
  flex-wrap: wrap;
  gap: 15px;
}

.form-group {
  display: flex;
  flex-direction: column;
}

label {
  font-weight: bold;
  margin-bottom: 5px;
}

input,
select {
  margin-bottom: 15px;
  padding: 10px;
  border: 1px solid #ddd;
  border-radius: 5px;
  font-size: 14px;
}

/* Estilo dos botões */
button.btn-submit {
  background-color: #42b983;
  color: white;
  padding: 10px 15px;
  border: none;
  border-radius: 5px;
  cursor: pointer;
  font-size: 16px;
}

button.btn-submit:hover {
  background-color: #36a170;
}

/* Estilo da tabela */
.transaction-table {
  width: 100%;
  border-collapse: collapse;
  margin-top: 20px;
}

.transaction-table th,
.transaction-table td {
  padding: 12px 15px;
  border: 1px solid #ddd;
  text-align: left;
}

.transaction-table th {
  background-color: #f8f8f8;
  font-weight: bold;
}

/* Cores alternadas na tabela */
.striped-row {
  background-color: #f9f9f9;
}

/* Estilo para transações de income e expense */
.amount-income {
  color: green;
  font-weight: bold;
}

.amount-expense {
  color: red;
  font-weight: bold;
}

/* Estilo para botões de ação */
.action-btn {
  border: none;
  background-color: transparent;
  cursor: pointer;
}

.edit-btn {
  color: #f0ad4e;
}

.delete-btn {
  color: #d9534f;
}

/* Estilo do saldo */
.balance-display {
  color: #42b983;
  font-size: 24px;
  font-weight: bold;
  margin-top: 30px;
}

/* Estilo para o seletor de mês */
.month-selector {
  margin-bottom: 20px;
  display: flex;
  align-items: center;
}
</style>
