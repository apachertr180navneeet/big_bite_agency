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
    <h1>Salesperson Report</h1>
    
    <div>
        <strong>Name:</strong> {{ $user->full_name }}
    </div>
    <div>
        <strong>Total Outstanding:</strong> 
        {{ $salespersonOutstandings->sum(function($outstanding) { return $outstanding->outstanding; }) }}
    </div>

    <table>
        <thead>
            <tr>
                <th>Firm Name</th>
                <th>Total Outstanding</th>
                <th>Pending Invoice</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($salespersonOutstandings as $salespersonOutstanding)
                <tr>
                    <td>{{ $salespersonOutstanding->firm }}</td>
                    <td>{{ $salespersonOutstanding->outstanding }}</td>
                    <td>{{ $salespersonOutstanding->invoice }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
