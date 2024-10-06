<template>
    <div>
      <h1>Categories</h1>
  
      <!-- Formulário para criar nova categoria -->
      <form @submit.prevent="createCategory">
        <input v-model="newCategoryName" placeholder="Category Name" required />
        <input v-model="newCategoryDescription" placeholder="Category Description" />
        <select v-model="newCategoryType" required>
          <option disabled value="">Select Type</option>
          <option value="income">Income</option>
          <option value="expense">Expense</option>
        </select>
        <button type="submit">Add Category</button>
      </form>
  
      <h2>Existing Categories</h2>
  
      <!-- Tabela de categorias -->
      <table class="category-table">
        <thead>
          <tr>
            <th>Id</th>
            <th>Category Name</th>
            <th>Description</th>
            <th>Type</th>
            <th>Actions</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(category, index) in categories"
            :key="category.id"
            :class="{ 'striped-row': index % 2 === 1 }"
          >
            <td>{{ category.id }}</td>
  
            <!-- Exibe campos de edição apenas para a categoria sendo editada -->
            <td v-if="editingCategory.id !== category.id">{{ category.name }}</td>
            <td v-else>
              <input v-model="editingCategory.name" placeholder="New Name" />
            </td>
  
            <td v-if="editingCategory.id !== category.id">{{ category.description }}</td>
            <td v-else>
              <input v-model="editingCategory.description" placeholder="New Description" />
            </td>
  
            <td v-if="editingCategory.id !== category.id">{{ category.type }}</td>
            <td v-else>
              <select v-model="editingCategory.type" required>
                <option disabled value="">Select Type</option>
                <option value="income">Income</option>
                <option value="expense">Expense</option>
              </select>
            </td>
  
            <!-- Ações -->
            <td>
              <!-- Botão de editar -->
              <button
                v-if="editingCategory.id !== category.id"
                @click="editCategory(category)"
                class="action-btn edit-btn"
              >
                <i class="fas fa-edit"></i>
              </button>
  
              <!-- Botão de salvar -->
              <button
                v-else
                @click="updateCategory(category.id)"
                class="action-btn save-btn"
              >
                <i class="fas fa-save"></i>
              </button>
  
              <!-- Botão de cancelar -->
              <button
                v-if="editingCategory.id === category.id"
                @click="cancelEdit"
                class="action-btn cancel-btn"
              >
                <i class="fas fa-cancel"></i>
              </button>
  
              <!-- Botão de deletar -->
              <button v-if="editingCategory.id != category.id" @click="deleteCategory(category.id)" class="action-btn delete-btn">
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
  
  export default {
    data() {
      return {
        categories: [], // Lista de categorias
        newCategoryName: '', // Nome da nova categoria
        newCategoryDescription: '', // Descrição da nova categoria
        newCategoryType: '', // Tipo da nova categoria (income ou expense)
        editingCategory: {} // Categoria sendo editada
      }
    },
    mounted() {
      this.fetchCategories()
    },
    methods: {
      // Buscar categorias existentes
      async fetchCategories() {
        try {
          const response = await axios.get('http://localhost:8000/api/categories')
          this.categories = response.data
        } catch (error) {
          console.error('Error fetching categories:', error)
        }
      },
  
      // Criar nova categoria
      async createCategory() {
        try {
          const response = await axios.post('http://localhost:8000/api/categories', {
            name: this.newCategoryName,
            description: this.newCategoryDescription,
            type: this.newCategoryType // Inclui o campo 'type' ao criar a categoria
          })
          this.categories.push(response.data) // Adiciona a nova categoria à lista
          this.newCategoryName = '' // Limpa o campo
          this.newCategoryDescription = '' // Limpa o campo
          this.newCategoryType = '' // Limpa o campo de tipo
        } catch (error) {
          console.error('Error creating category:', error)
        }
      },
  
      // Editar categoria (mostrar formulário)
      editCategory(category) {
        this.editingCategory = { ...category } // Clona a categoria para edição
      },
  
      // Atualizar uma categoria existente
      async updateCategory(categoryId) {
        try {
          const response = await axios.put(`http://localhost:8000/api/categories/${categoryId}`, {
            name: this.editingCategory.name,
            description: this.editingCategory.description,
            type: this.editingCategory.type // Inclui o campo 'type' ao atualizar a categoria
          })
          // Atualiza a categoria na lista local
          const index = this.categories.findIndex((cat) => cat.id === categoryId)
          this.categories[index] = response.data
          this.editingCategory = {} // Limpa o formulário de edição
        } catch (error) {
          console.error('Error updating category:', error)
        }
      },
  
      // Cancelar edição
      cancelEdit() {
        this.editingCategory = {} // Limpa o formulário de edição
      },
  
      // Excluir categoria
      async deleteCategory(categoryId) {
        try {
          await axios.delete(`http://localhost:8000/api/categories/${categoryId}`)
          this.categories = this.categories.filter((cat) => cat.id !== categoryId) // Remove da lista local
        } catch (error) {
          console.error('Error deleting category:', error)
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
  .category-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
  }
  
  .category-table th, .category-table td {
    padding: 12px 15px;
    border: 1px solid #ddd;
    text-align: left;
  }
  
  /* Estilo do cabeçalho */
  .category-table thead {
    background-color: #f8f8f8;
    font-weight: bold;
  }
  
  /* Sombreamento alternado */
  .striped-row {
    background-color: #f9f9f9;
  }
  
  /* Sombreamento ao passar o mouse sobre a linha */
  .category-table tr:hover {
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
    font-size: 1.0rem; /* Tamanho do ícone */
  }
  </style>
  