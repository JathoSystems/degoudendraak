<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class ReviewController extends Controller
{
    public function showForm($reviewCode)
    {
        // Decode the review code to get table ID and date
        try {
            $decodedData = base64_decode($reviewCode);
            list($tableId, $date) = explode('-', $decodedData);

            // Find the table
            $table = Table::find($tableId);

            if (!$table) {
                return view('review.error', ['message' => 'Tafel niet gevonden.']);
            }

            // Check if a review already exists
            $existingReview = Review::where('review_code', $reviewCode)->first();
            if ($existingReview) {
                return view('review.already-submitted', ['table' => $table, 'discount_code' => $existingReview->discount_code]);
            }

            // Show the review form
            return view('review.form', [
                'table' => $table,
                'reviewCode' => $reviewCode
            ]);

        } catch (\Exception $e) {
            return view('review.error', ['message' => 'Ongeldige QR code.']);
        }
    }

    public function submitReview(Request $request, $reviewCode)
    {
        // Validate the form data
        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'food_rating' => 'required|integer|min:1|max:5',
            'service_rating' => 'required|integer|min:1|max:5',
            'ambiance_rating' => 'required|integer|min:1|max:5',
            'overall_rating' => 'required|integer|min:1|max:5',
            'feedback' => 'nullable|string',
            'favorite_dish' => 'nullable|string|max:255',
            'improvement_area' => 'nullable|string|max:255',
            'would_return' => 'required|boolean',
            'heard_about_us' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            // Decode the review code
            $decodedData = base64_decode($reviewCode);
            list($tableId, $date) = explode('-', $decodedData);

            // Generate a unique discount code
            $discountCode = 'DRAAK' . strtoupper(Str::random(6));

            // Save the review
            $review = new Review();
            $review->review_code = $reviewCode;
            $review->table_id = $tableId;
            $review->name = $request->name;
            $review->email = $request->email;
            $review->food_rating = $request->food_rating;
            $review->service_rating = $request->service_rating;
            $review->ambiance_rating = $request->ambiance_rating;
            $review->overall_rating = $request->overall_rating;
            $review->feedback = $request->feedback;
            $review->favorite_dish_selected = !empty($request->favorite_dish);
            $review->favorite_dish = $request->favorite_dish;
            $review->improvement_area = $request->improvement_area;
            $review->would_return = $request->would_return;
            $review->heard_about_us = $request->heard_about_us;
            $review->discount_code = $discountCode;
            $review->save();

            // Show thank you page with discount code
            return view('review.thank-you', [
                'review' => $review
            ]);

        } catch (\Exception $e) {
            return view('review.error', ['message' => 'Er is een fout opgetreden bij het verwerken van uw beoordeling.']);
        }
    }
}
