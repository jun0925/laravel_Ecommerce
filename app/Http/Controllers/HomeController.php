<?php

namespace App\Http\Controllers;

use Stripe\Charge;
use Stripe\Stripe;
use App\Models\Cart;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Comment;
use App\Models\Reply;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::paginate(6);
        $comments = Comment::orderByDesc('id')->get();
        $replies = Reply::all();
        return view('home.userpage', compact('products', 'comments', 'replies'));
    }

    public function redirect()
    {
        $usertype = Auth::user()->usertype;

        if($usertype == '1') {
            $total_product = Product::all()->count();
            $total_order = Order::all()->count();
            $total_user = User::all()->count();
            $total_revenue = Order::all()->sum('price') ? Order::all()->sum('price') : 0;
            $total_delivered = Order::where('delivery_status', '=', 'delivered')->get()->count();
            $total_processing = Order::where('delivery_status', '=', 'processing')->get()->count();

            return view('admin.home', compact('total_product', 'total_order', 'total_user', 'total_revenue', 'total_delivered', 'total_processing'));
        }

        $products = Product::paginate(6);
        $comments = Comment::orderByDesc('id')->get();
        $replies = Reply::all();
        return view('home.userpage', compact('products', 'comments', 'replies'));
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

    public function cash_order()
    {
        $user = Auth::user();
        $userid = $user->id;

        $data = Cart::where('user_id', '=', $userid)->get();
        
        foreach($data as $data) {
            $order = new Order;
            $order->name = $data->name;
            $order->email = $data->email;
            $order->phone = $data->phone;
            $order->address = $data->address;
            $order->user_id = $data->user_id;

            $order->product_title = $data->product_title;
            $order->price = $data->price;
            $order->quantity = $data->quantity;
            $order->image = $data->image;
            $order->product_id = $data->product_id;

            $order->payment_status = 'cash on delivery';
            $order->delivery_status = 'processing';

            $order->save();

            // Cart에서 Order로 이동했으므로 Cart 데이터 삭제
            $cartid = $data->id;
            $cart = Cart::find($cartid);
            $cart->delete();
        }

        return redirect()->back()->with('message', 'We Received Your Order. We will connect with you soon...');
    }

    public function stripe($totalprice)
    {
        return view('home.stripe', compact('totalprice'));
    }

    public function stripePost(Request $request, $totalprice)
    {
        Stripe::setApiKey(env('STRIPE_SECRET'));
    
        Charge::create ([
                "amount" => $totalprice * 100, // 달러 계산을 위해 *100을 붙임
                "currency" => "usd",
                "source" => $request->stripeToken,
                "description" => "Thanks for payment" 
        ]);

        // 장바구니에 담긴 제품 삭제
        $user = Auth::user();
        $userid = $user->id;

        $data = Cart::where('user_id', '=', $userid)->get();
        
        foreach($data as $data) {
            $order = new Order;
            $order->name = $data->name;
            $order->email = $data->email;
            $order->phone = $data->phone;
            $order->address = $data->address;
            $order->user_id = $data->user_id;

            $order->product_title = $data->product_title;
            $order->price = $data->price;
            $order->quantity = $data->quantity;
            $order->image = $data->image;
            $order->product_id = $data->product_id;

            $order->payment_status = 'Paid';
            $order->delivery_status = 'processing';

            $order->save();

            // Cart에서 Order로 이동했으므로 Cart 데이터 삭제
            $cartid = $data->id;
            $cart = Cart::find($cartid);
            $cart->delete();
        }

        Session::flash('success', 'Payment successful!');

        return back();
    }

    public function show_order()
    {
        if(Auth::id()) {
            $user = Auth::user();
            $userid = $user->id;
            $orders = Order::where('user_id', '=', $userid)->get();

            return view('home.order', compact('orders'));
        }

        return redirect('login');
    }

    public function cancel_order($id)
    {
        $order = Order::find($id);
        $order->delivery_status = 'You canceled the order';
        $order->save();

        return redirect()->back();
    }

    public function add_comment(Request $request)
    {
        if(Auth::id()) {
            $comment = new Comment;
            $comment->name = Auth::user()->name;
            $comment->user_id = Auth::user()->id;
            $comment->comment = $request->comment;
            $comment->save();

            return redirect()->back();
        }

        return redirect('login');
    }

    public function add_reply(Request $request)
    {   
        if(Auth::id()) {
            $reply = new Reply;
            $reply->name = Auth::user()->name;
            $reply->user_id = Auth::user()->id;
            $reply->comment_id = $request->commentId;
            $reply->reply = $request->reply;
            $reply->save();

            return redirect()->back();
        }

        return redirect('login');
    }
}
