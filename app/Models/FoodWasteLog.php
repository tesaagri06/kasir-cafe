<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class FoodWasteLog extends Model
{
    protected $table = 'food_waste_log';
    protected $fillable = [
        'id_menu',
        'jumlah',
        'alasan',
        'catatan',
    ];
    protected $casts = [
        'jumlah'     => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function menu(): BelongsTo
    {
        return $this->belongsTo(Menu::class, 'id_menu', 'id_menu');
    }
    public function scopePeriode(Builder $query, string $dari, string $sampai): Builder
    {
        return $query->whereDate('created_at', '>=', $dari)
                     ->whereDate('created_at', '<=', $sampai);
    }
}