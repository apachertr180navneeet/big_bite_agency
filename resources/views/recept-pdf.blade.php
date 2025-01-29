<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Ledger</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .summary {
            margin-top: 20px;
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .image_mrgin{
            margin-left: 40%
        }
    </style>
</head>
<body>
    <div class="image_mrgin">
        <img src="{{public_path('assets/admin/img/bigbitelogo.png')}}" width="35%"/>
    </div>
    <p class="text-center"><strong>Name:</strong> {{ $customerDetails['name'] }}</p>
    <p class="text-center"><strong>City:</strong> {{ $customerDetails['city'] }}</p>
    <p class="text-center"><strong>Phone:</strong> {{ $customerDetails['phone'] }}</p>

    <table class="table table-bordered" id="branchTable">
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Receipt</th>
                <th>Discount</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ledgerData as $ledger)
                <tr>
                    <td>{{ htmlspecialchars($ledger['date'], ENT_QUOTES, 'UTF-8') }}</td>
                    <td>{{ htmlspecialchars($ledger['description'], ENT_QUOTES, 'UTF-8') }}</td>
                    <td>{{ number_format($ledger['receipt'], 2) }}</td>
                    <td>{{ number_format($ledger['discount'], 2) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>  
</body>
</html>
