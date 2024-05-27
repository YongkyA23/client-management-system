<!DOCTYPE html>
<html>

<head>
    <title>Quotation for {{ $invoice->project->client->name }}</title>
    <style>
        * {
            font-family: "Calibri", sans-serif;
            margin-top: 10px;
            margin-bottom: 0px;
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
        td {
            /* border: 1px solid #ddd; */
            padding: 8px;
            text-align: left;
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
            margin-top: 10px;
            font-size: smaller;
        }

        .notes ol {
            margin-left: -20px;
        }

        .project-details {
            display: grid;
            /* Use grid layout for alignment */
            grid-template-columns: auto 1fr;
            /* Two columns: labels and values */
            column-gap: 10px;
            /* Add some space between columns */
            margin-bottom: 20px;
        }

        .project-details strong {
            /* Style the labels (bold) */
            text-align: left;
            /* Align labels to the right */
        }

        .signature {
            margin-top: 0px;
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
                <td colspan="4" style="font-weight: bold; text-align: right;">
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
                    <td style="padding-left: 30px;">- {{ $detail->name }}</td>
                    <td style="text-align: center;">{{ $detail->quantity }}</td>
                    <td style="text-align: right;">{{ number_format($detail->price, 0, '.', ',') }}</td>
                    <td style="text-align: right;">{{ number_format($detail->total_price, 0, '.', ',') }}</td>
                </tr>
            @endforeach
        @endforeach


        <tr class="bg-grey">
            <td colspan="4"><strong>Sub. Total</strong></td>

            <td style="font-weight: bold; text-align: right;">
                {{ number_format($grandTotal, 0, '.', ',') }}
            </td>
        </tr>
        @php
            $tax = ($grandTotal * $invoice->tax_percent) / 100;
        @endphp
        <tr>
            <td><strong>VAT {{ $invoice->tax_percent }}% on above</strong></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="font-weight: bold; text-align: right;">
                {{ number_format($tax, 0, '.', ',') }}
            </td>
        </tr>
        <tr class="bg-grey">
            <td><strong>GRAND TOTAL</strong></td>
            <td></td>
            <td></td>
            <td></td>
            <td style="font-weight: bold; text-align: right;">
                {{ number_format($invoice->total, 0, '.', ',') }}
            </td>
        </tr>
    </table>

    <div class="approval">
        <h3 class="bg-grey">APPROVAL</h3>
        <div class="signature">
            <div style="float: left; width: 48%; margin-right: 4%">
                <p>Prepared by</p>
                <br>
                <br>
                <p>____________________</p>
                <p>Diane</p>
            </div>
            <div style="float: right; ">
                <p>Client approval</p>
                <br>
                <br>
                <p>____________________</p>
                <p>Client/</p>
            </div>
            <div style="clear: both"></div>
        </div>
    </div>

    <div class="notes">
        <strong>Note:</strong>
        <div style="line-height: 1.5; margin-bottom: 15px;">
            {!! nl2br(e($invoiceNotes->content)) !!}
        </div>
    </div>
</body>

</html>
