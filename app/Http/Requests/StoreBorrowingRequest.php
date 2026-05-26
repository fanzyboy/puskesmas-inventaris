<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreBorrowingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // Siapa saja yang login bisa request peminjaman
    }

    public function rules(): array
    {
        return [
            'item_id' => 'required|exists:items,id',
            'qty' => 'required|integer|min:1',
            'borrow_date' => 'required|date|after_or_equal:today',
            'notes' => 'nullable|string|max:500'
        ];
    }
}