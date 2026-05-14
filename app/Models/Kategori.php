<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kategori extends Model
{
    protected $table      = 'kategori';
    protected $primaryKey = 'id_kategori';
    protected $fillable = [
        'nama_kategori',
        'deskripsi',
        'icon',
        'status',
    ];
    public function menu(): HasMany
    {
        return $this->hasMany(Menu::class, 'id_kategori', 'id_kategori');
    }
    public function menuAktif(): HasMany
    {
        return $this->hasMany(Menu::class, 'id_kategori', 'id_kategori')
                    ->where('status_menu', 'aktif')
                    ->where('stok', '>', 0);
    }
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('status', 'aktif');
    }
}