<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    // List all transactions
    public function index()
    {
        $transactions = Transaction::all();
        return response()->json($transactions);
    }

    // Add a new transaction
    public function store(Request $request)
    {
        $transaction = new Transaction();
        //$transaction->user_id = "1"; // Related to the authenticated user
        $transaction->description = $request->description;
        $transaction->amount = $request->amount;
        $transaction->date = $request->date;
        $transaction->type = $request->type; // Receives "income" or "expense"
        $transaction->category = $request->category;
        $transaction->save();

        return response()->json(['message' => 'Transaction added successfully!']);
    }

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
        $incomes = Transaction::where('type', 'income')->sum('amount');
        $expenses = Transaction::where('type', 'expense')->sum('amount');

        $balance = $incomes - $expenses;

        return response()->json(['balance' => $balance]);
    }
}
