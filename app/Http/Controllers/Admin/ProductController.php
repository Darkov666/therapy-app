<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Product::query()->with(['category', 'user']);

        // Filtering
        if ($request->search) {
            $query->where('title', 'like', "%{$request->search}%");
        }

        if ($request->category) {
            $query->where('category_id', $request->category);
        }

        // Role-based Scoping
        if ($user->role === 'psychologist' || $user->role === 'titular') {
            $query->where('user_id', $user->id);
        }

        $products = $query->latest()->paginate(10)->withQueryString();

        return Inertia::render('Admin/Products/Index', [
            'products' => $products,
            'filters' => $request->only(['search', 'category']),
        ]);
    }

    public function create()
    {
        $categories = \App\Models\Category::where('type', 'product')
            ->where('is_active', true)
            ->get();

        return Inertia::render('Admin/Products/Create', [
            'categories' => $categories
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_mxn' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|string|max:50',
            'cover_image' => 'nullable|image|max:2048', // Public
            'product_file' => 'nullable|file|max:51200', // Private (50MB)
            'is_active' => 'boolean',
        ]);

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $coverPath = $request->file('cover_image')->store('products/covers', 'public');
        }

        $filePath = null;
        if ($request->hasFile('product_file')) {
            // Store in private disk (local or R2)
            $disk = config('filesystems.default') === 'r2' ? 'r2' : 'local';
            $pathPrefix = $disk === 'local' ? 'private/products' : 'products';

            $filePath = $request->file('product_file')->store($pathPrefix, $disk);
        }

        Product::create([
            'user_id' => auth()->id(),
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'],
            'price_mxn' => $validated['price_mxn'],
            'type' => $validated['type'],
            'is_active' => $validated['is_active'] ?? true,
            'cover_path' => $coverPath,
            'file_path' => $filePath,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(Product $product)
    {
        $this->authorizeAction($product);
        $categories = \App\Models\Category::where('type', 'product')
            ->where('is_active', true)
            ->get();

        return Inertia::render('Admin/Products/Edit', [
            'product' => $product,
            'categories' => $categories
        ]);
    }

    public function update(Request $request, Product $product)
    {
        $this->authorizeAction($product);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price_mxn' => 'required|numeric|min:0',
            'category_id' => 'required|exists:categories,id',
            'type' => 'required|string|max:50',
            'cover_image' => 'nullable|image|max:2048',
            'product_file' => 'nullable|file|max:51200',
            'is_active' => 'boolean',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($product->cover_path) {
                Storage::disk('public')->delete($product->cover_path);
            }
            $product->cover_path = $request->file('cover_image')->store('products/covers', 'public');
        }

        if ($request->hasFile('product_file')) {
            // Delete old file
            $disk = config('filesystems.default') === 'r2' ? 'r2' : 'local';
            if ($product->file_path) {
                Storage::disk($disk)->delete($product->file_path);
            }

            $pathPrefix = $disk === 'local' ? 'private/products' : 'products';
            $product->file_path = $request->file('product_file')->store($pathPrefix, $disk);
        }

        $product->update([
            'category_id' => $validated['category_id'],
            'title' => $validated['title'],
            'slug' => Str::slug($validated['title']),
            'description' => $validated['description'],
            'price_mxn' => $validated['price_mxn'],
            'type' => $validated['type'],
            'is_active' => $validated['is_active'],
            'cover_path' => $product->cover_path,
            'file_path' => $product->file_path,
        ]);

        return redirect()->route('admin.products.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        $this->authorizeAction($product);

        if ($product->cover_path) {
            Storage::disk('public')->delete($product->cover_path);
        }

        if ($product->file_path) {
            $disk = config('filesystems.default') === 'r2' ? 'r2' : 'local';
            Storage::disk($disk)->delete($product->file_path);
        }

        $product->delete();

        return redirect()->route('admin.products.index')->with('success', 'Producto eliminado correctamente.');
    }

    private function authorizeAction(Product $product)
    {
        $user = auth()->user();
        if ($user->role === 'admin')
            return true;

        if ($product->user_id !== $user->id) {
            abort(403, 'No tienes permiso para modificar este producto.');
        }
    }
}
