<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = ['item_code', 'name', 'category', 'room_id', 'qty', 'status'];

    public function room() {
        return $this->belongsTo(Room::class);
    }

    public function logs() {
        return $this->hasMany(ItemLog::class);
    }
}