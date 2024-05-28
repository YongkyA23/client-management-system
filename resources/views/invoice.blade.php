<!DOCTYPE html>
<html>

<head>
    <title>Invoice for {{ $invoice->project->client->name }}</title>
    <style>
        * {
            font-family: "Calibri", sans-serif;
            margin-top: 10px;
            font-size: 12px;
        }

        .mainTable {
            width: 100%;
            border-collapse: collapse;
        }

        .title {
            text-decoration: underline;
        }

        th {
            padding: 5px 5px 5px 8px;
            text-align: left;
        }

        td .mainTable {
            padding-left: 8px;
        }

        .bottomInfo {
            padding: 5px 5px 5px 0px;
        }

        th {
            background-color: #f2f2f2;
        }

        .bg-grey {
            background-color: #f2f2f2;
        }

        .logo {
            text-align: left;
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
            text-align: left;
        }

        .approval {
            width: 100%;
            margin-top: 20px;
            page-break-inside: avoid;
        }

        .approval h3 {
            background-color: #f2f2f2;
            text-align: center;
        }

        .signature-table {
            width: 100%;
            margin-top: 10px;
            table-layout: fixed;
        }

        .signature-table td {
            padding: 0px;
            vertical-align: top;
        }

        .signature-table td:first-child {
            text-align: left;
        }

        .signature-table td:last-child {
            text-align: left;
            padding-left: 210px;
        }

        .signature-table img {
            width: 100px;
            height: 40px;
        }

        .signature-table p {
            margin: 5px 0;
        }

        .signature-section {
            text-align: left;
        }

        .signature-space {
            width: 100px;
            height: 50px;
        }

        .header-table {
            width: 100%;
            margin-bottom: 20px;
        }

        .header-table td {
            vertical-align: top;
        }

        .header-info {
            text-align: right;
        }

        .company-info,
        .client-info {
            margin-bottom: 20px;
        }
    </style>
</head>

<body>
    <table class="header-table">
        <tr>
            <td class="logo">
                <img src="images/logo.png" alt="Company Logo" width="150" />
            </td>
            <td class="header-info">
                <h4 class="title">Invoice</h4>
                <p>Referensi: {{ $invoice->id }}</p>
                <p>Tanggal: {{ $invoice->issue_date }}</p>
                <p>Tgl. Jatuh Tempo: {{ $invoice->due_date }}</p>
                <p>No. NPWP: 123456</p>
                <p>Status: Unpaid</p>
            </td>
        </tr>
    </table>

    <table class="header-table">
        <tr>
            <td class="company-info">
                <h4>Info Perusahaan</h4>
                <p>PT Multi Jaya Abadi</p>
                <p>Alamat: Jl. ini aja dulu No.12</p>
                <p>Jakarta Utara</p>
                <p>DKI Jakarta 10450</p>
                <p>Indonesia</p>
                <p>Telp: 08112288833</p>
                <p>Email: solusi_finansial@gmail.com</p>
            </td>
            <td class="client-info">
                <h4>Tagihan Untuk</h4>
                <p>Abdul Simalakama</p>
                <p>UP: Anugrah Royan</p>
                <p>Alamat: Jl. Merpati Raya</p>
                <p>Jakarta Utara</p>
                <p>DKI Jakarta 10450</p>
                <p>Indonesia</p>
                <p>Telp: 085974964088</p>
                <p>Email: abdul08@gmail.com</p>
            </td>
        </tr>
    </table>

    <div class="project-details">
        <div class="proj-title">
            <strong>Project</strong> : {{ $invoice->project->name }}
        </div>
    </div>
    <table class="mainTable" style="width: 100%; border-collapse: collapse;">
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
        <table class="signature-table">
            <tr>
                <td>
                    <p class="signature-section">Prepared by</p>
                    <img src="{{ 'storage/' . $cPerson->signature_image }}" alt="Signature" />
                    <p class="signature-section">____________________</p>
                    <p class="signature-section">{{ $cPerson->name }}</p>
                </td>
                <td>
                    <p class="signature-section">Client approval</p>
                    <div class="signature-space"></div>
                    <p class="signature-section">____________________</p>
                    <p class="signature-section">Client/</p>
                </td>
            </tr>
        </table>
    </div>

    @php
        try {
            $content = nl2br(e($invoiceNotes->content));
        } catch (Exception $e) {
            $content = 'An error occurred while processing the content.';
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
