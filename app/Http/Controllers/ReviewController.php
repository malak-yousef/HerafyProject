<?php

namespace App\Http\Controllers;

use App\Models\Review;
use App\Models\Craftman;
use App\Models\Customer;
use App\Models\Order;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $reviews = Review::with([
            'craftsman.user',
            'customer.user',
            'request'
        ])
            ->latest()
            ->paginate(10);

        return view('cms.review.index', compact('reviews'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $craftmen = Craftman::with('user')->get();
        $customers = Customer::with('user')->get();
        $requests = Order::with('customer.user')->get();

        return view('cms.review.create', compact(
            'craftmen',
            'customers',
            'requests'
        ));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
            'craftsman_id' => 'required|exists:craftmen,id',
            'customer_id' => 'required|exists:customers,id',
            'request_id' => 'required|exists:orders,id',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);

        Review::create($data);

        return redirect()
            ->route('reviews.index')
            ->with('success', 'تم إضافة التقييم بنجاح');
    }

    /**
     * Display the specified resource.
     */
    public function show(Review $review)
    {
        $review->load([
            'craftsman.user',
            'customer.user',
            'request'
        ]);

        return view('cms.review.show', compact('review'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Review $review)
    {
        $craftmen = Craftman::with('user')->get();
        $customers = Customer::with('user')->get();
        $requests = Order::with('customer.user')->get();

        return view('cms.review.edit', compact(
            'review',
            'craftmen',
            'customers',
            'requests'
        ));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Review $review)
    {
        $data = $request->validate([
            'rating' => 'required|integer|between:1,5',
            'comment' => 'nullable|string',
            'craftsman_id' => 'required|exists:craftmen,id',
            'customer_id' => 'required|exists:customers,id',
            'request_id' => 'required|exists:orders,id',
            'status' => 'required|in:pending,approved,rejected',
        ]);

        $review->update($data);

        return redirect()
            ->route('reviews.index')
            ->with('success', 'تم تعديل التقييم بنجاح');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Review::destroy($id);
    }

    public function trashed()
    {
        $reviews = Review::onlyTrashed()
            ->orderBy('deleted_at', 'desc')
            ->get();

        return response()->view('cms.review.trashed', compact('reviews'));
    }

    public function restore($id)
    {
        Review::onlyTrashed()->findOrFail($id)->restore();
        return back()->with('success', 'Success');
    }

    public function force($id)
    {
        Review::onlyTrashed()->findOrFail($id)->forceDelete();
        return back()->with('success', 'Success');
    }

    public function forceAll()
    {
        Review::onlyTrashed()->forceDelete();
        return back()->with('success', 'Success');
    }
}
