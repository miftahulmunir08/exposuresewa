<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
        // dd('sudah disini');
        // if (Auth::user()) {
        //     return redirect()->route('cashier');
        // }
        return view('auth/login');
    }

    public function check_login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|min:4'
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors();
            dd($errors);
            // return redirect()->back()->withErrors($errors);
        } else {
            if (Auth::attempt($request->only('email', 'password'))) {
                $user = User::where('email', $request->email)->firstOrFail();
                // if ($user->is_active === '0') {
                //     $errors = "Your account still inactive. Please wait until you received approval";
                //     return redirect()->back()->withErrors($errors);
                // }
                // Buat token API menggunakan Sanctum
                $token = $user->createToken('api-token')->plainTextToken;

                // // Simpan token di session untuk digunakan dalam API request berikutnya
                $request->session()->put('api_token', $token);

                // // Regenerasi session ID untuk keamanan
                $request->session()->regenerate();

                // // Redirect ke halaman cashier atau sesuai kebutuhan
                return redirect()->route('dashboard');
            } else {
                $errors = "Kombinasi User dan Password tidak dapat ditemukan";
                return redirect()->back()->withErrors($errors);
            }
        }

        // return redirect()->route('dashboard');
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
