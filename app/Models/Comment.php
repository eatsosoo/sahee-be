<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Comment extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'content',
        'user_id',
        'book_id',
        'parent_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
