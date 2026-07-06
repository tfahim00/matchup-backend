<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class NotificationCustom extends Model
{
    use HasFactory;

    protected $table = 'notifications_custom';

    protected $fillable = ['user_id','title','body','type','is_read'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
