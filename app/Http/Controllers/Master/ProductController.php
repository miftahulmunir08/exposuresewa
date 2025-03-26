<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['menu_active'] = 'product';
        return view('master/product', $data);
    }

    public function getData()
    {
        $product = Product::with(['category'])->select(['id', 'uuid', 'name', 'category_id', 'harga']);


        return DataTables::of($product)
            ->addColumn('no', function () {
                return 'DT_RowIndex';
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->filterColumn('category', function ($query, $keyword) {
                $query->whereHas('category', function ($q) use ($keyword) {
                    $q->where('name', 'like', "%{$keyword}%");
                });
            })
            ->addColumn('action', function ($product) {
                return '
                <a onclick="byid(`' . $product->uuid . '`)" href="#" class="btn btn-sm btn-primary">Edit</a>
                <a onclick="destroy(`' . $product->uuid . '`)" href="#" class="btn btn-sm btn-danger">Delete</a>
                ';
            })
            ->rawColumns(['action']) // Jika ada kolom HTML
            ->addIndexColumn()
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required',
            'product_category' => 'required',
            'product_price' => 'required',
        ]);

        $id = \Illuminate\Support\Str::uuid()->toString();

        $data['uuid']      = $id;
        $data['name']      = $request->product_name;
        $data['category_id'] = $request->product_category;
        $data['kode_barang'] = Product::generateKodeBarang(); // Auto-generate
        $data['status'] = 'tersedia'; // Auto-generate
        $data['harga'] = $request->product_price; // Auto-generate

        $product = Product::create($data);

        if (!$product) {
            return response()->json([
                'message' => '404',
                'error' => 'Insert Product Failed',
            ], 404);
        }

        return response()->json([
            'message' => '200',
            'data' => $product,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::where(['uuid' => $id])->first();
        if (!$product) {
            return response()->json([
                'message' => '404',
                'error' => 'Product not found',
            ], 404);
        }

        return response()->json([
            'message' => '200',
            'data' => $product,
        ], 200);
    }

    public function getAll()
    {
        $products = Product::all();
        return response()->json(['data' => $products]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'product_name' => 'required',
            'product_category' => 'required',
            'product_price' => 'required',
        ]);

        // Find category or return 404
        $customer = Product::findOrFail($id);

        $data['uuid']      = $id;
        $data['name']      = $request->product_name;
        $data['category_id'] = $request->product_category;
        $data['harga'] = $request->product_price; // Auto-generate


        // Update the category
        $customer->update($data);

        if (!$customer) {
            return response()->json([
                'message' => '404',
                'error' => 'Update Customer Failed',
            ], 404);
        }

        return response()->json([
            'message' => '200',
            'data' => $customer,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $customer = Product::findOrFail($id);

        // Delete the category
        $customer->delete();

        return response()->json(['message' => 'Product deleted successfully']);
    }
}
