<?php

namespace App\Ai\Agents;

use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class BudgetAssistant implements Agent, Conversational, HasTools
{
    use Promptable;

   public int $budgetId=0;
   public string $budgetContext='';
   public bool $hasCategories=true

    public function instructions(): Stringable|string
    {
        return 'You are a helpful assistant.';
    }

    public function tools(): iterable
    {
        return [];
    }
}
