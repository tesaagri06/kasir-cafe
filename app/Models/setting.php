<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Setting extends Model
{
    protected $table        = 'settings';
    protected $primaryKey   = 'setting_key';
    protected $keyType      = 'string';
    public    $incrementing = false;

    protected $fillable = [
        'setting_key',
        'setting_value',
    ];
    public static function get(string $key, ?string $default = null): ?string
    {
        $row = static::find($key);
        return $row ? $row->setting_value : $default;
    }
    public static function set(string $key, ?string $value): void
    {
        static::updateOrCreate(
            ['setting_key'   => $key],
            ['setting_value' => $value]
        );
    }
    public static function getAll(): array
    {
        return static::all()
            ->pluck('setting_value', 'setting_key')
            ->toArray();
    }
}