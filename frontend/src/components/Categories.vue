<template>
    <div>
      <h1>Categories</h1>
  
      <!-- Formulário para criar nova categoria -->
      <form @submit.prevent="createCategory">
        <input v-model="newCategoryName" placeholder="Category Name" required>
        <input v-model="newCategoryDescription" placeholder="Category Description">
        <button type="submit">Add Category</button>
      </form>
  
      <h2>Existing Categories</h2>
      <ul>
        <li v-for="category in categories" :key="category.id">
          [ id:{{ category.id }} ] {{ category.name }}
          <button @click="editCategory(category)">Edit</button>
          <button @click="deleteCategory(category.id)">Delete</button>
  
          <!-- Formulário para editar uma categoria -->
          <div v-if="category.id === editingCategory.id">
            <form @submit.prevent="updateCategory(category.id)">
              <input v-model="editingCategory.name" placeholder="New Name">
              <input v-model="editingCategory.description" placeholder="New Description">
              <button type="submit">Update Category</button>
              <button @click="cancelEdit">Cancel</button>
            </form>
          </div>
        </li>
      </ul>
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
        editingCategory: {}, // Categoria sendo editada
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
            description: this.newCategoryDescription
          })
          this.categories.push(response.data) // Adiciona a nova categoria à lista
          this.newCategoryName = '' // Limpa o campo
          this.newCategoryDescription = '' // Limpa o campo
        } catch (error) {
          console.error('Error creating category:', error)
        }
      },
  
      // Editar categoria (mostrar formulário)
      editCategory(category) {
        this.editingCategory = { ...category } // Clonar a categoria para edição
      },
  
      // Atualizar uma categoria existente
      async updateCategory(categoryId) {
        try {
          const response = await axios.put(`http://localhost:8000/api/categories/${categoryId}`, {
            name: this.editingCategory.name,
            description: this.editingCategory.description
          })
          // Atualiza a categoria na lista local
          const index = this.categories.findIndex(cat => cat.id === categoryId)
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
          this.categories = this.categories.filter(cat => cat.id !== categoryId) // Remove da lista local
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
  
  input {
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
  