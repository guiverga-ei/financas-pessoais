<template>
    <div>
      <h1>Transactions</h1>
  
      <!-- Formulário para criar nova transação -->
      <form @submit.prevent="createTransaction">
        <input v-model="newTransactionDescription" placeholder="Description" required />
        <input v-model.number="newTransactionAmount" type="number" placeholder="Amount" required />
        
        <!-- Selecionar categoria -->
        <select v-model="newTransactionCategoryId" required>
          <option disabled value="">Select Category</option>
          <option v-for="category in categories" :key="category.id" :value="category.id">
            {{ category.name }} - {{ category.type }}
          </option>
        </select>
  
        <!-- Adicionar o campo para data da transação -->
        <input v-model="newTransactionDate" type="date" required />
  
        <button type="submit">Add Transaction</button>
      </form>
  
      <h2>Existing Transactions</h2>
  
      <!-- Seletor de mês -->
      <div>
        <label for="month">Select Month:</label>
        <input type="month" v-model="selectedMonth" @change="fetchTransactionsByMonth" />
      </div>
      
      <!-- Tabela de transações -->
      <table class="transaction-table">
        <thead>
          <tr>
            <th>Id</th>
            <th>Date</th>
            <th>Description</th>
            <th>Amount (€)</th>
            <th>Type</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="(transaction, index) in transactions" :key="transaction.id" :class="{ 'striped-row': index % 2 === 1 }">
            <td>{{ transaction.id }}</td>
            <td>{{ transaction.date }}</td>
            <td>{{ transaction.description }}</td>
            <td :class="getAmountClass(transaction)">
              {{ getAmountSign(transaction) }} {{ transaction.amount }}€
            </td>
            <td>{{ transaction.category?.type || '###' }}</td>
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
  
      <h2>Balance: {{ balance }}€</h2>
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
        selectedMonth: '', // Mês selecionado pelo usuário
        newTransactionDescription: '', // Descrição da nova transação
        newTransactionAmount: 0, // Valor da nova transação
        newTransactionCategoryId: '', // Categoria selecionada
        newTransactionDate: '', // Data da nova transação
        editingTransaction: {} // Transação sendo editada
      }
    },
    mounted() {
      this.fetchTransactions() // Carrega todas as transações inicialmente
      this.fetchCategories() // Carrega as categorias
      this.fetchBalance() // Carrega o saldo
    },
    methods: {
      // Buscar saldo (filtrado por mês, se disponível)
      async fetchBalance() {
        try {
          const query = this.selectedMonth ? `?month=${this.selectedMonth}` : '';
          const response = await axios.get(`http://localhost:8000/api/balance${query}`);
          this.balance = response.data.balance;
        } catch (error) {
          console.error('Error fetching balance:', error);
        }
      },
  
      // Buscar transações filtradas por mês
      async fetchTransactionsByMonth() {
        try {
          const query = this.selectedMonth ? `?month=${this.selectedMonth}` : '';
          const response = await axios.get(`http://localhost:8000/api/transactions${query}`);
          this.transactions = response.data;
          this.fetchBalance(); // Atualiza o saldo com base nas transações filtradas
        } catch (error) {
          console.error('Error fetching transactions:', error);
        }
      },
  
      // Criar nova transação
      async createTransaction() {
        try {
          await axios.post('http://localhost:8000/api/transactions', {
            description: this.newTransactionDescription,
            amount: this.newTransactionAmount,
            category_id: this.newTransactionCategoryId,
            date: this.newTransactionDate
          });
  
          if (this.selectedMonth) {
            this.fetchTransactionsByMonth();
          } else {
            this.fetchTransactions();
          }
  
          // Limpar campos do formulário
          this.newTransactionDescription = '';
          this.newTransactionAmount = 0;
          this.newTransactionCategoryId = '';
          this.newTransactionDate = '';
        } catch (error) {
          console.error('Error creating transaction:', error);
        }
      },
  
      // Buscar todas as transações
      async fetchTransactions() {
        try {
          const response = await axios.get('http://localhost:8000/api/transactions');
          this.transactions = response.data;
          this.fetchBalance(); // Atualiza o saldo com todas as transações
        } catch (error) {
          console.error('Error fetching transactions:', error);
        }
      },
  
      // Buscar categorias
      async fetchCategories() {
        try {
          const response = await axios.get('http://localhost:8000/api/categories');
          this.categories = response.data;
        } catch (error) {
          console.error('Error fetching categories:', error);
        }
      },
  
      // Método para determinar a classe de estilo com base no tipo de transação
      getAmountClass(transaction) {
        if (transaction.category?.type === 'income') {
          return 'amount-income'
        } else if (transaction.category?.type === 'expense') {
          return 'amount-expense'
        }
        return ''
      },
  
      // Método para retornar o sinal "+" ou "-" baseado no tipo da transação
      getAmountSign(transaction) {
        if (transaction.category?.type === 'income') {
          return '+'
        } else if (transaction.category?.type === 'expense') {
          return '-'
        }
        return ''
      }
    }
  }
  </script>
  
  <style scoped>
  h1, h2 {
    color: #42b983;
  }
  
  form {
    margin-bottom: 1.5rem;
  }
  
  button {
    background-color: #42b983;
    color: white;
    border: none;
    padding: 5px 10px;
    cursor: pointer;
  }
  
  button:hover {
    background-color: #36a170;
  }
  
  input, select {
    margin-right: 10px;
    padding: 5px;
  }
  
  /* Estilo da tabela */
  .transaction-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }
  
  .transaction-table th, .transaction-table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: left;
  }
  
  /* Estilo do cabeçalho */
  .transaction-table thead {
    background-color: #f8f8f8;
    font-weight: bold;
  }
  
  /* Sombreamento alternado */
  .striped-row {
    background-color: #f9f9f9;
  }
  
  /* Sombreamento ao passar o mouse sobre a linha */
  .transaction-table tr:hover {
    background-color: #f1f1f1;
  }
  
  /* Estilo para transações de income */
  .amount-income {
    color: green;
    font-weight: bold;
  }
  
  /* Estilo para transações de expense */
  .amount-expense {
    color: red;
    font-weight: bold;
  }
  
  /* Botões de ação */
  .action-btn {
    border: none;
    padding: 5px 8px;
    cursor: pointer;
    margin-right: 5px;
  }
  
  .edit-btn {
    background-color: #f0ad4e;
    color: white;
  }
  
  .edit-btn:hover {
    background-color: #ec971f;
  }
  
  .delete-btn {
    background-color: #d9534f;
    color: white;
  }
  
  .delete-btn:hover {
    background-color: #c9302c;
  }
  
  .save-btn {
    background-color: #5cb85c;
    color: white;
  }
  
  .save-btn:hover {
    background-color: #4cae4c;
  }
  
  .cancel-btn {
    background-color: #d9534f;
    color: white;
  }
  
  .cancel-btn:hover {
    background-color: #c9302c;
  }
  
  i {
    font-size: 1.2rem;
  }
  </style>
  