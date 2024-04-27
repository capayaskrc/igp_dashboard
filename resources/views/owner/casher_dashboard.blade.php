<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Casher Dashboard') }}
        </h2>
    </x-slot>
    <div class="container container-fluid mx-auto p-8 mt-5 border-b-2 border-t-2">
        <h1 class="text-3xl font-bold mb-4">Point of Sale</h1>
        <div class="flex flex-wrap justify-end">
            <div class="categories w-2/3 lg:w-2/3">
                @foreach ($productsByCategory as $category => $products)
                    <div class="card mb-4">
                        <div id="category-{{ $loop->index + 1 }}" class="category card-body">
                            <h2 class="text-xl font-semibold mb-2 cursor-pointer">{{ $category }}</h2>
                            <div class="border-b mb-3"></div> <!-- Line below the category -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 space-y-4 hidden">
                                @foreach ($products as $product)
                                    <div class="product bg-white p-4 shadow-md rounded-md">
                                        <h3 class="text-lg font-semibold product-name">{{ $product->name }}</h3>
                                        <p class="product-price">Price: P{{ $product->price }}</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="col-span-1 w-1/3 lg:w-1/3 ml-auto border-l pl-5 pt-3 flex-col justify-center lg:justify-end"> <!-- Added flex and justify-center for center alignment -->
                <h2 class="text-xl font-semibold mb-2 border-b-2">Selected Products</h2>
                <div id="selected-products" class="space-y-4">
                    <!-- Selected products will be displayed here -->
                </div>
                <p class="mt-4 font-bold">Total: P<span id="total">0</span></p>
                <button id="process-payment" class="bg-blue-500 text-white font-semibold px-4 py-2 rounded-md mt-4 hover:bg-blue-700">Process Payment</button>
            </div>
        </div>
    </div>


    <script>
        function updateTotal() {
            const selectedProducts = document.querySelectorAll('.selected-product');
            let total = 0;

            selectedProducts.forEach(function(selectedProduct) {
                const totalText = selectedProduct.querySelector('.mx-2');
                const subtotal = parseFloat(totalText.textContent.replace('Total: P', ''));
                total += subtotal;
            });

            document.getElementById('total').textContent = total.toFixed(2);
        }

        document.addEventListener('DOMContentLoaded', function() {
            const categories = document.querySelectorAll('.category');

            categories.forEach(function(category) {
                const heading = category.querySelector('h2');
                const productsContainer = category.querySelector('.space-y-4');

                heading.addEventListener('click', function() {
                    productsContainer.classList.toggle('hidden');
                });

                const products = productsContainer.querySelectorAll('.product');

                products.forEach(function(product) {
                    product.addEventListener('click', function() {
                        const productName = product.querySelector('.product-name').textContent;
                        const productPriceElement = product.querySelector('.product-price'); // Select the product price element
                        const productPriceText = productPriceElement ? productPriceElement.textContent : ''; // Check if the price element exists
                        const productPrice = parseFloat(productPriceText.replace('Price: P', ''));

                        let selectedProduct = document.createElement('div');
                        selectedProduct.classList.add('selected-product');
                        selectedProduct.innerHTML = `
                    <p class="font-semibold">${productName}</p>
                    <div class="flex justify-between items-center">
                        <div class="price">
                            <p>Price: P${productPrice.toFixed(2)}</p>
                        </div>
                        <div class="flex items-center">
                            <label for="quantity" class="mr-2">Quantity</label>
                            <input type="number" class="quantity w-16" value="1" min="1">
                        </div>
                        <div>
                            <p class="mx-2">Total: P${productPrice.toFixed(2)}</p>
                        </div>
                    </div>
                `;

                        document.getElementById('selected-products').appendChild(selectedProduct);

                        const quantityInput = selectedProduct.querySelector('.quantity');

                        quantityInput.addEventListener('input', function() {
                            const quantity = parseInt(quantityInput.value);
                            const total = quantity * productPrice;
                            selectedProduct.querySelector('.mx-2').textContent = `Total: P${total.toFixed(2)}`;
                            updateTotal();
                        });

                        updateTotal();
                    });
                });
            });

            const processPaymentButton = document.getElementById('process-payment');
            processPaymentButton.addEventListener('click', function() {
                const selectedProducts = document.querySelectorAll('.selected-product');
                const salesData = [];
                selectedProducts.forEach(function(selectedProduct) {
                    const productName = selectedProduct.querySelector('.font-semibold').textContent;
                    const productPrice = parseFloat(selectedProduct.querySelector('.price').textContent.replace('Price: P', '')); // Changed to select the price directly
                    const quantity = parseInt(selectedProduct.querySelector('.quantity').value);
                    const totalAmount = parseFloat(selectedProduct.querySelector('.mx-2').textContent.replace('Total: P', ''));

                    salesData.push({
                        productName: productName,
                        productPrice: productPrice,
                        quantitySold: quantity,
                        totalAmount: totalAmount
                    });
                });
                saveSalesData(salesData);
            });
        });


        function saveSalesData(salesData) {
            fetch('/owner/dashboard/save-sales-data', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ salesData: salesData })
            })
                .then(response => response.json())
                .then(data => {
                    console.log(data);
                    document.getElementById('selected-products').innerHTML = '';
                    document.getElementById('total').textContent = '0.00';
                    alert('Payment processed successfully!'); // Optionally show a success message
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

    </script>

</x-app-layout>
