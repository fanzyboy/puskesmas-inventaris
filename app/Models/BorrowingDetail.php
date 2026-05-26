<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BorrowingDetail extends Model
{
    protected $fillable = ['borrowing_id', 'item_id', 'qty'];

    public function item() {
        return $this->belongsTo(Item::class);
    }
}