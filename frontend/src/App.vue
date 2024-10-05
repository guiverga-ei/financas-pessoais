<template>
    <div>
      <h1>Transactions</h1>
      <ul>
        <li v-for="transaction in transactions" :key="transaction.id">
          {{ transaction.description }} - {{ transaction.amount }} - {{ transaction.type }}
        </li>
      </ul>
      <h2>Balance: {{ balance }}</h2>
    </div>
  </template>
  
  <script>
  import axios from 'axios'
  
  export default {
    data() {
      return {
        transactions: [],
        balance: 0 // Adiciona uma propriedade para armazenar o balance
      }
    },
    mounted() {
      this.fetchTransactions()
      this.fetchBalance() // Chama o método para obter o balance
    },
    methods: {
      async fetchTransactions() {
        try {
          const response = await axios.get('http://localhost:8000/api/transactions')
          this.transactions = response.data
        } catch (error) {
          console.error('Error fetching transactions:', error)
        }
      },
      async fetchBalance() {
        try {
          const response = await axios.get('http://localhost:8000/api/balance')
          this.balance = response.data.balance // Armazena o balance retornado pela API
        } catch (error) {
          console.error('Error fetching balance:', error)
        }
      }
    }
  }
  </script>
  
  <style scoped>
  h1 {
    color: #42b983;
  }
  h2 {
    color: #333;
  }
  </style>
  