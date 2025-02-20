<!-- resources/views/admin/reports/salesperson_pdf.blade.php -->

<!DOCTYPE html>
<html>
<head>
    <title>Salesperson Report</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 8px;
            text-align: left;
        }
    </style>
</head>
<body>
    <h1>Salesperson</h1>
    <div>
        <strong>Total Outstanding:</strong> 
        {{ $totaloutStanding }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Sale Parson</th>
                <th>Total Outstanding</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($salespersonOutstandings as $salespersonOutstanding)
                <tr>
                    <td>{{ $salespersonOutstanding['full_name'] }}</td>
                    <td>{{ $salespersonOutstanding['outstanding_amount'] }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
