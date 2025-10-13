<?php

namespace App\Http\Middleware;

use App\Models\TableQrCode;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyQrCode
{
    public function handle(Request $request, Closure $next): Response
    {
        $qrCodeValue = $request->route('qrCode');

        if (!$qrCodeValue || !is_string($qrCodeValue)) {
            abort(404);
        }

        $store = $request->store;

        $qrCode = TableQrCode::with('table.store')
            ->where('qr_code', $qrCodeValue)
            ->first();

        if (!$qrCode || !$qrCode->isValid()) {
            abort(404);
        }

        // Make the QR code available to downstream handlers if needed
        $request->merge(['table' => $qrCode->table]);

        return $next($request);
    }
}
