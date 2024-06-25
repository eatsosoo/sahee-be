<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Storage;

class QRCodeController extends Controller
{
    public function generate(Request $request)
    {
        // Validate the request data
        $request->validate([
            'total_amount' => 'required|numeric',
            'bank_name' => 'required|string',
        ]);

        // Get the data from the request
        $totalAmount = $request->input('total_amount');
        $bankName = $request->input('bank_name');

        // Prepare the QR code data
        $qrCodeData = "Total Amount: $totalAmount\nBank Name: $bankName";

        // Generate the QR code
        $qrCode = QrCode::format('png')->size(300)->generate($qrCodeData);

        // Save the QR code image to storage (optional)
        $fileName = 'qr_code.png';
        Storage::put("public/$fileName", $qrCode);

        // Return the QR code image as a response
        return response($qrCode, 200)
            ->header('Content-Type', 'image/png');
    }
}
