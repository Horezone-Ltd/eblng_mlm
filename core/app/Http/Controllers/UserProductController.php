<?php

namespace App\Http\Controllers;

use App\Http\Utils\CartService;
use App\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class UserProductController extends Controller
{
    public $productModel;
    public $cartService;
    public function __construct(Product $product, CartService $cartService)
    {
        $this->productModel = $product;
        $this->cartService = $cartService;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = $this->productModel->where('status', 1)->orderBy('id', 'desc')->get();
        $cartTotal=0;
        $balance = 0;
        if (auth()->check()) {
            $balance = auth()->user()->balance;
            $cart = $this->cartService->cart();
            $map = [];
            $mapKeys = [];
            $cart->cartItems()->get()->each(function ($item) use(&$map, &$mapKeys, &$cartTotal) {
                $cartTotal+= ceil($item->quantity * $item->price);
                $map[$item->product_id] = $item->quantity;
                $mapKeys[] = $item->product_id;
            });
            $products = $products->map(function ($item) use ($mapKeys, $map) {
                $item->quantity = 0;
                if(in_array($item->id, $mapKeys)) {
                    $item->quantity = $map[$item->id];
                }
                $item->cartPrice = ceil(($item->quantity?$item->quantity: 1) * $item->price);
                return $item;
            });
        } else {
            $products = $products->map(function ($item) {
                $item->quantity = 0;
                $item->cartPrice = ceil($item->price);
                return $item;
            });
        }

        $page_title = "Products";
        return view('products.index', compact('page_title', 'products', 'cartTotal', 'balance'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getSingleProduct($id)
    {
        $product = $this->productModel->findOrFail($id);
        $page_title = $product->name;
        return view('products.single-product', compact('page_title', 'product'));
    }


    public function handleCartUpdate(Request $request) {
        if (!auth()->check()) {
            $notify[] = ['error', 'Please login to add to cart'];
            Session::put('add-to-cart', true);
            return redirect()->route('user.login')->withNotify($notify);
        }
        if (!auth()->user()->my_level()->first()){
            $notify[] = ['error', 'Please subscribe to a plan to buy products'];
            Session::put('subscribe-before-add-to-cart', true);
            return redirect()->route('user.plan.purchase')->withNotify($notify);
        }
        $this->cartService->cartQuantityAdapter($request->product_id, $request->quantity);
        $notify[] = ['success', 'Cart updated successfully'];
        return back()->withNotify($notify);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
