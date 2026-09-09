@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="{{ asset('css/invoice.css') }}">
@endsection

@section('content')

<div class="container-fluid invoice-page">

    <div class="invoice-wrapper" id="invoice">

        {{-- Invoice Header --}}
        <div class="invoice-header">

            <div class="company-section">

                <div class="company-logo">
                    SM
                </div>

                <div>
                    <h1>StockMan</h1>
                    <p>Smart Stock Management System</p>
                </div>

            </div>


            <div class="invoice-title">

                <span class="invoice-label">
                    SALES INVOICE
                </span>

                <h3>{{ $sale->invoice_number }}</h3>

                <p>
                    {{ $sale->created_at->format('d M Y, h:i A') }}
                </p>

            </div>

        </div>


        <div class="invoice-divider"></div>


        {{-- Customer and Payment Information --}}
        <div class="invoice-info">

            <div class="customer-info">

                <span class="section-label">
                    BILL TO
                </span>

                <h4>
                    {{ $sale->customer_name ?? 'Walk-in Customer' }}
                </h4>

                <p>
                    <strong>Phone:</strong>

                    {{ $sale->phone_number ?? '-' }}
                </p>

            </div>


            <div class="payment-info">

                <span class="section-label">
                    PAYMENT METHOD
                </span>

                <div class="payment-badge">

                    {{ strtoupper($sale->payment_method) }}

                </div>

                <p class="payment-status">
                    @if($sale->status === 'completed')
                        <span class="text-success">
                            Completed
                        </span>
                    @elseif($sale->status === 'cancelled')
                        <span class="text-danger">
                            Cancelled
                        </span>
                    @endif
                </p>

            </div>

        </div>


        {{-- Products Table --}}
        <div class="table-responsive">

            <table class="table invoice-table">

                <thead>

                    <tr>

                        <th>#</th>

                        <th>Product</th>

                        <th>Price</th>

                        <th>Qty</th>

                        <th class="text-end">
                            Amount
                        </th>

                    </tr>

                </thead>


                <tbody>

                    @foreach($sale->sale_items as $index => $item)

                    <tr>

                        <td>
                            {{ $index + 1 }}
                        </td>


                        <td>

                            <div class="product-name">

                                {{ $item->product->name }}

                            </div>


                            <small>

                                SKU:
                                {{ $item->product->sku }}

                            </small>

                        </td>


                        <td>

                            ₹{{ number_format($item->price, 2) }}

                        </td>


                        <td>

                            {{ number_format($item->quantity, 0) }}

                        </td>


                        <td class="text-end amount">

                            ₹{{ number_format($item->subtotal, 2) }}

                        </td>

                    </tr>

                    @endforeach

                </tbody>

            </table>

        </div>


        {{-- Invoice Bottom --}}
        <div class="invoice-bottom">

            <div class="invoice-note">

                <h5>Thank you for your purchase! ❤️</h5>

                <p>
                    Thank you for choosing StockMan.
                    Please keep this invoice for your records.
                </p>

            </div>


            {{-- Totals --}}
            <div class="invoice-totals">

                <div class="total-row">

                    <span>Subtotal</span>

                    <strong>
                        ₹{{ number_format($sale->subtotal, 2) }}
                    </strong>

                </div>


                <div class="total-row">

                    <span>Discount</span>

                    <strong class="discount-amount">
                        - ₹{{ number_format($sale->discount, 2) }}
                    </strong>

                </div>


                <div class="total-row">

                    <span>Tax</span>

                    <strong>
                        ₹{{ number_format($sale->tax, 2) }}
                    </strong>

                </div>


                <div class="total-divider"></div>


                <div class="grand-total">

                    <span>Total Amount</span>

                    <strong>
                        ₹{{ number_format($sale->total_amount, 2) }}
                    </strong>

                </div>

            </div>

        </div>


        {{-- Footer --}}
        <div class="invoice-footer">

            <p>
                This is a computer-generated invoice.
            </p>

            <p>
                Invoice #{{ $sale->invoice_number }}
            </p>

        </div>

    </div>


    {{-- Action Buttons --}}
    <div class="invoice-actions">

        <a href="{{ route('sales.create') }}"
           class="btn btn-primary">

            + New Sale

        </a>


        <button type="button"
                id="printInvoiceBtn"
                onclick="window.print()"
                class="btn btn-dark">

            🖨 Print Invoice

        </button>

        <button type="button" id="downloadPdfBtn" class="btn btn-danger">
            📄 Download PDF
        </button>

        <button type="button" id="shareWatsappBtn" class="btn btn-success">
            📱 Share on WhatsApp
        </button>

    </div>

</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2pdf.js/0.10.1/html2pdf.bundle.min.js"></script>
<script>
    
    document.getElementById('printInvoiceBtn').addEventListener('click', ()=>{
        window.print();
    })

    //PDF code
    const invoiceNumber = "{{ $sale->invoice_number }}"
    document.getElementById('downloadPdfBtn').addEventListener('click',()=>{
        const invoice = document.getElementById('invoice');

        const options = {
            margin :10,

            filename: invoiceNumber + '.pdf',

            image:{
                type: 'jpeg',
                quality: 0.98
            },

            html2canvas:{
                scale:2
            },
            
            jsPDF: {
                unit: 'mm',

                format: 'a4',

                orientation: 'portrait'
            }
        };

        html2pdf()
            .set(options)
            .from(invoice)
            .save();
    });

    //Watsapp sharing code

    document.getElementById('shareWatsappBtn').addEventListener('click', async function(){
        const invoice = document.getElementById('invoice');

        const invoiceNumber = '{{ $sale->invoice_number }}';

        const options = {
            margin : 5,

            image:{
                type: 'jpeg',
                quality: 0.98
            },

            html2canvas:{
                scale:2,
                useCORS: true
            },

            jsPDF:{
                unit: 'mm',
                format: 'a4',
                orientation:'portrait'
            }
        };

        try{
            
            //Generate PDF as Blob
            const pdfBlob = await html2pdf()
                .set(options)
                .from(invoice)
                .outputPdf('blob');

            //Create actual PDF file
            const pdfFile = new File(
                [pdfBlob],
                invoiceNumber + '.pdf',

                {
                    type: 'application/pdf'
                }
            );

            //Checking if file is supported
            if(
                navigator.share &&
                navigator.canShare &&
                navigator.canShare({files: [pdfFile]})
            ){
                await navigator.share({
                    title: 'Sale Invoice',

                    text: 'Invoice ' + invoiceNumber,

                    files: [pdfFile]
                });
            }else{
                alert('File sharing is not supported on this browser. Please download the PDF and share it manually');

            }
        }catch(error){
            //User may cancel the share dialog
            if(error.name !== 'AbortError'){
                console.error('Share error:', error);
                alert('Unable to share invoice');
            }
        }
    });
</script>
@endsection