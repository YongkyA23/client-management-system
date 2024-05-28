<!DOCTYPE html>
<html>

<head>
    <title>Quotation for {{ $invoice->project->client->name }}</title>
    <style>
        * {
            font-family: "Calibri", sans-serif;
            margin-top: 10px;
            /* margin-bottom: 20px; */
            font-size: 12px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        .title {
            text-decoration: underline
        }

        th,
        {
        /* border: 1px solid #ddd; */
        padding: 5px 5px 5px 8px;
        text-align: left;
        }

        td {
            padding-left: 8px;

        }

        .bottomInfo {
            padding: 5px 5px 5px 8px;
        }

        th {
            background-color: #f2f2f2;
        }

        .bg-grey {
            background-color: #f2f2f2;
        }



        .logo {
            position: absolute;
            top: 0;
            right: 0;
            z-index: -1;
            text-align: right;
        }

        .notes {

            font-size: smaller;

        }

        .notes ol {
            margin-left: -20px;
        }

        .project-details {
            display: grid;
            grid-template-columns: auto 1fr;
            column-gap: 10px;
            margin-bottom: 20px;
        }

        .project-details strong {
            /* Style the labels (bold) */
            text-align: left;
            /* Align labels to the right */
        }

        .signature {
            margin-top: 0px;
            margin-bottom: -70px;
        }

        .approval h3 {
            text-align: center;
            padding: 5px 0 5px 0;

        }
    </style>
</head>

<body>
    <div class="logo">
        <img src="images/logo.png" alt="Company Logo" width="150" />
    </div>

    <h4 class="title">QUOTATION</h4>

    <div class="project-details">
        <strong>Nbr.</strong> : {{ $invoice->id }}<br />
        <strong>Date</strong> : {{ $invoice->issue_date }}<br />
        <strong>Client</strong> : {{ $invoice->project->client->name }}<br />
        <strong>Brand</strong> : {{ $invoice->project->client->name }}<br />
        <div class="proj-title">
            <strong>Project</strong> : {{ $invoice->project->name }}
        </div>
    </div>
    <table style="width: 100%; border-collapse: collapse;">
        <tr>
            <th>Agency Cost</th>
            <th style="text-align: center;">Qty</th>
            <th style="text-align: right;">Rate</th>
            <th style="text-align: right;">Sub Total</th>
            <th style="text-align: right;">TOTAL (IDR)</th>
        </tr>
        @php
            $groupedDetails = $invoice->invoice_details->groupBy('service_category.name');
            $grandTotal = 0;
            $totalPrice = 0;
            $categoryTotal = 0;

            function toRoman($num)
            {
                $n = intval($num);
                $result = '';

                $lookup = [
                    'M' => 1000,
                    'CM' => 900,
                    'D' => 500,
                    'CD' => 400,
                    'C' => 100,
                    'XC' => 90,
                    'L' => 50,
                    'XL' => 40,
                    'X' => 10,
                    'IX' => 9,
                    'V' => 5,
                    'IV' => 4,
                    'I' => 1,
                ];

                foreach ($lookup as $roman => $value) {
                    $matches = intval($n / $value);
                    $result .= str_repeat($roman, $matches);
                    $n = $n % $value;
                }

                return $result;
            }

        @endphp
        @foreach ($groupedDetails as $categoryName => $details)
            @php
                $categoryTotal = 0;
            @endphp
            <tr>
                <td colspan="4" style="font-weight: bold;"> {{ toRoman($loop->iteration) }}.
                    {{ strtoupper($categoryName) }}</td>
                <td colspan="4" style="font-weight: bold; text-align: right; padding-right:5px;">
                    @php
                        foreach ($details as $detail) {
                            $categoryTotal += $detail->total_price;
                        }
                        $grandTotal += $categoryTotal;
                    @endphp
                    {{ number_format($categoryTotal, 0, '.', ',') }}
                </td>
            </tr>
            @foreach ($details as $detail)
                <tr style="font-style: italic;">
                    <td style="padding-left: 25px;">- {{ $detail->name }}</td>
                    <td style="text-align: center; ">{{ $detail->quantity }}</td>
                    <td style="text-align: right; padding-right:5px;">{{ number_format($detail->price, 0, '.', ',') }}
                    </td>
                    <td style="text-align: right; padding-right:5px;">
                        {{ number_format($detail->total_price, 0, '.', ',') }}</td>
                </tr>
            @endforeach
        @endforeach



        <tr class="bg-grey">
            <td colspan="4" class="bottomInfo"><strong>Sub. Total</strong></td>

            <td style="font-weight: bold; text-align: right; padding-right:5px;">
                {{ number_format($grandTotal, 0, '.', ',') }}
            </td>
        </tr>
        @php
            $tax = ($grandTotal * $invoice->tax_percent) / 100;
        @endphp
        <tr>
            <td class="bottomInfo"><strong>VAT {{ $invoice->tax_percent }}% on above</strong></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="font-weight: bold; text-align: right; padding-right:5px;">
                {{ number_format($tax, 0, '.', ',') }}
            </td>
        </tr>
        <tr>
            <td class="bottomInfo"><strong>GRAND TOTAL</strong></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="font-weight: bold; text-align: right; padding-right:5px;">
                {{ number_format($invoice->total, 0, '.', ',') }}
            </td>
        </tr>
    </table>

    <div class="approval">
        <h3 class="bg-grey">APPROVAL</h3>
        <div class="signature">
            <div style="float: left; width: 48%; margin-right: 4%">
                <p>Prepared by</p>
                <img src="{{ 'storage/' . $cPerson->signature_image }}" alt="Signature"
                    style="width:100px; height:40px; " />
                <p>____________________</p>
                <p>{{ $cPerson->name }}</p>
            </div>
            <div style="float:
                    right; ">
                <p>Client approval</p>
                <div style="width:100px; height:57px;"></div>
                <p>____________________</p>
                <p>Client/</p>
            </div>
            <div style="clear: both"></div>
        </div>
    </div>

    @php
        try {
            $content = nl2br(e($invoiceNotes->content));
        } catch (Exception $e) {
            $content = 'An error occurred while processing the content.';
            // Optionally, log the error for debugging purposes
            \Log::error('Error processing invoice notes content: ' . $e->getMessage());
        }
    @endphp


    <div class="notes">
        <strong>Note:</strong>
        <div style="line-height: 1.5; margin-bottom: 15px;">
            {!! $content !!}
        </div>
    </div>
</body>

</html>
