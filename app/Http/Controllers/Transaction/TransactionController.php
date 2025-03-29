<?php

namespace App\Http\Controllers\Transaction;

use App\Http\Controllers\Controller;
use App\Models\StockLog;
use App\Models\Transaction;
use App\Models\TransactionCart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $data['menu_active'] = 'transaksi';
        return view('transaction/create', $data);
    }

    public function getData()
    {
        $transaction = Transaction::with(['customer'])->select(['id', 'transaction_code', 'customer_id', 'tanggal_pinjam', 'tanggal_kembali', 'status']);


        return DataTables::of($transaction)
            ->addColumn('no', function () {
                return 'DT_RowIndex';
            })
            ->filterColumn('transaction_code', function ($query, $keyword) {
                $query->where('transaction_code', 'like', "%{$keyword}%");
            })
            ->addColumn('customer_id', function ($transaction) {
                return $transaction->customer->name;
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
                $buttons = '';

                if ($transaction->status == 'dipinjam') {
                    $buttons .= '<a onclick="byid(`' . $transaction->id . '`)" href="#" class="btn btn-sm btn-warning mt-1 mr-1">Dikembalikan</a>';
                }

                $buttons .= '<a href="' . route('transaction.detail', $transaction->transaction_code) . '" class="btn btn-sm btn-info mt-1">Detail</a>';
                $buttons .= '<a onclick="destroy(`' . $transaction->id . '`)" href="#" class="btn btn-sm btn-danger mt-1">Delete</a>';

                return $buttons;
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
            'start_date' => 'required',
            'end_date' => 'required',
        ]);

        $data['transaction_code'] = Transaction::generateTransactionCode();
        $data['customer_id'] = $request->customer;
        $data['tanggal_pinjam'] = $request->start_date;
        $data['tanggal_kembali'] = $request->end_date;
        $data['status'] = 'dipinjam'; // Auto-generate

        DB::beginTransaction();

        try {
            // Simpan transaksi baru
            $transaction = Transaction::create($data);

            // Update transaction_code di TransactionCart berdasarkan kode transaksi baru
            $carts = TransactionCart::whereNull('transaction_code')->get();
            TransactionCart::whereNull('transaction_code')
                ->update(['transaction_code' => $data['transaction_code']]);

            foreach ($carts as $cart) {
                StockLog::create([
                    'transaction_code' => $data['transaction_code'],
                    'product_id' => $cart->product_id,
                    'qty' => $cart->qty,
                    'status' => 'dipinjam' // Status otomatis 'dipinjam'
                ]);
            }

            $cart = TransactionCart::with(["product"])->select(['uuid', 'id', 'transaction_code', 'user_id', 'product_id', 'qty', 'price'])->where('transaction_code', NULL);
            // Commit transaksi jika semua berhasil
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Transaksi berhasil!',
                'transaction' => $transaction
            ]);
        } catch (\Exception $e) {
            // Rollback jika terjadi error
            DB::rollBack();

            return response()->json([
                'success' => false,
                'message' => 'Transaksi gagal!',
                'error' => $e->getMessage()
            ]);
        }
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
