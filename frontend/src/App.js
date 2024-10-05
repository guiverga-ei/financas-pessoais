import React, { useEffect, useState } from 'react';
import 'bootstrap/dist/css/bootstrap.min.css';

function App() {
  const [transactions, setTransactions] = useState([]);
  const [description, setDescription] = useState('');
  const [amount, setAmount] = useState('');
  const [date, setDate] = useState('');

  // Função para buscar as transações da API
  useEffect(() => {
    fetch('http://127.0.0.1:8000/api/transactions')
      .then(response => {
        if (!response.ok) {
          throw new Error('Erro ao buscar transações');
        }
        return response.json();
      })
      .then(data => setTransactions(data))
      .catch(error => console.error('Erro ao buscar transações:', error));
  }, []);

  // Função para criar uma nova transação
  const createTransaction = (e) => {
    e.preventDefault();

    // Enviar os dados do formulário para a API Laravel
    fetch('http://127.0.0.1:8000/api/transactions', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        description,
        amount: parseFloat(amount),
        date,
      }),
    })
      .then(response => {
        if (!response.ok) {
          throw new Error('Erro ao criar transação');
        }
        return response.json();
      })
      .then(data => {
        // Atualizar a lista de transações com a nova transação criada
        setTransactions([...transactions, data]);

        // Limpar os campos do formulário
        setDescription('');
        setAmount('');
        setDate('');
        alert("Transação criada com sucesso!");
      })
      .catch(error => console.error('Erro ao criar transação:', error));
  };

  return (
    <div className="container mt-5">
      <h1 className="text-center">Gerenciador de Transações Financeiras</h1>

      {/* Formulário para criar uma nova transação */}
      <form onSubmit={createTransaction} className="border p-4 rounded">
        <div className="mb-3">
          <label className="form-label">Descrição:</label>
          <input
            type="text"
            className="form-control"
            value={description}
            onChange={(e) => setDescription(e.target.value)}
            required
          />
        </div>
        <div className="mb-3">
          <label className="form-label">Valor (€):</label>
          <input
            type="number"
            className="form-control"
            step="0.01"
            value={amount}
            onChange={(e) => setAmount(e.target.value)}
            required
          />
        </div>
        <div className="mb-3">
          <label className="form-label">Data:</label>
          <input
            type="date"
            className="form-control"
            value={date}
            onChange={(e) => setDate(e.target.value)}
            required
          />
        </div>
        <button type="submit" className="btn btn-success">Adicionar Transação</button>
      </form>

      {/* Lista de transações */}
      <h2 className="mt-4">Transações Existentes</h2>
      <ul className="list-group">
        {transactions.map((transaction, index) => (
          <li className="list-group-item" key={index}>
            {transaction.description}: {transaction.amount}€ (Data: {transaction.date})
          </li>
        ))}
      </ul>
    </div>
  );
}

export default App;
