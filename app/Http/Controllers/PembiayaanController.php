<?php

namespace App\Http\Controllers;

use App\Helpers;
use App\Http\Requests\PembiayaanStoreRequest;
use Illuminate\Http\Request;

use App\Models\Pembiayaan;

class PembiayaanController extends Controller
{
    public function index()
    {
        $data = Pembiayaan::get()->all();
        return response()->api(200, "Data ditemukan", $data);
    }

    public function store(PembiayaanStoreRequest $request)
    {
        $request->validated();

        $model = Pembiayaan::createPembiayaanFromRequest($request->all());
        return response()->api(201, "Pembiayaan berhasil dibuat dengan kode: " . $model->code, ["code" => $model->code]);
    }

    public function show(Request $request, $id = null)
    {
        if (!$request->hasAny('refresh')) { // skip pengecekan redis jika ada param "refresh"
            $res = Helpers::get_cache_pembiayaan_detail($id);
            if ($res['isExists']) {
                return response()->api(200, "Data ditemukan", $res['data']);
            }
        }

        $pembiayaan = Pembiayaan::with('rencanaPembayaran')->where('code', $id)->first();

        if ($pembiayaan) {
            Helpers::store_cache_pembiayaan_detail($id, $pembiayaan);

            return response()->api(200, "Data ditemukan", $pembiayaan);
        } else {
            return response()->api(404, "Pembiayaan tidak ditemukan");
        }
    }
}
