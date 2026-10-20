<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>IT_Asset_Accountability_Form_{{ $transaction->user ? $transaction->user->present()->fullName() : 'Unknown' }}_{{ date('Y-m-d') }}</title>
    
    <link rel="shortcut icon" type="image/ico" href="{{ ($snipeSettings) && ($snipeSettings->favicon!='') ?  Storage::disk('public')->url(e($snipeSettings->favicon)) : config('app.url').'/favicon.ico' }}">

    <link rel="stylesheet" href="{{ url(mix('css/dist/bootstrap-table.css')) }}">
    <link rel="stylesheet" href="{{ url(mix('css/dist/all.css')) }}">

    <script nonce="{{ csrf_token() }}">
        window.snipeit = {
            settings: {
                "per_page": 50
            }
        };
    </script>

    <style>
        body {
            font-family: "Arial, Helvetica", sans-serif;
            padding: 20px;
        }
        
        @page {
            size: A4;
            margin: 0.5cm;
        }
        
        .print-logo {
            max-height: 40px;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 30px;
        }
        
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        
        .signature-table {
            border: none;
            margin-top: 50px;
        }
        
        .signature-table td {
            border: none;
            padding: 10px;
            vertical-align: top;
        }
        
        .signature-line {
            border-bottom: 1px solid #000;
            width: 200px;
            display: inline-block;
            margin-bottom: 10px;
        }
        
        h4 {
            margin-top: 20px;
            margin-bottom: 10px;
        }
        
        @media print {
            html, body { 
                width: 100%; 
                height: 100%; 
                margin: 0; 
                padding: 0; 
            }
            
            table { 
                page-break-inside: auto; 
            }
            
            tr { 
                page-break-inside: avoid; 
                page-break-after: auto; 
            }
            
            thead { 
                display: table-header-group; 
            }
            
            tfoot { 
                display: table-footer-group; 
            }
        }
    </style>

</head>
<body>

@if ($snipeSettings->logo_print_assets=='1')
    <div style="text-align:center; margin-bottom: 20px;">
        @if ($snipeSettings->brand == '3')
            @if ($snipeSettings->logo!='')
                <img class="print-logo" src="{{ config('app.url') }}/uploads/{{ $snipeSettings->logo }}"><br>
            @endif
            <span style="font-size:2em; font-weight:bold; display:block; margin-top:10px;">IT Asset Accountability Form</span>
            <span style="font-size:1.2em; display:block; margin-top:10px;">Document No: {{ $transaction->document_number }}</span>
        @elseif ($snipeSettings->brand == '2')
            @if ($snipeSettings->logo!='')
                <img class="print-logo" src="{{ config('app.url') }}/uploads/{{ $snipeSettings->logo }}"><br>
            @endif
            <span style="font-size:1.2em; display:block; margin-top:10px;">Document No: {{ $transaction->document_number }}</span>
        @else
            <span style="font-size:2em; font-weight:bold; display:block; margin-top:10px;">IT Asset Accountability Form</span>
            <span style="font-size:1.2em; display:block; margin-top:10px;">Document No: {{ $transaction->document_number }}</span>
        @endif
    </div>
@else
    <div style="text-align:center; margin-bottom: 20px;">
        <span style="font-size:2em; font-weight:bold; display:block; margin-top:10px;">IT Asset Accountability Form</span>
        <span style="font-size:1.2em; display:block; margin-top:10px;">Document No: {{ $transaction->document_number }}</span>
    </div>
@endif

<div style="display: flex; justify-content: space-between; align-items: center;">
    <div style="text-align: left;">
        <h2>
            {{ trans('general.assigned_to', ['name' => $transaction->user ? $transaction->user->present()->fullName() : 'Unknown']) }}
            {{ ($transaction->user && $transaction->user->employee_num!='') ? ' (#'.$transaction->user->employee_num.') ' : '' }}
            {{ ($transaction->user && $transaction->user->jobtitle!='' ? ' - '.$transaction->user->jobtitle : '') }}
        </h2>
        <div style="margin-top: 10px;">
            <strong>{{ trans('general.date') }}:</strong> {{ $transaction->transaction_date ? Helper::getFormattedDateObject($transaction->transaction_date, 'date', false) : Helper::getFormattedDateObject(now(), 'date', false) }}
        </div>
    </div>
    <div style="text-align: right;">
        {{ trans('Printed On: ')}} {{ Helper::getFormattedDateObject(now(), 'datetime', false) }}
    </div>
