<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ElectionsController;


Route::get('/election/{election}', [ElectionsController::class, 'show'])->name('election.show');
Route::get('/election/{election}/vote', [ElectionsController::class, 'vote'])->name('election.vote');
Route::post('/election/{election}/vote', [ElectionsController::class, 'voteStore'])->name('election.vote.store');

