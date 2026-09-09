@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="">
@endsection

@section('content')
<div class="container-fluid px-3">
    <h2>Sales Return</h2>

    <p>
        Invoice:
        <strong>{{ $sale->invoice_number }}</strong>
    </p>

    <hr>

    @foreach ($sale->sale_items as $saleItem)
        <div style="margin-bottom:10px;">
            <strong>{{ $saleItem->product->name }}</strong>

            <div>
                Sold quantity:
                {{ $saleItem->quantity }}
            </div>

            <div>
                Price:
                {{ number_format($saleItem->price,2) }}
            </div>
        </div>
    @endforeach
</div>
@endsection