<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Menu extends Model
{
    protected $table      = 'menu';
    protected $primaryKey = 'id_menu';
    protected $fillable = [
        'nama_menu',
        'harga',
        'stok',
        'id_kategori',
        'status_menu',
    ];

    protected $casts = [
        'harga'       => 'integer',
        'stok'        => 'integer',
        'id_kategori' => 'integer',
    ];

    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class, 'id_kategori', 'id_kategori');
    }

    public function detailTransaksi(): HasMany
    {
        return $this->hasMany(DetailTransaksi::class, 'id_menu', 'id_menu');
    }

    public function foodWasteLogs(): HasMany
    {
        return $this->hasMany(FoodWasteLog::class, 'id_menu', 'id_menu');
    }

    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status_menu', 'aktif');
    }

    public function scopeTersedia(Builder $query): Builder
    {
        return $query->where('status_menu', 'aktif')->where('stok', '>', 0);
    }

    public function kurangiStok(int $qty): void
    {
        $this->refresh();
        if ($this->stok < $qty) {
            throw new \Exception(
                "Stok '{$this->nama_menu}' tidak cukup. " .
                "Tersedia: {$this->stok}, Dibutuhkan: {$qty}"
            );
        }
        $this->decrement('stok', $qty);
    }
}