</div>

<p></p>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>{{ trans('general.asset_tag') }}</th>
            <th>{{ trans('general.name') }}</th>
            <th>{{ trans('general.serial') }}</th>
            <th>{{ trans('general.model') }}</th>
            <th>{{ trans('general.action') }}</th>
            <th>{{ trans('general.target') }}</th>
            <th>{{ trans('general.location') }}</th>
            <th>{{ trans('general.notes') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach($transaction->items as $index => $item)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $item->asset ? $item->asset->asset_tag : 'N/A' }}</td>
                <td>{{ $item->asset ? $item->asset->name : 'N/A' }}</td>
                <td>{{ $item->asset ? $item->asset->serial : 'N/A' }}</td>
                <td>{{ $item->asset && $item->asset->model ? $item->asset->model->name : 'N/A' }}</td>
                <td>checkout</td>
                <td>{{ $transaction->user ? $transaction->user->present()->fullName() : 'N/A' }}</td>
                <td>
                    @if($item->asset && $item->asset->location)
                        {{ $item->asset->location->name }}
                    @elseif($transaction->user && $transaction->user->location)
                        {{ $transaction->user->location->name }}
                    @else
                        N/A
                    @endif
                </td>
                <td>{{ $item->notes ?: $transaction->notes ?: '' }}</td>
            </tr>
        @endforeach
    </tbody>
</table>

<!-- Statement Section -->
<div style="margin-top: 50px;">
    <p style="text-align: center; vertical-align: top; font-weight: bold; text-decoration: underline; font-size: 1.3em;">Statement of Accountability</p>
    <p style="text-align: center; vertical-align: top;">I acknowledge receipt of the listed items and accept responsibility for their care and proper use. I will use them only for work, return them when required, and may be held liable for any loss or damage unless proven not my fault.</p>
    
    <p style="text-align: center; vertical-align: top; font-weight: bold; text-decoration: underline; font-size: 1.3em;">IT Asset Policy</p>
    <p style="text-align: center; vertical-align: top;">By signing, I agree to these terms and the company's IT Asset Policy.</p>
</div>

<!-- Signature Section -->
<div style="margin-top: 80px;">
    <!-- Employee -->
    <div style="margin-bottom: 50px;">
        <strong>Employee:</strong>
        <table style="width: 100%; border: none; margin-top: 10px;">
            <tr>
                <td style="border: none; border-bottom: 1px solid #000; width: 25%; padding: 5px 10px 5px 0;">Name</td>
                <td style="border: none; border-bottom: 1px solid #000; width: 45%; padding: 5px 10px 5px 0;">Signature</td>
                <td style="border: none; border-bottom: 1px solid #000; width: 30%; padding: 5px 10px 5px 0;">Date</td>
            </tr>
        </table>
    </div>

    <!-- IT Personnel -->
    <div style="margin-bottom: 50px;">
        <strong>IT Personnel:</strong>
        <table style="width: 100%; border: none; margin-top: 10px;">
            <tr>
                <td style="border: none; border-bottom: 1px solid #000; width: 25%; padding: 5px 10px 5px 0;">Name</td>
                <td style="border: none; border-bottom: 1px solid #000; width: 45%; padding: 5px 10px 5px 0;">Signature</td>
                <td style="border: none; border-bottom: 1px solid #000; width: 30%; padding: 5px 10px 5px 0;">Date</td>
            </tr>
        </table>
    </div>

    <!-- IT Manager -->
    <div style="margin-bottom: 50px;">
        <strong>IT Manager:</strong>
        <table style="width: 100%; border: none; margin-top: 10px;">
            <tr>
                <td style="border: none; border-bottom: 1px solid #000; width: 25%; padding: 5px 10px 5px 0;">Name</td>
                <td style="border: none; border-bottom: 1px solid #000; width: 45%; padding: 5px 10px 5px 0;">Signature</td>
                <td style="border: none; border-bottom: 1px solid #000; width: 30%; padding: 5px 10px 5px 0;">Date</td>
            </tr>
        </table>
    </div>
</div>

</body>
</html>
