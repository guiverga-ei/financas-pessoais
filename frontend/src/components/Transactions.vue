<template>
    <div>
      <h1>Transactions</h1>
  
      <!-- Formulário para criar nova transação -->
      <form @submit.prevent="createTransaction">
        <input v-model="newTransactionDescription" placeholder="Description" required>
        <input v-model.number="newTransactionAmount" type="number" placeholder="Amount" required>
        <select v-model="newTransactionType" required>
          <option value="income">Income</option>
          <option value="expense">Expense</option>
        </select>
        <button type="submit">Add Transaction</button>
      </form>
  
      <h2>Existing Transactions</h2>
      <ul>
        <li v-for="transaction in transactions" :key="transaction.id">
          [ id:{{ transaction.id }} ] {{ transaction.description }} - {{ transaction.amount }}€ - {{ transaction.type }}
          <button @click="editTransaction(transaction)">Edit</button>
          <button @click="deleteTransaction(transaction.id)">Delete</button>
  
          <!-- Formulário para editar uma transação -->
          <div v-if="transaction.id === editingTransaction.id">
            <form @submit.prevent="updateTransaction(transaction.id)">
              <input v-model="editingTransaction.description" placeholder="New Description" required>
              <input v-model.number="editingTransaction.amount" type="number" placeholder="New Amount" required>
              <select v-model="editingTransaction.type" required>
                <option value="income">Income</option>
                <option value="expense">Expense</option>
              </select>
              <button type="submit">Update Transaction</button>
              <button @click="cancelEdit">Cancel</button>
            </form>
          </div>
        </li>
      </ul>
  
      <h2>Balance: {{ balance }}€</h2>
    </div>
  </template>
  
  <script>
  import axios from 'axios'
  
  export default {
    data() {
      return {
        transactions: [], // Lista de transações
        balance: 0, // Saldo total
        newTransactionDescription: '', // Descrição da nova transação
        newTransactionAmount: 0, // Valor da nova transação
        newTransactionType: 'income', // Tipo da nova transação (income ou expense)
        editingTransaction: {}, // Transação sendo editada
      }
    },
    mounted() {
      this.fetchTransactions()
      this.fetchBalance()
    },
    methods: {
      // Buscar transações existentes
      async fetchTransactions() {
        try {
          const response = await axios.get('http://localhost:8000/api/transactions')
          this.transactions = response.data
        } catch (error) {
          console.error('Error fetching transactions:', error)
        }
      },
  
      // Buscar saldo
      async fetchBalance() {
        try {
          const response = await axios.get('http://localhost:8000/api/balance')
          this.balance = response.data.balance
        } catch (error) {
          console.error('Error fetching balance:', error)
        }
      },
  
      // Criar nova transação
      async createTransaction() {
        try {
          const response = await axios.post('http://localhost:8000/api/transactions', {
            description: this.newTransactionDescription,
            amount: this.newTransactionAmount,
            type: this.newTransactionType
          })
          this.transactions.push(response.data) // Adiciona a nova transação à lista
          this.newTransactionDescription = '' // Limpa o campo
          this.newTransactionAmount = 0 // Limpa o campo
          this.newTransactionType = 'income' // Reseta o tipo
          this.fetchBalance() // Atualiza o saldo
        } catch (error) {
          console.error('Error creating transaction:', error)
        }
      },
  
      // Editar transação (mostrar formulário)
      editTransaction(transaction) {
        this.editingTransaction = { ...transaction } // Clonar a transação para edição
      },
  
      // Atualizar uma transação existente
      async updateTransaction(transactionId) {
        try {
          const response = await axios.put(`http://localhost:8000/api/transactions/${transactionId}`, {
            description: this.editingTransaction.description,
            amount: this.editingTransaction.amount,
            type: this.editingTransaction.type
          })
          // Atualiza a transação na lista local
          const index = this.transactions.findIndex(trans => trans.id === transactionId)
          this.transactions[index] = response.data
          this.editingTransaction = {} // Limpa o formulário de edição
          this.fetchBalance() // Atualiza o saldo
        } catch (error) {
          console.error('Error updating transaction:', error)
        }
      },
  
      // Cancelar edição
      cancelEdit() {
        this.editingTransaction = {} // Limpa o formulário de edição
      },
  
      // Excluir transação
      async deleteTransaction(transactionId) {
        try {
          await axios.delete(`http://localhost:8000/api/transactions/${transactionId}`)
          this.transactions = this.transactions.filter(trans => trans.id !== transactionId) // Remove da lista local
          this.fetchBalance() // Atualiza o saldo
        } catch (error) {
          console.error('Error deleting transaction:', error)
        }
      }
    }
  }
  </script>
  
  <style scoped>
  h1, h2 {
    color: #42b983;
  }
  
  form {
    margin-bottom: 1rem;
  }
  
  button {
    margin-left: 10px;
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
  
  ul {
    list-style-type: none;
    padding: 0;
  }
  
  li {
    margin-bottom: 10px;
  }
  </style>
  