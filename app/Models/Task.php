<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;


class Task extends Model
{
    use HasFactory;
    protected $fillable = [
        'title',
        'is_done',
        'user_id',
        'project_id'
    ];
    protected $casts = [
        'is_done'=> 'boolean'
    ];
    protected $hidden = [
        'updated_at'
    ];
    public function creator():BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    protected static function booted():void
    {
        static::addGlobalScope('creator', function(Builder $builder){
            $builder->where('user_id', Auth::id());
        });
    }
    public function project():BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }
}
