<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\TransactionCart;
use Illuminate\Http\Request;
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


    public function getData(Request $request)
    {
        $cart = TransactionCart::select(['id', 'transaction_code', 'user_id', 'product_id', 'qty', 'price'])->where('transaction_code', 'like', '%' . $request->transaction_code . '%');

        return DataTables::of($cart)
            ->addColumn('no', function () {
                return 'DT_RowIndex';
            })
            ->addColumn('user_id', function ($cart) {
                return $cart->user_id;
            })
            ->addColumn('product_id', function ($cart) {
                return $cart->product_id;
            })
            ->addColumn('qty', function ($cart) {
                return $cart->qty;
            })
            ->addColumn('price', function ($cart) {
                return $cart->price;
            })
            ->addColumn('action', function ($cart) {
                return '
                <a onclick="byid(`' . $cart->id . '`)" href="#" class="btn btn-sm btn-primary mt-1">Edit</a>
                <a onclick="destroy(`' . $cart->id . '`)" href="#" class="btn btn-sm btn-danger mt-1">Delete</a>
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
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
