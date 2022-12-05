<!DOCTYPE html>
<html lang="ko">
  <head>
    <!-- Required meta tags -->
    @include('admin.css')

    <style>
        .div_center {
            text-align: center;
            padding-top: 40px;
        }
        .font_size {
            font-size: 40px;
            padding-bottom: 40px;
        }
        .text_color {
            color: black;
            padding-bottom: 20px;
        }
        label {
            display: inline-block;
            width: 200px;
        }
        .div_design {
            padding-bottom: 15px;
        }
    </style>
  </head>
  <body>
    <div class="container-scroller">
      <!-- partial:partials/_sidebar.html -->
      @include('admin.sidebar')
      <!-- partial -->
        @include('admin.header')
        <!-- partial -->
        <div class="main-panel"> 
            <div class="content-wrapper">

                @if (session()->has('message'))
                    <div class="alert alert-success">
                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">x</button>
                        {{ session()->get('message') }}
                    </div>
                @endif

                <div class="div_center">
                    <h1 class="font_size">Add Product</h1>

                    <form action="{{ url('/update_product_confirm', $product->id) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="div_design">
                            <label>Product Title :</label>
                            <input type="text" class="text_color" name="title" placeholder="Write a title" required value="{{ $product->title }}">
                        </div>

                        <div class="div_design">
                            <label>Product Description :</label>
                            <input type="text" class="text_color" name="description" placeholder="Write a description" required value="{{ $product->description }}">
                        </div>

                        <div class="div_design">
                            <label>Product Price :</label>
                            <input type="number" class="text_color" name="price" placeholder="Write a price" required value="{{ $product->price }}">
                        </div>

                        <div class="div_design">
                            <label>Discont Price :</label>
                            <input type="number" class="text_color" name="dis_price" placeholder="Write a dis_price" required value="{{ $product->discount_price }}">
                        </div>

                        <div class="div_design">
                            <label>Product Quantity :</label>
                            <input type="number" min="0" class="text_color" name="quantity" placeholder="Write a quantity" required value="{{ $product->quantity }}">
                        </div>

                        <div class="div_design">
                            <label>Product Category :</label>
                            <select name="category" class="text_color" required>
                                @foreach ($category as $category)
                                    <option value="{{ $category->category_name }}" {{ $product->category === $category->category_name ? 'selected' :'' }}>{{ $category->category_name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="div_design">
                            <label>Current Product Image :</label>
                            <img src="{{ asset('/product/'.$product->image) }}" style="margin:auto;" height="100" width="100">
                        </div>

                        <div class="div_design">
                            <label>Product Image Here :</label>
                            <input type="file" name="image">
                        </div>

                        <div class="div_design">
                            <input type="submit" value="Update Product" class="btn btn-primary">
                        </div>
                    </form>
                </div>
            </div>
        </div>
    <!-- container-scroller -->
    <!-- plugins:js -->
    @include('admin.script')
    <!-- End custom js for this page -->
  </body>
</html>