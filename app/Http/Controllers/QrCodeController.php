<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TableQrCode;
use Illuminate\Support\Facades\Session;

class QrCodeController
{
    public function redirect(Request $request, string $qrCode)
    {
        $qrCode = TableQrCode::where('qr_code', $qrCode)->with('table.store')->first();

        if (!$qrCode->isValid()) {
            abort(404);
        }

        Session::put([
            'current_table_id' => $qrCode->table->id,
            'current_table_number' => $qrCode->table->table_number,
        ]);

        return redirect()->route('menu.store.index', ['store' => $qrCode->table->store]);
    }
}
