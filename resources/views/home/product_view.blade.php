<section class="product_section layout_padding">
    <div class="container">
    <div class="heading_container heading_center">
        <div>
            <form action="{{ url('search_product') }}" method="GET">
                @csrf
                <input style="width: 500px;" type="text" name="search" placeholder="search for Something">
                <input type="submit" value="search">
            </form>
        </div>
        {{-- Add searchbar end --}}
    </div>
    
    @if (session()->has('message'))
        <div class="alert alert-success">
        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
        {{ session()->get('message') }}
        </div>
    @endif

    <div class="row">
            @foreach($products as $product)
                <div class="col-sm-6 col-md-4 col-lg-4">
                <div class="box">
                    <div class="option_container">
                        <div class="options">
                            <a href="{{ url('product_detail', $product->id) }}" class="option1">
                            Product Details
                            </a>
                            <form action="{{ url('add_cart', $product->id) }}" method="POST">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <input type="number" name="quantity" value="1" min="1" style="display: inline-block; width: 90px; height: 50px;">
                                    </div>
                                    <div class="col-md-4">
                                        <input class="btn" type="submit" value="Add to Cart">
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="img-box">
                        <img src="{{ asset('product/'.$product->image) }}" alt="">
                    </div>
                    <div class="detail-box">
                        <h5>
                            {{ $product->title }}
                        </h5>
                        @if ($product->discount_price != null)
                            <h6 style="color: red;">
                            Discount price<br>
                            ${{ $product->discount_price }}
                            </h6>

                            <h6 style="text-decoration: line-through; color: blue;">
                            Price<br>
                            ${{ $product->price }}
                            </h6>
                        @else
                            <h6 stlye="color: bule;">
                            Price<br>
                            ${{ $product->price }}
                            </h6>
                        @endif
                    </div>
                </div>
                </div>
            @endforeach
            
            {{ $products->links() }}
        <div>
    </div>
</section>