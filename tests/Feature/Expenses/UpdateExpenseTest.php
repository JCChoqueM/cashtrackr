<?php

use App\Models\Budget;
use App\Models\Expense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

/* ============================================================
   Verifica que el dueño del gasto pueda actualizarlo
   ============================================================ */
it('allows the expense owner to update an expense', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $budget = Budget::factory()->for($user)->create([
        'type' => 'general',
    ]);

    $expense = Expense::factory()->for($budget)->create([
        'name' => 'Supermercado',
        'amount' => 500,
        'category' => 'food',
    ]);

    $response = $this->actingAs($user)->put(route('expenses.update', [$budget, $expense]), [
        'name' => 'Supermercado Wallmart',
        'amount' => 750,
        'category' => 'food',
    ]);

    $response->assertRedirect(route('budgets.show', $budget));
    $response->assertSessionHas('success', 'Gasto Actualizado Correctamente');

    $this->assertDatabaseHas('expenses', [
        'name' => 'Supermercado Wallmart',
        'amount' => 750,
        'category' => 'food',
        'id' => $expense->id,
    ]);

});
/* ============================================================
   Fin: dueño puede actualizar su gasto
   ============================================================ */

/* ============================================================
   Verifica que un usuario no autenticado (guest) NO pueda
   actualizar gastos y sea redirigido al login
   ============================================================ */
it('does not allow guests to update expenses', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $budget = Budget::factory()->for($user)->create([
        'type' => 'general',
    ]);
    $expense = Expense::factory()->for($budget)->create();

    $response = $this->put(route('expenses.update', [$budget, $expense]), [
        'name' => 'Gasto Actualizado',
        'amount' => 200.00,
        'category' => 'food',
    ]);
    $response->assertRedirect(route('login'));
    $this->assertDatabaseMissing('expenses', [
        'name' => 'Gasto Actualizado',
        'amount' => 200.00,
        'category' => 'food',
        'id' => $expense->id,
    ]);
});
/* ============================================================
   Fin: guest no puede actualizar gastos
   ============================================================ */

/* ============================================================
   Verifica que un usuario con email NO verificado no pueda
   actualizar gastos
   ============================================================ */
it('does not allow unverified users to update expenses', function () {
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $budget = Budget::factory()->for($user)->create([
        'type' => 'general',
    ]);

    $expense = Expense::factory()->for($budget)->create();

    $response = $this->actingAs($user)->put(route('expenses.update', [$budget, $expense]), [
        'name' => 'Gasto Actualizado',
        'amount' => 200.00,
        'category' => 'food',
    ]);
    $response->assertRedirect(route('verification.notice'));

    $this->assertDatabaseMissing('expenses', [
        'name' => 'Gasto Actualizado',
        'amount' => 200.00,
        'category' => 'food',
        'id' => $expense->id,
    ]);

});
/* ============================================================
   Fin: usuario no verificado no puede actualizar gastos
   ============================================================ */

/* ============================================================
   Verifica que un usuario autenticado NO pueda actualizar
   gastos que pertenecen a otro usuario
   ============================================================ */
it('does not allow other users to update expenses they do not own', function () {
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
        'name' => 'Original',
    ]);

    $response = $this->actingAs(($otherUser))->put(route('expenses.update', [$budget, $expense]), [
        'name' => 'hackeado!!',
        'amount' => 999.00,
        'category' => 'food',
    ]);

    $response->assertForbidden();

    $this->assertDatabaseHas('expenses', [
        'id' => $expense->id,
        'name' => 'Original',
    ]);

});
/* ============================================================
   Fin: otro usuario no puede actualizar gastos ajenos
   ============================================================ */

/* ============================================================
   Verifica que los campos requeridos sean validados
   correctamente al actualizar un gasto en un presupuesto general
   ============================================================ */
it('validates required fields when updating an expense in a general budget', function () {
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $budget = Budget::factory()->for($user)->create([
        'type' => 'general',
    ]);

    $expense = Expense::factory()->for($budget)->create();

    $response = $this->actingAs($user)
        ->from(route('budgets.show', $budget))
        ->put(route('expenses.update', [$budget, $expense]), [
            'name' => '',
            'amount' => '',
            'category' => '',
        ]);

    $response->assertRedirect(route('budgets.show', $budget));
    $response->assertSessionHasErrors([
        'name',
        'amount',
        'category',
    ]);

});
/* ============================================================
   Fin: validación de campos requeridos al actualizar gasto
   ============================================================ */
