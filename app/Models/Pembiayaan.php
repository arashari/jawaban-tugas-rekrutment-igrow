<?php

namespace App\Models;

use App\Helpers;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pembiayaan extends Model
{
    use HasFactory;

    protected $guarded = ['id', 'created_at', 'updated_at'];
    protected $hidden = ['id', 'created_at', 'updated_at'];

    public static function createPembiayaanFromRequest(array $attrs)
    {
        try {
            DB::beginTransaction();

            $pembiayaan = self::create(array_merge($attrs, [
                'code' => Helpers::generate_pembiayaan_code(),
                'end_date' => Helpers::calculate_end_date($attrs['start_date'], $attrs['tenor'])
            ]));

            $pembiayaan->rencanaPembayaran()->createMany(Helpers::calculate_rencana_pembayaran($pembiayaan));

            DB::commit();

            return $pembiayaan;
        } catch (\Exception $e) {
            DB::rollback();

            throw $e;
        }
    }

    public function rencanaPembayaran()
    {
        return $this->hasMany(RencanaPembayaran::class);
    }
}
