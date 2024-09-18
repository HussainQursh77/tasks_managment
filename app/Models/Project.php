<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description'
    ];

    public function users()
    {
        return $this->belongsToMany(User::class)
            ->withPivot('user_role', 'contribution_hours', 'last_activity', 'start_time', 'end_time')
            ->withTimestamps();
    }
}
