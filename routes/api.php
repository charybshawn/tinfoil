use App\Models\Product;

Route::get('/products/{product}/variations', function (Product $product) {
    return response()->json(
        $product->variations()
            ->select(['id', 'name', 'price', 'unit_type', 'unit_value'])
            ->where('status', 'active')
            ->get()
    );
})->name('api.product.variations'); 