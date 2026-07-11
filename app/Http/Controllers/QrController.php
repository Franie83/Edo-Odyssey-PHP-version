<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use Illuminate\Http\Request;

class QrController extends Controller
{
    public function scan(string $code)
    {
        $qr = QrCode::where('code', $code)->first();

        if (!$qr) {
            return view('errors.404', ['message' => 'QR code not found.']);
        }

        $qr->increment('scan_count');
        $qr->update(['last_scanned_at' => now()]);

        // Redirect to the appropriate entity page
        $route = match($qr->entity_type) {
            'Attraction' => route('attractions.detail', $qr->entity_id),
            'Hotel'      => route('hotels.detail', $qr->entity_id),
            'Restaurant' => route('restaurants.detail', $qr->entity_id),
            'Event'      => route('events.detail', $qr->entity_id),
            default      => route('main.home'),
        };

        return redirect($route)->with('success', 'QR Code scanned! Welcome.');
    }
}
