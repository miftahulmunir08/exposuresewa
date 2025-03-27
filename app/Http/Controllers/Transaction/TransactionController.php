<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['menu_active'] = 'transaksi';
        return view('transaction/index', $data);
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
        $transaction = Transaction::select(['id', 'transaction_code', 'customer_id', 'tanggal_pinjam', 'tanggal_kembali', 'status']);


        return DataTables::of($transaction)
            ->addColumn('no', function () {
                return 'DT_RowIndex';
            })
            ->filterColumn('transaction_code', function ($query, $keyword) {
                $query->where('transaction_code', 'like', "%{$keyword}%");
            })
            ->addColumn('customer_id', function ($transaction) {
                return $transaction->customer_id;
            })
            ->addColumn('tanggal_pinjam', function ($transaction) {
                return $transaction->tanggal_pinjam;
            })
            ->addColumn('tanggal_kembali', function ($transaction) {
                return $transaction->tanggal_kembali;
            })
            ->addColumn('status', function ($row) {
                $status = strtolower($row->status); // Pastikan lowercase untuk pencocokan warna
                $colors = [
                    'dipinjam' => 'warning',
                    'dikembalikan' => 'success',
                    'unfinish' => 'danger',
                ];
                $color = $colors[$status] ?? 'secondary'; // Default jika tidak cocok

                return '<span class="text-white badge bg-' . $color . '">' . ucfirst($row->status) . '</span>';
            })
            ->addColumn('action', function ($transaction) {
                return '
                <a href="' . route('transaction.detail', $transaction->transaction_code) . '" class="btn btn-sm btn-primary mt-1">Cart</a>
                <a onclick="byid(`' . $transaction->id . '`)" href="#" class="btn btn-sm btn-primary mt-1">Edit</a>
                <a onclick="destroy(`' . $transaction->id . '`)" href="#" class="btn btn-sm btn-danger mt-1">Delete</a>
                ';
            })
            ->rawColumns(['action', 'status']) // Jika ada kolom HTML
            ->addIndexColumn()
            ->make(true);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer' => 'required',
            'tanggal_pinjam' => 'required',
            'tanggal_kembali' => 'required',
        ]);

        $data['transaction_code']      = Transaction::generateTransactionCode();
        $data['customer_id'] = $request->customer;
        $data['tanggal_pinjam'] = $request->tanggal_pinjam;
        $data['tanggal_kembali'] = $request->tanggal_kembali;
        $data['status'] = 'unfinish'; // Auto-generate

        $transaction = Transaction::create($data);

        if (!$transaction) {
            return response()->json([
                'message' => '404',
                'error' => 'Create Transaction Failed',
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

    public function detail($transaction_code)
    {
        $data['menu_active'] = 'transaksi';
        $data['transaction_code'] = $transaction_code;
        return view('transaction/detail', $data);
    }


    public function show(string $id)
    {
        $transaction = Transaction::where(['id' => $id])->first();
        if (!$transaction) {
            return response()->json([
                'message' => '404',
                'error' => 'Transaction Log not found',
            ], 404);
        }

        return response()->json([
            'message' => '200',
            'data' => $transaction,
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
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $transaction = Transaction::findOrFail($id);

        // Delete the category
        $transaction->delete();

        return response()->json(['message' => 'Transaction deleted successfully']);
    }
}
