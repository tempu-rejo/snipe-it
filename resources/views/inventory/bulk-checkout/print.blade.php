<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IT Asset Accountability Form - {{ $transaction->document_number }}</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm;
        }
        
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #000;
            margin: 0;
            padding: 0;
        }
        
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #000;
            padding-bottom: 15px;
        }
        
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            margin: 0 0 5px 0;
        }
        
        .header h2 {
            font-size: 14px;
            font-weight: normal;
            margin: 0;
        }
        
        .info-section {
            margin-bottom: 25px;
        }
        
        .info-row {
            display: flex;
            margin-bottom: 8px;
        }
        
        .info-label {
            width: 150px;
            font-weight: bold;
        }
        
        .info-value {
            flex: 1;
            border-bottom: 1px solid #000;
            padding-bottom: 2px;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        
        .items-table th,
        .items-table td {
            border: 1px solid #000;
            padding: 8px;
            text-align: left;
            vertical-align: top;
        }
        
        .items-table th {
            background-color: #f5f5f5;
            font-weight: bold;
            text-align: center;
        }
        
        .signature-section {
            margin-top: 40px;
        }
        
        .signature-row {
            display: flex;
            margin-bottom: 80px;
        }
        
        .signature-box {
            flex: 1;
            text-align: center;
            margin: 0 20px;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            height: 60px;
            margin-bottom: 10px;
        }
        
        .terms {
            margin-top: 30px;
            font-size: 10px;
        }
        
        .terms h4 {
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .terms ul {
            margin: 0;
            padding-left: 20px;
        }
        
        .terms li {
            margin-bottom: 5px;
        }
        
        @media print {
            .no-print {
                display: none;
            }
            
            body {
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
        
        .print-button {
            position: fixed;
            top: 20px;
            right: 20px;
            background: #007bff;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
        }
        
        .print-button:hover {
            background: #0056b3;
        }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Print</button>
    
    <div class="header">
        <h1>IT ASSET ACCOUNTABILITY FORM</h1>
        <h2>CHECKOUT TRANSACTION</h2>
    </div>
    
    <div class="info-section">
        <div class="info-row">
            <div class="info-label">Document Number:</div>
            <div class="info-value">{{ $transaction->document_number }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Transaction Date:</div>
            <div class="info-value">{{ $transaction->transaction_date ? \Carbon\Carbon::parse($transaction->transaction_date)->format('d/m/Y') : '-' }}</div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Employee Name:</div>
            <div class="info-value">
                @if($transaction->user)
                    {{ $transaction->user->present()->fullName() }}
                @else
                    -
                @endif
            </div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Employee Number:</div>
            <div class="info-value">
                @if($transaction->user && $transaction->user->employee_num)
                    {{ $transaction->user->employee_num }}
                @else
                    -
                @endif
            </div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Department:</div>
            <div class="info-value">
                @if($transaction->user && $transaction->user->department)
                    {{ $transaction->user->department->name }}
                @else
                    -
                @endif
            </div>
        </div>
        
        <div class="info-row">
            <div class="info-label">Location:</div>
            <div class="info-value">
                @if($transaction->location)
                    {{ $transaction->location->name }}
                @else
                    -
                @endif
            </div>
        </div>

        <div class="info-row">
            <div class="info-label">Location Detail:</div>
            <div class="info-value">{{ $transaction->location_detail ?: '-' }}</div>
        </div>

        <div class="info-row">
            <div class="info-label">On Behalf:</div>
            <div class="info-value">{{ $transaction->on_behalf ?: '-' }}</div>
        </div>
        
        @if($transaction->notes)
        <div class="info-row">
            <div class="info-label">Notes:</div>
            <div class="info-value">{{ $transaction->notes }}</div>
        </div>
        @endif
    </div>
    
    <table class="items-table">
        <thead>
            <tr>
                <th style="width: 5%">No.</th>
                <th style="width: 15%">Asset Tag</th>
                <th style="width: 25%">Asset Name</th>
                <th style="width: 20%">Model</th>
                <th style="width: 15%">Serial Number</th>
                <th style="width: 20%">Condition</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transaction->items as $index => $item)
            <tr>
                <td style="text-align: center">{{ $index + 1 }}</td>
                <td>{{ $item->asset->asset_tag ?? '-' }}</td>
                <td>{{ $item->asset->name ?? '-' }}</td>
                <td>{{ $item->asset->model->name ?? '-' }}</td>
                <td>{{ $item->asset->serial ?? '-' }}</td>
                <td>{{ $item->asset->status_label->name ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <div class="signature-section">
        <div class="signature-row">
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>Asset Custodian</strong><br>
                @if($transaction->user)
                    {{ $transaction->user->present()->fullName() }}<br>
                    @if($transaction->user->employee_num)
                        {{ $transaction->user->employee_num }}
                    @endif
                @endif
            </div>
            
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>IT Administrator</strong><br>
                @if($transaction->createdBy)
                    {{ $transaction->createdBy->present()->fullName() }}<br>
                    @if($transaction->createdBy->employee_num)
                        {{ $transaction->createdBy->employee_num }}
                    @endif
                @endif
            </div>
        </div>
        
        <div class="signature-row">
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>Department Head</strong><br>
                <br>
            </div>
            
            <div class="signature-box">
                <div class="signature-line"></div>
                <strong>Date</strong><br>
                {{ now()->format('d/m/Y') }}
            </div>
        </div>
    </div>
    
    <div class="terms">
        <h4>Terms and Conditions:</h4>
        <ul>
            <li>The employee acknowledges receipt of the above IT assets in good working condition.</li>
            <li>The employee is responsible for the proper care and security of the assigned assets.</li>
            <li>Any damage, loss, or theft must be reported immediately to the IT Department.</li>
            <li>Assets must be returned in good condition upon termination or reassignment.</li>
            <li>Unauthorized modification or personal use of IT assets is strictly prohibited.</li>
            <li>The employee agrees to comply with all company IT policies and procedures.</li>
        </ul>
    </div>
    
    <script>
        // Auto print when page loads (optional)
        // window.onload = function() { window.print(); }
    </script>
</body>
</html>
