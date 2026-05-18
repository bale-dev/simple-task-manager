<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Task extends Model
{
    protected $fillable = ['name', 'priority', 'project_id'];

    protected $casts = [
        'priority' => 'integer',
        'project_id' => 'integer',
    ];

    protected static function booted(): void
    {
        static::addGlobalScope('ordered', function (Builder $builder) {
            $builder->orderBy('priority');
        });
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }
}
