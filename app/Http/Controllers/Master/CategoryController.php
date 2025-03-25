<?php

namespace App\Http\Controllers\Master;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $data['menu_active'] = 'category';
        return view('master/category', $data);
    }


    public function getData()
    {
        $category = Category::select(['id', 'uuid', 'name']);

        return DataTables::of($category)
            ->addColumn('no', function () {
                return 'DT_RowIndex';
            })
            ->filterColumn('name', function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->addColumn('action', function ($category) {
                return '
                <a onclick="byid(`' . $category->uuid . '`)" href="#" class="btn btn-sm btn-primary">Edit</a>
                <a onclick="destroy(`' . $category->uuid . '`)" href="#" class="btn btn-sm btn-danger">Delete</a>
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
            'category_name' => 'required|alpha:ascii'
        ]);

        $id = \Illuminate\Support\Str::uuid()->toString();

        $data['uuid']      = $id;
        $data['name']      = $request->category_name;

        $category = Category::create($data);

        if (!$category) {
            return response()->json([
                'message' => '404',
                'error' => 'Insert Category Failed',
            ], 404);
        }

        return response()->json([
            'message' => '200',
            'data' => $category,
        ], 200);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id = null)
    {
        if ($id != null) {
            $category = Category::where('uuid', $id)->first();
            if (!$category) {
                return response()->json(['message' => 'Category not found'], 404);
            }
            return response()->json($category);
        }

        $categories = Category::all();
        return response()->json(['data' => $categories]);
    }

    public function getAll()
    {
        $categories = Category::all();
        return response()->json(['data' => $categories]);
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
            'category_name' => 'required|regex:/^[a-zA-Z\s-]+$/u',
        ]);

        // Find category or return 404
        $category = Category::findOrFail($id);

        $data['uuid']      = $id;
        $data['name']      = $request->category_name;

        // Update the category
        $category->update($data);

        if (!$category) {
            return response()->json([
                'message' => '404',
                'error' => 'Update Category Failed',
            ], 404);
        }

        return response()->json([
            'message' => '200',
            'data' => $category,
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
        $category = Category::findOrFail($id);

        // Delete the category
        $category->delete();

        return response()->json(['message' => 'Category deleted successfully']);
    }
}
