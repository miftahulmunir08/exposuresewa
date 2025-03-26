<?php

namespace App\Http\Controllers\Stock;

use App\Http\Controllers\Controller;
use App\Models\StockLog;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class StockController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['menu_active'] = 'stock';
        return view('stock/index', $data);
    }


    public function getData()
    {
        $product = StockLog::with(['product'])->select(['id', 'transaction_code', 'product_id', 'status', 'qty']);


        return DataTables::of($product)
            ->addColumn('no', function () {
                return 'DT_RowIndex';
            })
            ->filterColumn('product', function ($query, $keyword) {
                $query->where('product_id', 'like', "%{$keyword}%");
            })
            ->addColumn('qty', function ($product) {
                return $product->qty;
            })
            ->addColumn('status', function ($row) {
                $status = strtolower($row->status); // Pastikan lowercase untuk pencocokan warna
                $colors = [
                    'baru' => 'success',
                    'dipinjam' => 'warning',
                    'dikembalikan' => 'info',
                    'hilang' => 'dark',
                    'rusak' => 'secondary',
                ];
                $color = $colors[$status] ?? 'secondary'; // Default jika tidak cocok

                return '<span class="text-white badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('action', function ($product) {
                return '
                <a onclick="byid(`' . $product->id . '`)" href="#" class="btn btn-sm btn-primary">Edit</a>
                <a onclick="destroy(`' . $product->id . '`)" href="#" class="btn btn-sm btn-danger">Delete</a>
                ';
            })
            ->rawColumns(['action', 'status']) // Jika ada kolom HTML
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
            'product_qty' => 'required',
            'product_status' => 'required',
        ]);

        $data['product_id']      = $request->product_name;
        $data['qty'] = $request->product_qty;
        $data['status'] = $request->product_status; // Auto-generate

        $stock = StockLog::create($data);

        if (!$stock) {
            return response()->json([
                'message' => '404',
                'error' => 'Insert Stock Failed',
            ], 404);
        }

        return response()->json([
            'message' => '200',
            'data' => $stock,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $stocklog = StockLog::where(['id' => $id])->first();
        if (!$stocklog) {
            return response()->json([
                'message' => '404',
                'error' => 'Stock Log not found',
            ], 404);
        }

        return response()->json([
            'message' => '200',
            'data' => $stocklog,
        ], 200);
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
            'product_qty' => 'required',
            'product_status' => 'required',
        ]);

        // Find category or return 404
        $stock = StockLog::findOrFail($id);

        $data['product_id'] = $request->product_name;
        $data['qty'] = $request->product_qty;
        $data['status'] = $request->product_status; // Auto-generate

        // Update the category
        $stock->update($data);

        if (!$stock) {
            return response()->json([
                'message' => '404',
                'error' => 'Update Stock Log Failed',
            ], 404);
        }

        return response()->json([
            'message' => '200',
            'data' => $stock,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $stock = StockLog::findOrFail($id);

        // Delete the category
        $stock->delete();

        return response()->json(['message' => 'Stock deleted successfully']);
    }
}
