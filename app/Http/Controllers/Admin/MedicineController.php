<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Medicine;
use App\Models\MedicineBatch;
use App\Models\Category;

class MedicineController extends Controller
{
    public function index()
    {
        // Sab se latest medicines sab se uper show hon gi
        $medicines = Medicine::with('category', 'batches')->latest()->get();
        return view('admin.medicines.index', compact('medicines'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('admin.medicines.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $productType = $request->input('product_type', 'medicine');
        $hasExpiry = $request->boolean('has_expiry', $productType === 'medicine');

        $rules = [
            'product_type' => 'nullable|in:medicine,general',
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'batch_number' => 'nullable|string|max:255',
            'quantity' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'purchase_price' => 'required|numeric|min:0',
            'expiry_date' => $hasExpiry ? 'required|date' : 'nullable|date',
        ];

        $request->validate($rules);

        $medicine = Medicine::firstOrCreate(
            ['name' => trim($request->name)],
            [
                'category_id' => $request->category_id,
                'product_type' => $productType,
                'has_expiry' => $hasExpiry,
                'track_batches' => $request->boolean('track_batches', true),
                'dosage_unit' => $request->dosage_unit ?: ($productType === 'general' ? 'Piece' : 'Tablet'),
                'base_unit' => $request->base_unit ?: ($productType === 'general' ? 'Piece' : 'Tablet'),
                'unit_price' => $request->selling_price,
                'purchase_price' => $request->purchase_price,
            ]
        );

        MedicineBatch::create([
            'medicine_id' => $medicine->id,
            'batch_number' => $request->batch_number ?: (($productType === 'general' ? 'GEN-' : 'BATCH-') . rand(1000, 9999)),
            'quantity' => $request->quantity,
            'purchase_price' => $request->purchase_price,
            'selling_price' => $request->selling_price,
            'expiry_date' => $hasExpiry ? $request->expiry_date : null,
        ]);

        $label = $productType === 'general' ? 'General Product' : 'Medicine';
        return redirect('/medicines')->with('message', "{$label} Added Successfully!");
    }
}