<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Product;
use App\Models\Cart;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::paginate(6);
        return view('home.userpage', compact('products'));
    }

    public function redirect()
    {
        $usertype = Auth::user()->usertype;

        if($usertype == '1') {
            return view('admin.home');
        }

        $products = Product::paginate(6);
        return view('home.userpage', compact('products'));
    }

    public function product_detail($id)
    {
        $product = Product::find($id);

        return view('home.product_detail', compact('product'));
    }

    public function add_cart(Request $request, $id)
    {
        // 로그인 된 사용자
        if(Auth::id()) {
            $user = Auth::user();
            $product = Product::find($id);
            
            $cart = new Cart;
            $cart->name = $user->name;
            $cart->email = $user->email;
            $cart->phone = $user->phone;
            $cart->address = $user->address;
            $cart->user_id = $user->id;
            
            $cart->product_title = $product->title;

            if ($product->discount_price != null) {
                $cart->price = $product->discount_price * $request->quantity;
            } else {
                $cart->price = $product->price * $request->quantity;
            }

            $cart->image = $product->image;
            $cart->product_id = $product->id;
            $cart->quantity = $request->quantity;
            
            $cart->save();

            return redirect()->back();
        }

        // 로그인 하지 않은 사용자
        return redirect('login');
    }

    public function show_cart()
    {
        if (Auth::id()) {
            $id = Auth::user()->id;
            $carts = Cart::where('user_id','=' ,$id)->get();
            $totalPrice = $carts->sum('price');
            return view('home.showcart', compact('carts', 'totalPrice'));
        }

        return redirect('login');
    }

    public function remove_cart($id)
    {
        $cart = Cart::find($id);
        $cart->delete();

        return redirect()->back();
    }
}
