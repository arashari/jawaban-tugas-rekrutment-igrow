<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Helpers;

class Pembiayaan extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    public static function createPembiayaanFromRequest(array $attrs)
    {
        return self::create(array_merge($attrs, [
            'code' => Helpers::generate_pembiayaan_code(),
            'end_date' => Helpers::calculate_end_date($attrs['start_date'], $attrs['tenor'])
        ]));
    }
}
