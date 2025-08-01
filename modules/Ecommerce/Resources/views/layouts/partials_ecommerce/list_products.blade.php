@foreach ($dataPaginate as $item)
    <div class="col-6 {{ \Route::currentRouteName() == 'tenant.ecommerce.index' ? 'col-md-3' : 'col-md-4' }}">
        <div class="product product-style {{ stock($item, $configuration) ? 'productdisabled' : '' }}">
            <figure class="product-image-container">
                @php
                    $configuration = \App\Models\Tenant\Configuration::first();
                    $defaultImage = $configuration->product_default_image ?? 'imagen-no-disponible.jpg';
                    $defaultImagePath = $defaultImage === 'imagen-no-disponible.jpg'
                        ? asset('logo/imagen-no-disponible.jpg')
                        : asset('storage/defaults/' . $defaultImage);
                            
                    $imagePath = $item->image !== 'imagen-no-disponible.jpg'
                        ? asset('storage/uploads/items/' . $item->image)
                        : $defaultImagePath;
                @endphp
                            
                <a href="/ecommerce/item/{{ $item->id }}" class="product-image product-image-list">
                    <img src="{{ $imagePath }}" class="image" alt="{{ $item->description }}">
                </a>
                <a href="{{route('item_partial', ['id' => $item->id])}}" class="btn-quickview">Vista Rápida</a>
                {{-- <span class="product-label label-sale">-20%</span> --}}
                @if(json_encode($item->is_new) == 1)
                    <span class="product-label label-hot">Nuevo</span>
                @endif
                @if(stock($item, $configuration))
                    <span class="product-label product-danger">AGOTADO</span>
                @endif
            </figure>
            <div class="product-details-ecommerce">
                <div class="ratings-container">
                    <div class="product-ratings">
                        <span class="ratings" style="width:0%"></span>
                    </div>
                </div>
                <div class="product-information">
                    <h2 class="product-title-ecommerce">
                        <a href="/ecommerce/item/{{ $item->id }}">{{ $item->description }}</a>
                    </h2>
                    <h3 class="product-stock">Disponible: 
                        <span>{{ number_format($item ->stock, 0) }}</span>
                    </h3>
                </div>
                <div class="product-price-ecommerce">
                    <div class="price-box-ecommerce">
                        <!-- <span class="old-price">S/ {{ number_format( ($item->sale_unit_price * 1.2 ) , 2 )}}</span> -->
                        <span class="product-price-ecommerce">{{ $item->currency_type['symbol'] }} {{ number_format($item->sale_unit_price, 2) }}</span>
                    </div>
                    <div class="product-action">
                        <a href="#" class="paction add-cart" data-product="{{ json_encode( $item ) }}" title="Add to Cart">
                            <span>Agregar a Carrito</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endforeach

<?php

    function stock($item, $config)
    {
        if($config) {
            $stock=0;
            foreach ($item->warehouses as $key => $value) {
                $stock += $value->stock;
            }
            return ($stock > 0) ? false : true;
        }
    }
?>

<style>
    /* .product-style {
        border-style: solid;
        border-width: 1px;
        border-color: "#ddd";
        margin: 10px 1px;
    } */
    .product-image-list {
        max-height: 210px;
        min-height: 210px;
    }
    .image {
        max-height: 210px;
    }
    .product-danger {
        float: right;
        color: #fff;
        background-color: #dc3545;
        border-color: #dc3545;
    }
    .productdisabled
    {
        pointer-events: none;
        /* opacity: 0.7; */
    }
</style>
