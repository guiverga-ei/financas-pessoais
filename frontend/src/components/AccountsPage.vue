<template>
    <div class="accounts-container">
      <h2>New Account</h2>
  
      <!-- Formulário para criar nova categoria -->
      <form @submit.prevent="createAccount" class="account-form">
        <div class="form-group">
          <label for="name">Account Name</label>
          <input v-model="newAccountName" placeholder="Account Name" required />
  
          <label for="type">Account Type</label>
          <select v-model="newAccountType" required>
            <option disabled value="">Select Type</option>
            <option value="income">Income</option>
            <option value="expense">Expense</option>
          </select>
  
          <button type="submit" class="btn-submit">Add Account</button>
        </div>
      </form>
  
      <h2>Existing Accounts</h2>
  
      <!-- Tabela de categorias -->
      <table class="account-table">
        <thead>
          <tr>
            <th>Id</th>
            <th>Account Name</th>
            <th>Account Type</th>
            <th>Balace</th>
            <th>Created At</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(account, index) in accounts"
            :key="account.id"
            :class="{ 'striped-row': index % 2 === 1 }"
          >
            <td>{{ account.id }}</td>
  
            <!-- Exibe campos de edição apenas para a categoria sendo editada -->
            <td v-if="editingAccount.id !== account.id">{{ account.name }}</td>
            <td v-else>
              <input v-model="editingAccount.name" placeholder="New Name" />
            </td>
  
            <td v-if="editingAccount.id !== account.id">{{ account.account_type }}</td>
            <td v-else>
              <select v-model="editingAccount.account_type" required>
                <option disabled value="">Select Type</option>
                <option value="checking">Checking</option>
                <option value="savings">Savings</option>
                <option value="investment">Investment</option>
              </select>
            </td>

            <td v-if="editingAccount.id !== account.id">{{ account.balance }}</td>
            <td v-else>
              <input v-model="editingAccount.balance" placeholder="New Balance" />
            </td>

            <td v-if="editingAccount.id !== account.id">{{ formatDate(account.created_at) }}</td>
            <td v-else>
              <input v-model="editingAccount.created_at" placeholder="New Created Date" />
            </td>
  
            <!-- Ações -->
            <td>
              <!-- Botão de editar -->
              <button
                v-if="editingAccount.id !== account.id"
                @click="editAccount(account)"
                class="action-btn edit-btn"
              >
                <i class="fas fa-edit"></i>
              </button>
  
              <!-- Botão de salvar -->
              <button
                v-else
                @click="updateAccount(account.id)"
                class="action-btn save-btn"
              >
                <i class="fas fa-save"></i>
              </button>
  
              <!-- Botão de cancelar -->
              <button
                v-if="editingAccount.id === account.id"
                @click="cancelEdit"
                class="action-btn cancel-btn"
              >
                <i class="fas fa-times"></i>
              </button>
  
              <!-- Botão de deletar -->
              <button
                v-if="editingAccount.id !== account.id"
                @click="deleteAccount(account.id)"
                class="action-btn delete-btn"
              >
                <i class="fas fa-trash-alt"></i>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </template>
  
  <script>
  import axios from 'axios'
  import { format, parseISO } from 'date-fns';
  
  export default {
    data() {
      return {
        accounts: [], // Lista de categorias
        newAccountName: '', // Nome da nova categoria
        newAccountType: '', // Tipo da nova categoria (income ou expense)
        editingAccount: {} // Categoria sendo editada
      }
    },
    mounted() {
      this.fetchAccounts()
    },
    methods: {
      // Buscar categorias existentes
      async fetchAccounts() {
        try {
          const response = await axios.get('http://localhost:8000/api/accounts')
          this.accounts = response.data
        } catch (error) {
          console.error('Error fetching accounts:', error)
        }
      },
  
      // Criar nova categoria
      async createAccount() {
        try {
          const response = await axios.post('http://localhost:8000/api/accounts', {
            name: this.newAccountName,
            type: this.newAccountType
          })
          this.accounts.push(response.data) // Adiciona a nova categoria à lista
          this.newAccountName = '' // Limpa o campo
          this.newAccountType = '' // Limpa o campo de tipo
        } catch (error) {
          console.error('Error creating account:', error)
        }
      },
  
      // Editar categoria (mostrar formulário)
      editAccount(account) {
        this.editingAccount = { ...account } // Clona a categoria para edição
      },
  
      // Atualizar uma categoria existente
      async updateAccount(accountId) {
        try {
          const response = await axios.put(`http://localhost:8000/api/accounts/${accountId}`, {
            name: this.editingAccount.name,
            type: this.editingAccount.type
          })
          const index = this.accounts.findIndex((cat) => cat.id === accountId)
          this.accounts[index] = response.data
          this.editingAccount = {} // Limpa o formulário de edição
        } catch (error) {
          console.error('Error updating account:', error)
        }
      },
  
      // Cancelar edição
      cancelEdit() {
        this.editingAccount = {} // Limpa o formulário de edição
      },
  
      // Excluir categoria
      async deleteAccount(accountId) {
        try {
          await axios.delete(`http://localhost:8000/api/accounts/${accountId}`)
          this.accounts = this.accounts.filter((cat) => cat.id !== accountId) // Remove da lista local
        } catch (error) {
          console.error('Error deleting account:', error)
        }
      },

      formatDate(date) {
        return format(parseISO(date), 'dd/MM/yyyy');
      }
    }
  }
  </script>
  
  <style scoped>
    .accounts-container {
     
     padding: 20px;
   }
  
  /* Estilo do formulário */
  .account-form {
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
  
  input, select {
    margin-bottom: 15px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 14px;
  }
  
  /* Estilo do botão */
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
  .account-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }
  
  .account-table th, .account-table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: left;
  }
  
  .account-table th {
    background-color: #f8f8f8;
    font-weight: bold;
  }
  
  /* Cores alternadas na tabela */
  .striped-row {
    background-color: #f9f9f9;
  }
  
  .account-table tr:hover {
    background-color: #f1f1f1;
  }
  
  /* Estilo dos botões de ação */
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
  
  /* Estilo de botão de salvar */
  .save-btn {
    color: #5cb85c;
  }
  
  .cancel-btn {
    color: #d9534f;
  }
  </style>
  