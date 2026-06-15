<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Auth;

class StoreItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        if (Auth::user()->hasRole('admin')) return true;
        return Auth::user()->room_id == $this->room_id;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'category' => 'required|string|in:Alat Medis,Elektronik,Mebel,Logistik',
            'room_id' => 'required|exists:rooms,id',
            'qty' => 'required|integer|min:0',
            'status' => 'required|in:Baik,Rusak,Tidak Tersedia,Digunakan,Dikembalikan'
        ];
    }
}