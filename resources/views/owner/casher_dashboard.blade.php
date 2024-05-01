<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Cashier Dashboard') }}
        </h2>
    </x-slot>
    <div class="container container-fluid mx-auto p-8 mt-5 border-b-2 border-t-2">
        <h1 class="text-3xl font-bold mb-4">Point of Sale</h1>
        <div class="flex flex-wrap justify-end">
            <div class="categories w-2/3 lg:w-2/3">
                @foreach ($productsByCategory as $category => $products)
                    <div class="card mb-4">
                        <div id="category-{{ $loop->index + 1 }}" class="category card-body">
                            <div class="flex items-center mb-2 cursor-pointer">
                                <h2 class="text-xl font-semibold mr-3">{{ $category }}</h2>
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 arrow-icon">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 5.25 7.5 7.5 7.5-7.5m-15 6 7.5 7.5 7.5-7.5" />
                                </svg>
                            </div>
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
        document.addEventListener('DOMContentLoaded', function() {
            const categories = document.querySelectorAll('.category');

            categories.forEach(function(category) {
                const heading = category.querySelector('h2');
                const productsContainer = category.querySelector('.space-y-4');

                heading.addEventListener('click', function() {
                    productsContainer.classList.toggle('hidden');
                });

                const svgIcon = category.querySelector('.arrow-icon');
                svgIcon.addEventListener('click', function() {
                    productsContainer.classList.toggle('hidden');
                });

                const products = productsContainer.querySelectorAll('.product');

                products.forEach(function(product) {
                    product.addEventListener('click', function() {
                        const productName = product.querySelector('.product-name').textContent;
                        const productPriceElement = product.querySelector('.product-price');
                        const productPriceText = productPriceElement ? productPriceElement.textContent : '';
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
                        <div class="selected-product-action flex items-center">
                            <p class="mx-2">Total: P${productPrice.toFixed(2)}</p>
                            <button class="remove-product ml-2 focus:outline-none">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="w-4 h-4 remove-product-btn">
                                    <path d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z" />
                                </svg>
                            </button>
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
                    const productPrice = parseFloat(selectedProduct.querySelector('.price').textContent.replace('Price: P', ''));
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
                    alert('Payment processed successfully!');
                })
                .catch(error => {
                    console.error('Error:', error);
                });
        }

        // Event delegation for dynamically added remove product buttons
        document.addEventListener('click', function(event) {
            if (event.target.classList.contains('remove-product-btn')) {
                const selectedProduct = event.target.closest('.selected-product');
                if (selectedProduct) {
                    selectedProduct.remove();
                    updateTotal();
                }
            }
        });
    </script>

</x-app-layout>
