<template>
    <div class="user-bar">
      <img :src="userProfilePicture" alt="User profile picture" class="profile-picture" />
      <span class="user-name">{{ userName }}</span>
    </div>
  </template>
  
  <script>
  export default {
    name: "UserBar",
    data() {
      return {
        userName: "Bem-vindo, Guilherme", // Nome do utilizador para já é fixo
        userProfilePicture: "https://via.placeholder.com/40" // Imagem temporária
      };
    }
  };
  </script>
  
  <style scoped>
  .user-bar {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    padding: 10px;
    background-color: #f1f1f1;
    border-bottom: 1px solid #ddd;
    height: 60px;
  }
  
  .profile-picture {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    margin-right: 10px;
  }
  
  .user-name {
    font-weight: bold;
    color: white;
  }
  </style>
  