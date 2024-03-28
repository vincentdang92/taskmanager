<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class Project extends Model
{
    use HasFactory;
    protected $fillable = [
        'title'
    ];

    public function tasks(): HasMany
    {
        return $this->hasMany(Task::class);
    }
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    protected static function booted():void
    {
        static::addGlobalScope('creator', function(Builder $builder){
            $builder->where('user_id', Auth::id());
        });
    }
}
