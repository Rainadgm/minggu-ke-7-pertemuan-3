<?php

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('books can be listed and created through the API', function () {
    Book::create([
        'title' => 'Existing Book',
        'description' => 'An existing book.',
        'status' => 'available',
    ]);

    $this->getJson('/api/books')
        ->assertOk()
        ->assertJsonCount(1)
        ->assertJsonPath('0.title', 'Existing Book');

    $this->postJson('/api/books', [
        'title' => 'New Book',
        'description' => 'A newly created book.',
        'status' => 'available',
    ])
        ->assertCreated()
        ->assertJsonPath('title', 'New Book');

    $this->assertDatabaseHas('books', ['title' => 'New Book']);
});

test('a book can be shown updated and deleted through the API', function () {
    $book = Book::create([
        'title' => 'Old Title',
        'description' => 'Book description.',
        'status' => 'available',
    ]);

    $this->getJson("/api/books/{$book->id}")
        ->assertOk()
        ->assertJsonPath('title', 'Old Title');

    $this->patchJson("/api/books/{$book->id}", ['title' => 'New Title'])
        ->assertOk()
        ->assertJsonPath('title', 'New Title');

    $this->deleteJson("/api/books/{$book->id}")
        ->assertOk()
        ->assertJsonPath('deleted', true);

    $this->assertDatabaseMissing('books', ['id' => $book->id]);
});

test('book creation validates required fields', function () {
    $this->postJson('/api/books', [])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['title', 'description', 'status']);
});
