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

        if ($month) {
            $transactions = Transaction::with('category')
                ->whereMonth('date', Carbon::parse($month)->month)
                ->whereYear('date', Carbon::parse($month)->year)
                ->orderBy('date', 'asc')
                ->get();
        } else {
            $transactions = Transaction::with('category')
                ->orderBy('date', 'asc')
                ->get();
        }

        return response()->json($transactions);
    }

    // Adicionar uma nova transação
    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric',
            'date' => 'required|date',
            'category_id' => 'required|exists:categories,id', // Valida que a categoria existe
        ]);

        // Obter a categoria associada à transação
        $category = \App\Models\Category::findOrFail($request->category_id);

        // Se a categoria for de "expense", garantimos que o valor seja negativo
        $amount = $request->amount;
        if ($category->type === 'expense') {
            $amount = -abs($amount); // Garante que o valor seja sempre negativo para despesas
        }

        // Cria a transação com o valor ajustado
        $transaction = Transaction::create([
            'description' => $request->description,
            'amount' => $amount,
            'date' => $request->date,
            'category_id' => $request->category_id
        ]);

        return response()->json($transaction, 201);
    }

    // Mostrar uma transação específica
    public function show($id)
    {
        $transaction = Transaction::with('category')->findOrFail($id);
        return response()->json($transaction);
    }

    // Atualizar uma transação
    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);

        // Atualizar os dados da transação
        $transaction->update($request->all());

        // Ajustar o valor para ser negativo se for uma despesa
        if ($transaction->category->type === 'expense') {
            $transaction->amount = -abs($transaction->amount);
        }

        return response()->json(['message' => 'Transaction updated successfully!']);
    }

    // Excluir uma transação
    public function destroy($id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully!']);
    }

    // Calcular saldo total (soma de receitas - soma de despesas)
    public function calculateBalance(Request $request)
    {
        $month = $request->query('month');

        if ($month) {
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
            $incomes = Transaction::whereHas('category', function ($query) {
                $query->where('type', 'income');
            })->sum('amount');

            $expenses = Transaction::whereHas('category', function ($query) {
                $query->where('type', 'expense');
            })->sum('amount');
        }

        // Cálculo do saldo final
        $balance = $incomes - abs($expenses);  // As despesas são tratadas como negativas

        return response()->json(['balance' => $balance]);
    }





    // Método para fornecer dados para o dashboard
    public function getDashboardData(Request $request)
    {
        $currentMonth = Carbon::now()->month;

        // 1. Total de Receitas e Despesas (agregação total)
        $totalIncome = Transaction::whereHas('category', function ($query) {
            $query->where('type', 'income');
        })->sum('amount');

        $totalExpense = Transaction::whereHas('category', function ($query) {
            $query->where('type', 'expense');
        })->sum('amount');

        // Saldo Total
        $totalBalance = $totalIncome - abs($totalExpense);

        // 2. Resumo Mensal (Receitas e Despesas do mês atual)
        $monthlyIncome = Transaction::whereHas('category', function ($query) {
            $query->where('type', 'income');
        })
            ->whereMonth('date', $currentMonth)
            ->sum('amount');

        $monthlyExpense = Transaction::whereHas('category', function ($query) {
            $query->where('type', 'expense');
        })
            ->whereMonth('date', $currentMonth)
            ->sum('amount');

        // Saldo mensal
        $monthlyBalance = $monthlyIncome - abs($monthlyExpense);

        // 3. Despesas por Categoria
        $expensesByCategory = Transaction::whereHas('category', function ($query) {
            $query->where('type', 'expense');
        })
            ->with('category')
            ->selectRaw('category_id, SUM(amount) as total')
            ->groupBy('category_id')
            ->get();

        // 4. Últimas Transações
        $latestTransactions = Transaction::with('category')
            ->orderBy('date', 'desc')
            ->take(5)
            ->get();

        //dd($totalBalance);
        // Retornar os dados formatados para o frontend
        return response()->json([
            'total_balance' => $totalBalance,
            'monthly_income' => $monthlyIncome,
            'monthly_expense' => $monthlyExpense,
            'monthly_balance' => $monthlyBalance,
            'expenses_by_category' => $expensesByCategory,
            'latest_transactions' => $latestTransactions,
        ]);
    }
}
