<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CreditPurchases extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'credits_purchased',
        'cost_per_credit_at_purchase',
        'total_amount',
        'amount_fee',
        'amount_net',
        'm_payment_id',
        'pf_payment_id',
        'payment_status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


}
