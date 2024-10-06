<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    // Listar todas as categorias
    public function index()
    {
        return Category::all(); // Retorna todas as categorias
    }

    // Criar uma nova categoria
    public function store(Request $request)
    {
        // Valida os dados de entrada
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:income,expense', // Validação para 'type'
        ]);

        // Cria a categoria
        $category = Category::create($request->all());

        return response()->json($category, 201); // Retorna a categoria criada
    }

    // Exibir uma categoria específica
    public function show($id)
    {
        return Category::findOrFail($id); // Retorna a categoria com o id fornecido
    }

    // Atualizar uma categoria existente
    public function update(Request $request, $id)
    {
        // Valida os dados de entrada
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type' => 'required|in:income,expense', // Validação para 'type'
        ]);

        // Encontra a categoria e atualiza os dados
        $category = Category::findOrFail($id);
        $category->update($request->all());

        return response()->json($category); // Retorna a categoria atualizada
    }

    // Excluir uma categoria
    public function destroy($id)
    {
        Category::destroy($id); // Deleta a categoria pelo id

        return response()->json(['message' => 'Category deleted successfully']);
    }
}

