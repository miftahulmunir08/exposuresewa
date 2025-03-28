<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\TransactionCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\Facades\DataTables;

class TransactionCartController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    public function getData()
    {
        $cart = TransactionCart::with(["product"])->select(['uuid', 'id', 'transaction_code', 'user_id', 'product_id', 'qty', 'price'])->where('transaction_code', NULL);

        return DataTables::of($cart)
            ->addColumn('no', function () {
                return 'DT_RowIndex';
            })
            ->addColumn('user_id', function ($cart) {
                return $cart->user_id;
            })
            ->addColumn('product_id', function ($cart) {
                return $cart->product->name;
            })
            ->addColumn('qty', function ($cart) {
                return $cart->qty;
            })
            ->addColumn('price', function ($cart) {
                return convertDivider($cart->price);
            })
            ->addColumn('total', function ($cart) {

                return convertDivider($cart->price * $cart->qty);
            })
            ->addColumn('action', function ($cart) {
                return '
                <a onclick="byid(`' . $cart->uuid . '`)" href="#" class="btn btn-sm btn-primary mt-1">Edit</a>
                <a onclick="destroy(`' . $cart->uuid . '`)" href="#" class="btn btn-sm btn-danger mt-1">Delete</a>
                ';
            })
            ->rawColumns(['action']) // Jika ada kolom HTML
            ->addIndexColumn()
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_name' => 'required',
            'qty' => 'required',
        ]);

        $id = \Illuminate\Support\Str::uuid()->toString();

        $data['uuid']      = $id;
        $data['user_id']   = Auth::user()["uuid"];
        $data['transaction_code'] = $request->transaction_code;
        $data['product_id'] = $request->product_name;
        $data['qty'] = $request->qty;
        $price = Product::find($request->product_name);
        $data['price'] = $price->harga;

        $transaction = TransactionCart::create($data);

        if (!$transaction) {
            return response()->json([
                'message' => '404',
                'error' => 'Create Transaction Cart Failed',
            ], 404);
        }

        return response()->json([
            'message' => '200',
            'data' => $transaction,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $transaction_cart = TransactionCart::where(['uuid' => $id])->first();
        if (!$transaction_cart) {
            return response()->json([
                'message' => '404',
                'error' => 'Transaction Cart not found',
            ], 404);
        }

        return response()->json([
            'message' => '200',
            'data' => $transaction_cart,
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
            'qty' => 'required',
        ]);


        // Find category or return 404
        $stock = TransactionCart::findOrFail($id);

        $data['product_id'] = $request->product_name;
        $data['qty'] = $request->qty;
        $price = Product::find($request->product_name);
        $data['price'] = $price->harga;


        // Update the category
        $stock->update($data);

        if (!$stock) {
            return response()->json([
                'message' => '404',
                'error' => 'Update Cart Failed',
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
        $transaction_cart = TransactionCart::findOrFail($id);

        // Delete the category
        $transaction_cart->delete();

        return response()->json(['message' => 'Transaction Cart deleted successfully']);
    }

    public function getTotalCart()
    {
        $total = TransactionCart::whereNull('transaction_code')
            ->sum(DB::raw('qty * price'));

        return response()->json([
            'message' => '200',
            'data' => $total,
        ], 200);
    }
}
