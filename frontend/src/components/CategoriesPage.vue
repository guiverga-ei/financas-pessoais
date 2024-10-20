<template>
  <div class="categories-container">
    <h2>New Category</h2>

    <!-- Formulário para criar nova categoria -->
    <form @submit.prevent="createCategory" class="category-form">
      <div class="form-group">
        <label for="name">Category Name</label>
        <input v-model="newCategoryName" placeholder="Category Name" required />

        <label for="type">Category Type</label>
        <select v-model="newCategoryType" required>
          <option disabled value="">Select Type</option>
          <option value="income">Income</option>
          <option value="expense">Expense</option>
        </select>

        <button type="submit" class="btn-submit">Add Category</button>
      </div>
    </form>

    <h2>Existing Categories</h2>

    <!-- Tabela de categorias -->
    <table class="category-table">
      <thead>
        <tr>
          <th>Id</th>
          <th>Category Name</th>
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
              <i class="fas fa-times"></i>
            </button>

            <!-- Botão de deletar -->
            <button
              v-if="editingCategory.id !== category.id"
              @click="deleteCategory(category.id)"
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

export default {
  data() {
    return {
      categories: [], // Lista de categorias
      newCategoryName: '', // Nome da nova categoria
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
          type: this.newCategoryType
        })
        this.categories.push(response.data) // Adiciona a nova categoria à lista
        this.newCategoryName = '' // Limpa o campo
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
          type: this.editingCategory.type
        })
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
  .categories-container {
   
   padding: 20px;
 }

/* Estilo do formulário */
.category-form {
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

.category-table th {
  background-color: #f8f8f8;
  font-weight: bold;
}

/* Cores alternadas na tabela */
.striped-row {
  background-color: #f9f9f9;
}

.category-table tr:hover {
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
