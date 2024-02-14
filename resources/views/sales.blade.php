<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Sales Calculator</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Sales Calculator</h1>

        <!-- New Sales Form -->
        <form id="salesForm" action="/calculate-and-record-sale-ajax" method="post">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="quantity">Quantity:</label>
                    <input type="text" class="form-control" name="quantity" id="quantityInput" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="unitCost">Unit Cost:</label>
                    <input type="text" class="form-control" name="unitCost" id="unitCostInput" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="sellingPrice">Selling Price:</label>
                    <span class="form-control" id="sellingPrice">0.00</span>
                </div>
                <div class="form-group col-md-3">
                    <label class="invisible">Record Sale:</label>
                    <button type="button" class="btn btn-primary btn-block" onclick="recordSale()">Record Sale</button>
                </div>
            </div>
            <div class="alert alert-danger mt-2" id="error-message" style="display: none;"></div>
        </form>

        <!-- Display previous sales table -->
        <h2 class="mt-4">Previous Sales</h2>
        <table class="table table-bordered" id="previousSalesTable">
            <thead>
            <tr>
                <th>Quantity</th>
                <th>Unit Cost</th>
                <th>Selling Price</th>
            </tr>
            </thead>
            <tbody>
            @foreach($previousSales as $sale)
                <tr>
                    <td>{{ $sale->quantity }}</td>
                    <td>{{ $sale->unit_cost }}</td>
                    <td>{{ $sale->selling_price }}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>

    <!-- Include Bootstrap JS at the end of the body -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

    <!-- Your existing JavaScript code -->
    <script>
        function calculateSellingPrice() {
            var quantity = document.getElementById('quantityInput').value;
            var unitCost = document.getElementById('unitCostInput').value;

            if (quantity === '' || unitCost === '') {
                document.getElementById('error-message').style.display = 'block';
                document.getElementById('error-message').innerText = 'Both Quantity and Unit Cost are required.';
                
                document.getElementById('sellingPrice').innerText = '0.00';
            } else {
                document.getElementById('error-message').innerText = '';
                document.getElementById('error-message').style.display = 'none';

                // Using AJAX to calculate selling price
                fetch('/calculate-selling-price', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify({
                        quantity: quantity,
                        unitCost: unitCost,
                    }),
                })
                .then(response => response.json())
                .then(data => {
                    // Display the result on the page
                    document.getElementById('sellingPrice').innerText = data.selling_price.toFixed(2);

                    // Update the previous sales table
                    if (Array.isArray(data.previous_sales)) {
                        updatePreviousSalesTable(data.previous_sales);
                    } else {
                        console.error('Invalid or missing previous_sales data:', data.previous_sales);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            }
        }

        function recordSale() {
            // Using AJAX to submit the form without page refresh
            var formData = new FormData(document.getElementById('salesForm'));

            fetch('/calculate-and-store-sale', {
                method: 'POST',
                body: formData,
                
            })
            .then(response => response.json())
            .then(data => {
                // Update the previous sales table
                if (Array.isArray(data.previous_sales)) {
                    updatePreviousSalesTable(data.previous_sales);
                } else {
                    console.error('Invalid or missing previous_sales data:', data.previous_sales);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }

        function updatePreviousSalesTable(salesData) {
            var previousSalesTable = document.getElementById('previousSalesTable');
            var tbody = previousSalesTable.getElementsByTagName('tbody')[0];

            // Clear existing rows
            tbody.innerHTML = '';

            // Add new rows from the updated data
            salesData.forEach(sale => {
                var row = tbody.insertRow();
                var cell1 = row.insertCell(0);
                var cell2 = row.insertCell(1);
                var cell3 = row.insertCell(2);
                cell1.textContent = sale.quantity;
                cell2.textContent = sale.unit_cost;
                cell3.textContent = sale.selling_price;
            });
        }

        // Add event listeners to recalculate selling price on key changes
        document.getElementById('quantityInput').addEventListener('input', calculateSellingPrice);
        document.getElementById('unitCostInput').addEventListener('input', calculateSellingPrice);
    </script>
</body>
</html>
