<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;
use Carbon\Carbon;

class TransactionController extends Controller
{
    // List all transactions
    public function index(Request $request)
    {
        // Verifica se o mês foi passado como parâmetro na URL
        $month = $request->query('month');

        // Se houver um filtro de mês, aplique-o
        if ($month) {
            // Converta o mês para um formato de data, ex: '2023-10'
            $transactions = Transaction::with('category')
                ->whereMonth('date', Carbon::parse($month)->month)
                ->whereYear('date', Carbon::parse($month)->year)
                ->orderBy('date', 'asc')
                ->get();
        } else {
            // Se não houver mês, retorna todas as transações ordenadas por data
            $transactions = Transaction::with('category')
                ->orderBy('date', 'asc')
                ->get();
        }

        return response()->json($transactions);
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


    public function calculateBalance(Request $request)
    {
        $month = $request->query('month');

        if ($month) {
            // Filtrar transações pelo mês e ano e incluir a relação com a categoria
            $incomes = Transaction::whereHas('category', function ($query) {
                $query->where('type', 'income');
            })
                ->whereMonth('date', Carbon::parse($month)->month)
                ->whereYear('date', Carbon::parse($month)->year)
                ->sum('amount');

            $expenses = Transaction::whereHas('category', function ($query) {
                $query->where('type', 'expense');
            })
                ->whereMonth('date', Carbon::parse($month)->month)
                ->whereYear('date', Carbon::parse($month)->year)
                ->sum('amount');
        } else {
            // Somente transações com categorias "income"
            $incomes = Transaction::whereHas('category', function ($query) {
                $query->where('type', 'income');
            })
                ->sum('amount');

            // Somente transações com categorias "expense"
            $expenses = Transaction::whereHas('category', function ($query) {
                $query->where('type', 'expense');
            })
                ->sum('amount');
        }

        // Cálculo do saldo final
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
