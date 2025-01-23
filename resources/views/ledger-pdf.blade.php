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
    </style>
</head>
<body>
    <p><strong>Name:</strong> {{ $customerDetails['name'] }}</p>
    <p><strong>City:</strong> {{ $customerDetails['city'] }}</p>
    <p><strong>Phone:</strong> {{ $customerDetails['phone'] }}</p>

    <table class="table table-bordered" id="branchTable">
        <thead>
            <tr>
                <th>Date</th>
                <th>Description</th>
                <th>Bill</th>
                <th>Receipt</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($ledgerData as $ledger)
                <tr>
                    <td>{{ htmlspecialchars($ledger['date'], ENT_QUOTES, 'UTF-8') }}</td>
                    <td>{{ htmlspecialchars($ledger['description'], ENT_QUOTES, 'UTF-8') }}</td>
                    <td>{{ number_format($ledger['bill'], 2) }}</td>
                    <td>{{ number_format($ledger['receipt'], 2) }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="2" class="text-end"><strong>Total Due</strong></td>
                <td colspan="2"><strong>{{ number_format($totalDue, 2) }}</strong></td>
            </tr>
        </tbody>
    </table>  
</body>
</html>
