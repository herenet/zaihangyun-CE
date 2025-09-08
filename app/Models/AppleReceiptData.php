<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AppleReceiptData extends Model
{
    protected $table = 'apple_receipt_data';
    
    protected $primaryKey = 'verification_id';
    
    public $timestamps = false;

    protected $fillable = [
        'verification_id',
        'app_key',
        'receipt_data_hash',
        'receipt_data',
    ];

    /**
     * 关联验证记录
     */
    public function verification()
    {
        return $this->belongsTo(AppleReceiptVerification::class, 'verification_id');
    }
}