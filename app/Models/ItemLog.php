<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ItemLog extends Model
{
    protected $fillable = ['item_id', 'user_id', 'action', 'old_values', 'new_values'];

    public function item() {
        return $this->belongsTo(Item::class)->withDefault(['name' => 'Barang Terhapus']);
    }

    public function user() {
        return $this->belongsTo(User::class)->withDefault(['name' => 'Sistem']);
    }
}