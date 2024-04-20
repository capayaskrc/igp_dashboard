<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Casher Dashboard') }}
        </h2>
    </x-slot>

    <div class="container">
        <h1>Point of Sale</h1>
        <div class="row">
            <div class="col-md-6">
                <h2>Products</h2>
                <div id="products">
                    <!-- Products will be displayed here -->
                </div>
            </div>
            <div class="col-md-6">
                <h2>Cart</h2>
                <div id="cart">
                    <!-- Cart items will be displayed here -->
                </div>
                <p>Total: $<span id="total">0</span></p>
                <button id="process-payment" class="btn btn-primary">Process Payment</button>
            </div>
        </div>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Fetch products from the backend
            $.get('/products', function(products) {
                products.forEach(function(product) {
                    $('#products').append(
                        '<div class="product">' +
                        '<h3>' + product.name + '</h3>' +
                        '<p>Price: $' + product.price + '</p>' +
                        '<input type="number" class="quantity" value="1" min="1">' +
                        '<button class="add-to-cart btn btn-primary" data-id="' + product.id + '">Add to Cart</button>' +
                        '</div>'
                    );
                });
            });

            // Add to cart button click event
            $(document).on('click', '.add-to-cart', function() {
                const productId = $(this).data('id');
                const productName = $(this).prev('h3').text();
                const productPrice = parseFloat($(this).prevAll('p').text().replace('Price: $', ''));
                const productQuantity = $(this).prevAll('.quantity').val();

                const subtotal = productPrice * productQuantity;

                $('#cart').append(
                    '<div class="cart-item">' +
                    '<p>' + productName + ' - $' + productPrice + ' - Quantity: ' + productQuantity + '</p>' +
                    '</div>'
                );

                var total = parseFloat($('#total').text());
                total += subtotal;
                $('#total').text(total.toFixed(2));
            });

            // Process payment button click event
            $('#process-payment').click(function() {
                var total = parseFloat($('#total').text());
                alert('Total amount: $' + total.toFixed(2) + '. Payment processed successfully!');
            });
        });
    </script>
</x-app-layout>
