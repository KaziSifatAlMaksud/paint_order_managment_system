<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products =  Product::all();
        return view('admin.products.index', ['products' => $products]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $product = new Product();
        return view('admin.products.add', ['product' => $product]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $product = new Product();
        $data = $request->only($product->getFillable());
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageName = rand(111, 9999999) . $image->getClientOriginalName();
            $image->move(public_path('/uploads/'), $imageName);
            $data['img'] = $imageName;
        }
        $product->fill($data)->save();
        Session::flash('message', 'Product Added successfully');
        Session::flash('alert-class', 'alert-success');
        return redirect()->route("admins.product.index");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        return view('admin.products.add', ['product' => $product]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $data = $request->only($product->getFillable());
        if ($request->hasFile('img')) {
            $image = $request->file('img');
            $imageName = rand(111, 9999999) . $image->getClientOriginalName();
            $image->move(public_path('/uploads/'), $imageName);
            $data['img'] = $imageName;
        }
        $product->fill($data)->save();
        Session::flash('message', 'Product updated successfully');
        Session::flash('alert-class', 'alert-success');
        return redirect()->route("admins.product.index");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        if (file_exists(public_path('/uploads/') . $product->img)) {
            @unlink(public_path('/uploads/') . $product->img);
        }
        $product->delete();
        Session::flash('message', 'Product Delete successfully');
        Session::flash('alert-class', 'alert-success');
        return redirect()->route("admins.product.index");
    }
}
