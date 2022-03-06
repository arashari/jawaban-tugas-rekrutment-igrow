<?php

namespace App;

use App\Models\Pembiayaan;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redis;

class Helpers
{
    public static function generate_pembiayaan_code()
    {
        // https://stackoverflow.com/a/31107425
        /**
         * Generate a random string, using a cryptographically secure
         * pseudorandom number generator (random_int)
         *
         * This function uses type hints now (PHP 7+ only), but it was originally
         * written for PHP 5 as well.
         *
         * For PHP 7, random_int is a PHP core function
         * For PHP 5.x, depends on https://github.com/paragonie/random_compat
         *
         * @param int $length      How many characters do we want?
         * @param string $keyspace A string of all possible characters
         *                         to select from
         * @return string
         */
        function random_str(
            int $length = 64,
            string $keyspace = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
        ): string {
            if ($length < 1) {
                throw new \RangeException("Length must be a positive integer");
            }
            $pieces = [];
            $max = mb_strlen($keyspace, '8bit') - 1;
            for ($i = 0; $i < $length; ++$i) {
                $pieces[] = $keyspace[random_int(0, $max)];
            }
            return implode('', $pieces);
        }

        return random_str(5, "ABCDEFGHIJKLMNOPQRSTUVWXYZ");
    }

    public static function calculate_end_date($start_date, $tenor) {
        $date = Carbon::createFromFormat("Y-m-d", $start_date);
        $date->addMonths($tenor);

        return $date->format("Y-m-d");
    }

    public static function calculate_rencana_pembayaran(Pembiayaan $pembiayaan) {
        $date = Carbon::createFromFormat("Y-m-d", $pembiayaan->start_date);

        $rows = [];

        array_push($rows, [
            "payment_date" => $date->format("Y-m-d"),
            "pokok" => 0,
            "margin" => 0
        ]);

        $pokok_amount = (int) ($pembiayaan->plafond / ($pembiayaan->tenor / $pembiayaan->pi_pokok));
        $margin_amount = (int) (($pembiayaan->plafond * $pembiayaan->mpt / 100) / ($pembiayaan->tenor / $pembiayaan->pi_margin));

        if ($pembiayaan->pi_pokok == $pembiayaan->pi_margin) {
            for ($i = 0; $i < $pembiayaan->tenor; $i = $i + $pembiayaan->pi_pokok) {
                $date->addMonths($pembiayaan->pi_pokok);

                array_push($rows, [
                    "payment_date" => $date->format("Y-m-d"),
                    "pokok" => $pokok_amount,
                    "margin" => $margin_amount
                ]);
            }
        } else {
            $_copy = $date->copy(); // untuk margin
            $_copy2 = $date->copy(); // untuk merge

            $_row_pokok = [];
            for ($i = 0; $i < $pembiayaan->tenor; $i = $i + $pembiayaan->pi_pokok) {
                $date->addMonths($pembiayaan->pi_pokok);

                $_row_pokok[$date->format("Y-m-d")] = $pokok_amount;
            }

            $_row_margin = [];
            for ($i = 0; $i < $pembiayaan->tenor; $i = $i + $pembiayaan->pi_margin) {
                $_copy->addMonths($pembiayaan->pi_margin);

                $_row_margin[$_copy->format("Y-m-d")] = $margin_amount;
            }

            // merge
            for ($i = 0; $i < $pembiayaan->tenor; $i = $i + 1) {
                $_copy2->addMonth();
                $_date = $_copy2->format("Y-m-d");

                if (!array_key_exists($_date, $_row_pokok) && !array_key_exists($_date, $_row_margin)) {
                    continue;
                }

                array_push($rows, [
                    "payment_date" => $_date,
                    "pokok" => array_key_exists($_date, $_row_pokok) ? $_row_pokok[$_date] : 0,
                    "margin" => array_key_exists($_date, $_row_margin) ? $_row_margin[$_date] : 0
                ]);
            }
        }

        return $rows;
    }

    public static function store_cache_pembiayaan_detail($code, $data)
    {
        $key = "pembiayaan:".$code;

        Redis::set($key, json_encode($data));
        Redis::expire($key, 60 * 60 * 24); // expire in 1 day
    }

    public static function get_cache_pembiayaan_detail($code)
    {
        $key = "pembiayaan:".$code;

        if (Redis::exists($key) == 0) {
            return [ 'isExists' => false, 'data' => null ];
        }

        return [ 'isExists' => true, 'data' => json_decode(Redis::get($key)) ];
    }
}
