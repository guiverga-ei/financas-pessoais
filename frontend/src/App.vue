<template>
    <div>
      <h1>Transactions</h1>
      <ul>
        <li v-for="transaction in transactions" :key="transaction.id">
            [ id:{{ transaction.id }} ] {{ transaction.description }} - {{ transaction.amount }}€ - {{ transaction.type }}
        </li>
      </ul>
      <h2>Balance: {{ balance }}</h2>
      <h3>Categories</h3>
      <ul>
        <li v-for="category in categories" :key="category.id">
            [ id:{{ category.id }} ] {{ category.name }} 
        </li>
      </ul>
    </div>
  </template>
  
  <script>
  import axios from 'axios'
  
  export default {
    data() {
      return {
        transactions: [],
        balance: 0 
      }
    },
    mounted() {
      this.fetchTransactions()
      this.fetchCategories()
      this.fetchBalance() 
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
      async fetchCategories() {
        try {
          const response = await axios.get('http://localhost:8000/api/categories')
          this.categories = response.data
        } catch (error) {
          console.error('Error fetching categories:', error)
        }
      },
      async fetchBalance() {
        try {
          const response = await axios.get('http://localhost:8000/api/balance')
          this.balance = response.data.balance 
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
  h3 {
    color: #bb0e81;
  }
  </style>
  