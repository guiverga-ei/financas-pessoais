<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Transaction;

class TransactionController extends Controller
{

    public function index()
    {
        // Buscar todas as transações da base de dados
        $transactions = Transaction::all();

        // Retornar as transações em formato JSON
        return response()->json($transactions);
    }


    public function store(Request $request)
    {
        // Validar os dados da transação
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
        ]);

        // Criar a transação
        $transaction = Transaction::create([
            'description' => $request->description,
            'amount' => $request->amount,
            'date' => $request->date,
        ]);

        // Retornar a resposta com a transação criada
        return response()->json($transaction, 201);  // 201 = Created
    }
}
