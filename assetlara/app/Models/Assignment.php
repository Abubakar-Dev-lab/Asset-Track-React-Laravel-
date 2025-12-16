<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    use HasFactory;

    // We don't use SoftDeletes here. History is permanent.
    public $timestamps = false; // Optional: If you didn't add timestamps to this table, set false.
    // BUT we defined 'assigned_at' manually, so usually we keep timestamps=false or just manage them manually.
    // In our migration we relied on specific columns, so let's allow mass assignment:

    protected $fillable = [
        'user_id',
        'asset_id',
        'assigned_by',
        'assigned_at',
        'returned_at',
        'notes'
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'returned_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class); // The employee who got the item
    }

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function admin()
    {
        // We explicitly point this to the 'users' table
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
