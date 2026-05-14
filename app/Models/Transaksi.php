<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Transaksi extends Model
{
    protected $table      = 'transaksi';
    protected $primaryKey = 'id_transaksi';
    protected $fillable = [
        'customer_id',
        'nama_customer',
        'no_meja',
        'total',
        'pajak',
        'grand_total',
        'status',
        'catatan',
        'eco_packaging',
    ];

    protected $casts = [
        'total'         => 'integer',
        'pajak'         => 'integer',
        'grand_total'   => 'integer',
        'eco_packaging' => 'boolean',
        'created_at'    => 'datetime',
        'updated_at'    => 'datetime',
    ];
    public function customer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'customer_id', 'id_user');
    }
    public function details(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_transaksi', 'id_transaksi');
    }
    public function scopeSelesai(Builder $query): Builder
    {
        return $query->where('status', 'selesai');
    }
    public function scopePeriode(Builder $query, string $dari, string $sampai): Builder
    {
        return $query->whereDate('created_at', '>=', $dari)
                     ->whereDate('created_at', '<=', $sampai);
    }
    public static function hitungPajak(int $subtotal, int $persen = 3): int
    {
        return (int) round($subtotal * ($persen / 100));
    }
}