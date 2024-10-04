import React, { useEffect, useState } from 'react';

function App() {
  const [transactions, setTransactions] = useState([]);
  const [description, setDescription] = useState('');
  const [amount, setAmount] = useState('');
  const [date, setDate] = useState('');

  // Função para buscar as transações da API
  useEffect(() => {
    fetch('http://127.0.0.1:8000/api/transactions')
      .then(response => response.json())
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
        amount: parseFloat(amount), // Certifica-te de que o valor é numérico
        date,
      }),
    })
      .then(response => response.json())
      .then(data => {
        // Atualizar a lista de transações com a nova transação criada
        setTransactions([...transactions, data]);

        // Limpar os campos do formulário
        setDescription('');
        setAmount('');
        setDate('');
      })
      .catch(error => console.error('Erro ao criar transação:', error));
  };

  return (
    <div className="App">
      <h1>Transações Financeiras</h1>

      {/* Formulário para criar uma nova transação */}
      <form onSubmit={createTransaction}>
        <div>
          <label>Descrição:</label>
          <input
            type="text"
            value={description}
            onChange={(e) => setDescription(e.target.value)}
            required
          />
        </div>
        <div>
          <label>Valor (€):</label>
          <input
            type="number"
            step="0.01"
            value={amount}
            onChange={(e) => setAmount(e.target.value)}
            required
          />
        </div>
        <div>
          <label>Data:</label>
          <input
            type="date"
            value={date}
            onChange={(e) => setDate(e.target.value)}
            required
          />
        </div>
        <button type="submit">Adicionar Transação</button>
      </form>

      {/* Lista de transações */}
      <ul>
        {transactions.map((transaction, index) => (
          <li key={index}>
            {transaction.description}: {transaction.amount}€ (Data: {transaction.date})
          </li>
        ))}
      </ul>
    </div>
  );
}

export default App;
