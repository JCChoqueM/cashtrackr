<?php

use App\Models\Budget;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/* ============================================================
   Verifica que el dueño del gasto pueda eliminarlo
   ============================================================ */
it('allows the expense owner to delete an expense', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $budget = Budget::factory()->for($user)->create([
        'type' => 'general',
    ]);

    $expense = Expense::factory()->for($budget)->create([
        'name' => 'Supermercado',
    ]);

    $response = $this->actingAs($user)->delete(route('expenses.destroy', [$budget, $expense]));

    $response->assertRedirect(route('budgets.show', $budget));
    $this->assertSoftDeleted('expenses', [
        'id' => $expense->id,
    ]);

});
/* ============================================================
   Fin: dueño puede eliminar su gasto
   ============================================================ */

/* ============================================================
   Verifica que un usuario no autenticado (guest) NO pueda
   eliminar gastos y sea redirigido al login
   ============================================================ */
it('does not allow guests to delete expenses', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $budget = Budget::factory()->for($user)->create([
        'type' => 'general',
    ]);

    $expense = Expense::factory()->for($budget)->create();

    $response = $this->delete(route('expenses.destroy', [$budget, $expense]));
    $response->assertRedirect(route('login'));
    $this->assertDatabaseHas('expenses', [
        'id' => $expense->id,
    ]);

});
/* ============================================================
   Fin: guest no puede eliminar gastos
   ============================================================ */

/* ============================================================
   Verifica que un usuario con email NO verificado no pueda
   eliminar gastos
   ============================================================ */
it('does not allow unverified users to delete expenses', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $budget = Budget::factory()->for($user)->create([
        'type' => 'general',
    ]);

    $expense = Expense::factory()->for($budget)->create();

    $response = $this->actingAs($user)
        ->delete(route('expenses.destroy', [$budget, $expense]));

    $response->assertRedirect(route('verification.notice'));
    $this->assertDatabaseHas('expenses', [
        'id' => $expense->id,
    ]);

});
/* ============================================================
   Fin: usuario no verificado no puede eliminar gastos
   ============================================================ */

/* ============================================================
   Verifica que un usuario autenticado NO pueda eliminar
   gastos que pertenecen a otro usuario
   ============================================================ */
it('does not allow other users to delete expenses they do not own', function () {
    $owner = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $otherUser = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $budget = Budget::factory()->for($owner)->create([
        'type' => 'general',
    ]);

    $expense = Expense::factory()->for($budget)->create([
        'name' => 'Supermercado',
    ]);

    $response = $this->actingAs($otherUser)
        ->delete(route('expenses.destroy', [$budget, $expense]));

    $response->assertForbidden();
    $this->assertDatabaseHas('expenses', [
        'id' => $expense->id,
    ]);


});
/* ============================================================
   Fin: otro usuario no puede eliminar gastos ajenos
   ============================================================ */
