<?php

namespace App\Http\Controllers;

use App\Ai\Agents\BudgetAssistant;
use App\Models\Budget;
use Illuminate\Http\Request;
use Illuminate\Routing\Attributes\Controllers\Middleware;

#[Middleware('auth')]
#[Middleware('verified')]
class BudgetChatController extends Controller
{
    public function store(Request $request, Budget $budget)
    {
        // toma el promp del usuario
        $messages = $request->input('messages', []);
        $lastMessage = collect($messages)->last();

        $prompt = collect(data_get($lastMessage, 'parts', []))
            ->where('type', 'text')
            ->pluck('text')
            ->implode(' ')
        ?: data_get($lastMessage, 'content', '');
        // fin toma el promp del usuario

        // instancia el asistente y se pasa mas informacion tiene su System prompt completo (app/Ai/Agents/BusgetAssistant.php) y se le da los tool para que haga su consulta hacia las BD
        $agent = new BudgetAssistant;
        $agent->budgetId = $budget->id;
        $agent->hasCategories = $budget->isGeneral();

        if ($budget->isGoal()) {
            $agent->budgetContext = "Este presupuesto es de tipo Meta/Objetivo llamado '{$budget->name}' con un monto total de \${$budget->amount}. Los gastos NO tienen categorías, solo nombre y monto.";
        } else {
            $agent->budgetContext = "Este presupuesto es de tipo General llamado '{$budget->name}' con un monto total de \${$budget->amount}. Los gastos tienen nombre, monto y categoría.";
        }

        // escribe las respuestas
        return $agent
            ->stream(
                $prompt,
                provider: 'openrouter', // que proveedor va a usar
                model: 'poolside/laguna-xs.2:free',// que modelo va a usar
                //  model: 'google/gemma-4-26b-a4b-it:free',
                // model: 'nvidia/nemotron-3-super-120b-a12b:free',
                // model: 'qwen/qwen3-coder:free',
                // model: 'z-ai/glm-4.5-air:free',

            )->usingVercelDataProtocol(); //regresa

    }
}
