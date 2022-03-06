<?php

namespace App\Http\Controllers;

use App\Http\Requests\PembiayaanStoreRequest;

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

    public function show($id = null)
    {
        $pembiayaan = Pembiayaan::with('rencanaPembayaran')->where('code', $id)->first();

        if ($pembiayaan) {
            return response()->api(200, "Data ditemukan", $pembiayaan);
        } else {
            return response()->api(404, "Pembiayaan tidak ditemukan");
        }
    }
}
