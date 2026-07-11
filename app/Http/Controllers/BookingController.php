<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Attraction;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Guide;
use App\Models\Hotel;
use App\Models\Notification;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BookingController extends Controller
{
    /**
     * Resolve the booking target entity and extract its name and price.
     */
    private function resolveTarget(string $type, int $id): array
    {
        return match($type) {
            'Guide' => (function () use ($id) {
                $guide = Guide::with('user')->findOrFail($id);
                return ['name' => $guide->user?->full_name ?? 'Guide', 'price' => $guide->hourly_rate, 'entity' => $guide];
            })(),
            'Hotel' => (function () use ($id) {
                $hotel = Hotel::findOrFail($id);
                return ['name' => $hotel->name, 'price' => $hotel->price_per_night, 'entity' => $hotel];
            })(),
            'Restaurant' => (function () use ($id) {
                $rest = Restaurant::findOrFail($id);
                return ['name' => $rest->name, 'price' => $rest->avg_price, 'entity' => $rest];
            })(),
            'Attraction' => (function () use ($id) {
                $a = Attraction::findOrFail($id);
                return ['name' => $a->name, 'price' => $a->ticket_price, 'entity' => $a];
            })(),
            'Event' => (function () use ($id) {
                $e = Event::findOrFail($id);
                return ['name' => $e->name, 'price' => $e->ticket_price, 'entity' => $e];
            })(),
            default => abort(404, 'Unknown booking type.'),
        };
    }

    public function create(string $type, int $id)
    {
        $target = $this->resolveTarget($type, $id);
        return view('bookings.create', [
            'booking_type'  => $type,
            'target_id'     => $id,
            'target'        => $target['entity'],
            'target_name'   => $target['name'],
            'base_price'    => $target['price'],
        ]);
    }

    public function store(Request $request, string $type, int $id)
    {
        $request->validate([
            'start_date' => 'required|date|after:today',
            'end_date'   => 'nullable|date|after_or_equal:start_date',
            'guests'     => 'required|integer|min:1|max:50',
        ]);

        $target = $this->resolveTarget($type, $id);
        $days   = 1;
        if ($request->end_date) {
            $days = max(1, \Carbon\Carbon::parse($request->start_date)->diffInDays(\Carbon\Carbon::parse($request->end_date)) + 1);
        }
        $total = $target['price'] * $request->guests * $days;

        $booking = Booking::create([
            'reference_code'  => Helpers::generateReference(),
            'user_id'         => Auth::id(),
            'booking_type'    => $type,
            'target_id'       => $id,
            'target_name'     => $target['name'],
            'start_date'      => $request->start_date,
            'end_date'        => $request->end_date,
            'guests'          => $request->guests,
            'total_price'     => $total,
            'booking_status'  => 'Pending',
            'special_requests'=> $request->special_requests,
        ]);

        // ✅ Log initial status
        $booking->logStatus('Pending', 'Booking created.');

        Helpers::awardPoints(Auth::user(), 20, 'Making a booking');
        Helpers::logAction('create_booking', 'Booking', $booking->id, "Booking {$booking->reference_code} for {$type}");

        Notification::create([
            'user_id' => Auth::id(),
            'title'   => 'Booking Received',
            'message' => "Your booking ({$booking->reference_code}) for {$target['name']} is pending admin approval.",
            'type'    => 'booking',
        ]);

        return redirect()->route('dashboard.index')->with('success', "Booking {$booking->reference_code} submitted! Awaiting admin approval.");
    }

    public function cancel(Request $request, int $id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);
        if (!in_array($booking->booking_status, ['Pending', 'AdminApproved'])) {
            return back()->with('danger', 'This booking cannot be cancelled.');
        }
        $booking->update(['booking_status' => 'Cancelled']);
        $booking->logStatus('Cancelled');
        Helpers::logAction('cancel_booking', 'Booking', $id, "Booking {$booking->reference_code} cancelled by user");
        return back()->with('success', 'Booking cancelled.');
    }

    public function targetConfirm(Request $request, int $id)
    {
        $booking = Booking::findOrFail($id);
        $this->authorizeTargetAction($booking);

        // Vendor can only act if admin approved it first
        if ($booking->booking_status !== 'AdminApproved') {
            return back()->with('danger', 'Booking must be approved by admin first.');
        }

        $booking->update([
            'booking_status' => 'VendorAccepted',
            'vendor_comment' => $request->vendor_comment,
        ]);
        $booking->logStatus('VendorAccepted', $request->vendor_comment);

        Notification::create([
            'user_id' => $booking->user_id,
            'title'   => 'Booking Confirmed by Vendor',
            'message' => "Your booking {$booking->reference_code} has been accepted by the vendor.",
            'type'    => 'booking',
        ]);

        return back()->with('success', 'Booking accepted.');
    }

    public function targetReject(Request $request, int $id)
    {
        $booking = Booking::findOrFail($id);
        $this->authorizeTargetAction($booking);

        if ($booking->booking_status !== 'AdminApproved') {
            return back()->with('danger', 'Booking must be approved by admin first.');
        }

        $booking->update([
            'booking_status' => 'VendorRejected',
            'vendor_comment' => $request->vendor_comment,
        ]);
        $booking->logStatus('VendorRejected', $request->vendor_comment);

        Notification::create([
            'user_id' => $booking->user_id,
            'title'   => 'Booking Rejected by Vendor',
            'message' => "Your booking {$booking->reference_code} has been rejected by the vendor. Reason: " . $request->vendor_comment,
            'type'    => 'booking',
        ]);

        return back()->with('success', 'Booking rejected.');
    }

    public function complete(Request $request, int $id)
    {
        $booking = Booking::findOrFail($id);
        $this->authorizeTargetAction($booking);

        if ($booking->booking_status !== 'VendorAccepted') {
            return back()->with('danger', 'Only accepted bookings can be marked complete.');
        }

        $booking->update(['booking_status' => 'Completed', 'points_awarded' => true]);
        $booking->logStatus('Completed');

        if ($booking->user) {
            Helpers::awardPoints($booking->user, 30, 'Tour completed');
        }
        Helpers::logAction('complete_booking', 'Booking', $id, "Booking {$booking->reference_code} completed");
        return back()->with('success', 'Booking marked complete. Heritage points awarded!');
    }

    /**
     * Ensure the authenticated user is either an admin or the owner of the
     * target entity being booked (guide/hotel/restaurant/event).  Aborts with 403
     * otherwise.
     */
    private function authorizeTargetAction(Booking $booking): void
    {
        $user = Auth::user();

        // Admins can always act
        if (in_array($user->role, ['Agency Admin', 'Super Admin'])) {
            return;
        }

        // Check entity ownership based on booking type
        $owned = match ($booking->booking_type) {
            'Guide'      => Guide::where('id', $booking->target_id)
                                 ->where('user_id', $user->id)
                                 ->exists(),
            'Hotel'      => Hotel::where('id', $booking->target_id)
                                 ->where('user_id', $user->id)
                                 ->exists(),
            'Restaurant' => Restaurant::where('id', $booking->target_id)
                                      ->where('user_id', $user->id)
                                      ->exists(),
            'Event'      => Event::where('id', $booking->target_id)
                                 ->where('user_id', $user->id)
                                 ->exists(),
            default      => false,
        };

        if (!$owned) {
            abort(403, 'You are not authorised to manage this booking.');
        }
    }
}