<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Supplier;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::latest()->paginate(10);
        return view('admin.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        return view('admin.suppliers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        Supplier::create($request->all());

        return redirect('/suppliers')->with('message', 'Supplier added successfully!');
    }

    public function edit(Supplier $supplier)
    {
        return view('admin.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, Supplier $supplier)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
        ]);

        $supplier->update($request->all());

        return redirect('/suppliers')->with('message', 'Supplier updated successfully!');
    }

    public function destroy(Supplier $supplier)
    {
        $supplier->delete();
        return redirect('/suppliers')->with('message', 'Supplier deleted successfully!');
    }

    public function show(Supplier $supplier)
{
    // Supplier ki sari purchases aur medicines load karna
    $purchases = $supplier->purchases()->with('medicine')->latest()->paginate(10);
    return view('admin.suppliers.show', compact('supplier', 'purchases'));
}
public function payableReport()
{
    $suppliers = Supplier::all();
    $totalPayable = $suppliers->sum('opening_balance');
    
    return view('admin.suppliers.payable-report', compact('suppliers', 'totalPayable'));
}
}