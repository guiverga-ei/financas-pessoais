<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // List all transactions
    public function index()
    {
        return Transaction::with('category')->get(); // Inclui a categoria relacionada
    }

    // public function index()
    // {
    //     $transactions = Transaction::all();
    //     return response()->json($transactions);
    // }

    // Add a new transaction
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'category_id' => 'required|exists:categories,id', // Valida que a categoria existe
        ]);

        $transaction = Transaction::create($request->all());

        return response()->json($transaction, 201);
    }

    // public function store(Request $request)
    // {
    //     $transaction = new Transaction();
    //     //$transaction->user_id = "1"; // Related to the authenticated user
    //     $transaction->description = $request->description;
    //     $transaction->amount = $request->amount;
    //     $transaction->date = $request->date;
    //     //$transaction->type = $request->type; // Receives "income" or "expense"
    //     //$transaction->category = $request->category;
    //     'category_id' => 'required|exists:categories,id'; // Valida que a categoria existe
    //     $transaction->save();

    //     return response()->json(['message' => 'Transaction added successfully!']);
    // }

    // Show a specific transaction
    public function show($id)
    {
        $transaction = Transaction::findOrFail($id);
        return response()->json($transaction);
    }

    // Update a transaction
    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update($request->all());

        return response()->json(['message' => 'Transaction updated successfully!']);
    }

    // Delete a transaction
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully!']);
    }

    // Calculate total balance (sum of incomes - sum of expenses)
    public function calculateBalance()
    {
        // Somar todas as receitas (transactions com categoria 'income')
        $incomes = Transaction::whereHas('category', function ($query) {
            $query->where('type', 'income'); // Filtra as categorias de tipo 'income'
        })->sum('amount');

        // Somar todas as despesas (transactions com categoria 'expense')
        $expenses = Transaction::whereHas('category', function ($query) {
            $query->where('type', 'expense'); // Filtra as categorias de tipo 'expense'
        })->sum('amount');

        // Calcula o saldo
        $balance = $incomes - $expenses;

        return response()->json(['balance' => $balance]);
    }
    // // Calculate total balance (sum of incomes - sum of expenses)
    // public function calculateBalance()
    // {
    //     $incomes = Transaction::where('type', 'income')->sum('amount');
    //     $expenses = Transaction::where('type', 'expense')->sum('amount');

    //     $balance = $incomes - $expenses;

    //     return response()->json(['balance' => $balance]);
    // }
}
