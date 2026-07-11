<?php

namespace App\Http\Controllers;

use App\Helpers\Helpers;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'target_type' => 'required|in:Attraction,Guide,Hotel,Restaurant,Event',
            'target_id'   => 'required|integer',
            'rating'      => 'required|integer|min:1|max:5',
            'comment'     => 'nullable|string|max:1000',
            'title'       => 'nullable|string|max:160',
        ]);

        // One review per target per user – set pending approval
        Review::updateOrCreate(
            ['user_id' => Auth::id(), 'target_type' => $request->target_type, 'target_id' => $request->target_id],
            [
                'rating'      => $request->rating,
                'comment'     => $request->comment,
                'title'       => $request->title,
                'is_approved' => false, // ✅ pending admin approval
            ]
        );

        Helpers::awardPoints(Auth::user(), 10, 'Writing a review');
        Helpers::logAction('create_review', $request->target_type, $request->target_id);

        return back()->with('success', 'Review submitted! +10 Heritage Points earned. Your review will be visible after admin approval.');
    }
}