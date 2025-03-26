<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['menu_active'] = 'customer';
        return view('master/customer', $data);
    }

    /**
     * Show the form for creating a new resource.
     */

    public function getData()
    {
        $customer = Customer::select(['id', 'uuid', 'name', 'email', 'phone']);

        return DataTables::of($customer)
            ->addColumn('no', function () {
                return 'DT_RowIndex';
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->filterColumn('email', function ($query, $keyword) {
                $query->where('email', 'like', "%{$keyword}%");
            })
            ->filterColumn('phone', function ($query, $keyword) {
                $query->where('phone', 'like', "%{$keyword}%");
            })
            ->addColumn('action', function ($customer) {
                return '
                <a onclick="byid(`' . $customer->uuid . '`)" href="#" class="btn btn-sm btn-primary">Edit</a>
                <a onclick="destroy(`' . $customer->uuid . '`)" href="#" class="btn btn-sm btn-danger">Delete</a>
                ';
            })
            ->rawColumns(['action']) // Jika ada kolom HTML
            ->addIndexColumn()
            ->make(true);
    }

    public function getAll()
    {
        $customer = Customer::all();
        return response()->json(['data' => $customer]);
    }

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
            'customer_name' => 'required|alpha:ascii',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|numeric',
        ]);

        $id = \Illuminate\Support\Str::uuid()->toString();

        $data['uuid']      = $id;
        $data['name']      = $request->customer_name;
        $data['email']      = $request->customer_email;
        $data['phone']      = $request->customer_phone;


        $customer = Customer::create($data);

        if (!$customer) {
            return response()->json([
                'message' => '404',
                'error' => 'Insert Customer Failed',
            ], 404);
        }

        return response()->json([
            'message' => '200',
            'data' => $customer,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $customer = Customer::where(['uuid' => $id])->first();
        if (!$customer) {
            return response()->json([
                'message' => '404',
                'error' => 'Customer not found',
            ], 404);
        }

        return response()->json([
            'message' => '200',
            'data' => $customer,
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
        $request->validate([
            'customer_name' => 'required|alpha:ascii',
            'customer_email' => 'required|email',
            'customer_phone' => 'required|numeric',
        ]);

        // Find category or return 404
        $customer = Customer::findOrFail($id);

        $data['uuid']      = $id;
        $data['name']      = $request->customer_name;
        $data['email']      = $request->customer_email;
        $data['phone']      = $request->customer_phone;


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

        // Return the updated category
        // return $customer;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $customer = Customer::findOrFail($id);

        // Delete the category
        $customer->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
