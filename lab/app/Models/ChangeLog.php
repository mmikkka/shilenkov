<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ChangeLog extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [
        'entity_type',
        'entity_id',
        'created_by',
        'rollbacked_by',
        'action',
        'before',
        'after',
        'is_rollbacked',
        'rollbacked_at',
    ];
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'before' => 'array',
        'after' => 'array',
        'is_rollbacked' => 'boolean',
        'rollbacked_at' => 'datetime',
    ];
    /**
     * The relationships that should always be loaded.
     *
     * @var array<string>
     */
    protected $with = ['creator', 'rollbacker'];

    /**
     * Get the user who performed the action.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by')->withDefault([
            'name' => 'Смешарик',
        ]);
    }

    public function rollbacker(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rollbacked_by');
    }

    /**
     * Get the related entity.
     */
    public function entity(): MorphTo
    {
        return $this->morphTo('entity', 'entity_type', 'entity_id')->withTrashed();
    }
}
