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
        <tr
          v-for="(transaction, index) in transactions"
          :key="transaction.id"
          :class="{ 'striped-row': index % 2 === 1 }"
        >
          <td>{{ transaction.id }}</td>

          <!-- Exibe o campo de data -->
          <td v-if="editingTransaction.id !== transaction.id">{{ transaction.date }}</td>
          <td v-else>
            <input v-model="editingTransaction.date" type="date" required />
          </td>

          <!-- Exibe o campo de descrição -->
          <td v-if="editingTransaction.id !== transaction.id">{{ transaction.description }}</td>
          <td v-else>
            <input v-model="editingTransaction.description" placeholder="New Description" />
          </td>

          <!-- Exibe o campo de valor -->
          <td v-if="editingTransaction.id !== transaction.id">{{ transaction.amount }}€</td>
          <td v-else>
            <input
              v-model.number="editingTransaction.amount"
              type="number"
              placeholder="New Amount"
            />
          </td>

          <!-- Exibe o campo de tipo (categoria) -->
          <td v-if="editingTransaction.id !== transaction.id">
            {{ transaction.category?.type || '###' }}
          </td>
          <td v-else>
            <select v-model="editingTransaction.category_id" required>
              <option disabled value="">Select Category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }} - {{ category.type }}
              </option>
            </select>
          </td>

          <td>
            <!-- Botão de editar com ícone de lápis -->
            <button
              v-if="editingTransaction.id !== transaction.id"
              @click="editTransaction(transaction)"
              class="action-btn edit-btn"
            >
              <i class="fas fa-edit"></i>
            </button>

            <!-- Botão de salvar -->
            <button v-else @click="updateTransaction(transaction.id)" class="action-btn save-btn">
              <i class="fas fa-save"></i>
            </button>

            <!-- Botão de cancelar -->
            <button
              v-if="editingTransaction.id === transaction.id"
              @click="cancelEdit"
              class="action-btn cancel-btn"
            >
              <i class="fas fa-times"></i>
            </button>

            <!-- Botão de deletar com ícone de caixote de lixo -->
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
      newTransactionDescription: '', // Descrição da nova transação
      newTransactionAmount: 0, // Valor da nova transação
      newTransactionCategoryId: '', // Categoria selecionada
      newTransactionDate: '', // Data da nova transação
      editingTransaction: {} // Transação sendo editada
    }
  },
  mounted() {
    this.fetchTransactions()
    this.fetchCategories()
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

    // Buscar categorias existentes
    async fetchCategories() {
      try {
        const response = await axios.get('http://localhost:8000/api/categories')
        this.categories = response.data
      } catch (error) {
        console.error('Error fetching categories:', error)
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
        await axios.post('http://localhost:8000/api/transactions', {
          description: this.newTransactionDescription,
          amount: this.newTransactionAmount,
          category_id: this.newTransactionCategoryId, // Usar categoria selecionada
          date: this.newTransactionDate // Enviar a data da transação
        })

        // Recarrega a lista de transações após criar a nova transação
        this.fetchTransactions()

        // Limpar os campos do formulário
        this.newTransactionDescription = '' // Limpa o campo
        this.newTransactionAmount = 0 // Limpa o campo
        this.newTransactionCategoryId = '' // Reseta a categoria
        this.newTransactionDate = '' // Limpa a data

        // Atualiza o saldo
        this.fetchBalance()
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
        const response = await axios.put(
          `http://localhost:8000/api/transactions/${transactionId}`,
          {
            description: this.editingTransaction.description,
            amount: this.editingTransaction.amount,
            category_id: this.editingTransaction.category_id, // Usar categoria selecionada ao editar
            date: this.editingTransaction.date // Enviar a data da transação ao atualizar
          }
        )
        // Atualiza a transação na lista local
        const index = this.transactions.findIndex((trans) => trans.id === transactionId)
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
        this.transactions = this.transactions.filter((trans) => trans.id !== transactionId) // Remove da lista local
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
  border: 1px solid #ddd; /* Adiciona borda nas células */
  text-align: left;
}

/* Estilo do cabeçalho */
.transaction-table thead {
  background-color: #f8f8f8;
  font-weight: bold;
}

/* Sombreamento alternado */
.striped-row {
  background-color: #f9f9f9; /* Fundo mais claro para linhas ímpares */
}

/* Sombreamento ao passar o mouse sobre a linha */
.transaction-table tr:hover {
  background-color: #f1f1f1;
}

/* Botões de ação */
.action-btn {
  border: none;
  padding: 5px 8px;
  cursor: pointer;
  margin-right: 5px;
}

.edit-btn {
  background-color: #f0ad4e; /* Amarelo */
  color: white;
}

.edit-btn:hover {
  background-color: #ec971f;
}

.delete-btn {
  background-color: #d9534f; /* Vermelho */
  color: white;
}

.delete-btn:hover {
  background-color: #c9302c;
}

.save-btn {
  background-color: #5cb85c; /* Verde */
  color: white;
}

.save-btn:hover {
  background-color: #4cae4c;
}

.cancel-btn {
  background-color: #d9534f; /* Vermelho */
  color: white;
}

.cancel-btn:hover {
  background-color: #c9302c;
}

i {
  font-size: 1.2rem; /* Tamanho do ícone */
}
</style>
