<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Borrowing extends Model
{
    protected $fillable = ['borrow_code', 'requester_id', 'from_room_id', 'to_room_id', 'borrow_date', 'return_date', 'status', 'approved_by', 'notes'];

    public function requester() {
        return $this->belongsTo(User::class, 'requester_id');
    }

    public function fromRoom() {
        return $this->belongsTo(Room::class, 'from_room_id');
    }

    public function toRoom() {
        return $this->belongsTo(Room::class, 'to_room_id');
    }

    public function details() {
        return $this->hasMany(BorrowingDetail::class);
    }

    public function approver() {
        return $this->belongsTo(User::class, 'approved_by');
    }
}