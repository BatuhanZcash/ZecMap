<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BusinessJsonImport extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = [
        'original_filename',
        'total_items',
        'created_count',
        'skipped_count',
        'failed_count',
        'uploaded_by_user_id',
        'imported_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'total_items' => 'integer',
            'created_count' => 'integer',
            'skipped_count' => 'integer',
            'failed_count' => 'integer',
            'imported_at' => 'datetime',
        ];
    }

    public function uploadedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by_user_id');
    }

    public function businesses(): HasMany
    {
        return $this->hasMany(Business::class);
    }
}
