<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'paymentable_type', 'paymentable_id', 'stripe_payment_id', 'amount', 'status',
    ];

    public function paymentable()
    {
        return $this->morphTo();
    }
}
