@php
    use Modules\Template\Helpers\TemplatePdf;

    $establishment = $document->establishment;
    $customer = $document->customer;
    $invoice = $document->invoice;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    // $accounts = \App\Models\Tenant\BankAccount::all();
    $accounts = (new TemplatePdf)->getBankAccountsForPdf($document->establishment_id);

    $tittle = $document->prefix.'-'.str_pad($document->id, 8, '0', STR_PAD_LEFT);

    $logo = "storage/uploads/logos/{$company->logo}";
    if($establishment->logo) {
        $logo = "{$establishment->logo}";
    }
    $configurationInPdf= App\CoreFacturalo\Helpers\Template\TemplateHelper::getConfigurationInPdf();

@endphp
<html>
<head>
    {{--<title>{{ $tittle }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>

@if($company->logo)
    <div class="text-center company_logo_box pt-5">
        <img src="data:{{mime_content_type(public_path("{$logo}"))}};base64, {{base64_encode(file_get_contents(public_path("{$logo}")))}}" alt="{{$company->name}}" class="company_logo_ticket contain">
    </div>
{{--@else--}}
    {{--<div class="text-center company_logo_box pt-5">--}}
        {{--<img src="{{ asset('logo/logo.jpg') }}" class="company_logo_ticket contain">--}}
    {{--</div>--}}
@endif
<table class="full-width">
    <tr>
        <td class="text-center"><h5>{{ $company->name }}</h5></td>
    </tr>
    <tr>
        <td class="text-center"><h5>{{ 'RUC '.$company->number }}</h5></td>
    </tr>
    <tr>
        <td class="text-center">
            {{ ($establishment->address !== '-')? $establishment->address : '' }}
            {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
            {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
            {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}

            @isset($establishment->trade_address)
                <h6>{{ ($establishment->trade_address !== '-')? 'D. Comercial: '.$establishment->trade_address : '' }}</h6>
            @endisset
            <h6>{{ ($establishment->telephone !== '-')? 'Central telefónica: '.$establishment->telephone : '' }}</h6>

            <h6>{{ ($establishment->email !== '-')? 'Email: '.$establishment->email : '' }}</h6>

            @isset($establishment->web_address)
                <h6>{{ ($establishment->web_address !== '-')? 'Web: '.$establishment->web_address : '' }}</h6>
            @endisset

            @isset($establishment->aditional_information)
                <h6>{{ ($establishment->aditional_information !== '-')? $establishment->aditional_information : '' }}</h6>
            @endisset
        </td>
    </tr>
    <tr>
        <td class="text-center">{{ ($establishment->email !== '-')? $establishment->email : '' }}</td>
    </tr>
    <tr>
        <td class="text-center pb-3">{{ ($establishment->telephone !== '-')? $establishment->telephone : '' }}</td>
    </tr>
    <tr>
        <td class="text-center pt-3 border-top"><h4>COTIZACIÓN</h4></td>
    </tr>
    <tr>
        <td class="text-center pb-3 border-bottom"><h3>{{ $tittle }}</h3></td>
    </tr>
</table>
<table class="full-width">
    <tr>
        <td width="" class="pt-3"><p class="desc">F. Emisión:</p></td>
        <td width="" class="pt-3"><p class="desc">{{ $document->date_of_issue->format('Y-m-d') }} / {{ $document->time_of_issue }}</p></td>
    </tr>

    @if($document->date_of_due)
    <tr>
        <td width="" class=""><p class="desc">T. Validez:</p></td>
        <td width="" class=""><p class="desc">{{ $document->date_of_due }}</p></td>
    </tr>
    @endif

    @if($document->delivery_date)
    <tr>
        <td width="" class=""><p class="desc">T. Entrega:</p></td>
        <td width="" class=""><p class="desc">{{ $document->delivery_date }}</p></td>
    </tr>
    @endif

    <tr>
        <td class="align-top"><p class="desc">Cliente:</p></td>
        <td><p class="desc">{{ $customer->name }}</p></td>
    </tr>
    <tr>
        <td><p class="desc">{{ $customer->identity_document_type->description }}:</p></td>
        <td><p class="desc">{{ $customer->number }}</p></td>
    </tr>
    @if ($customer->address !== '')
        <tr>
            <td class="align-top"><p class="desc">Dirección:</p></td>
            <td>
                <p class="desc">
                    {{ $customer->address }}
                    {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
                    {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
                    {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
                </p>
            </td>
        </tr>
    @endif
    @if ($document->shipping_address)
    <tr>
        <td class="align-top"><p class="desc">Dir. Envío:</p></td>
        <td colspan="3">
            <p class="desc">
                {{ $document->shipping_address }}
            </p>
        </td>
    </tr>
    @endif

    @if ($customer->telephone)
    <tr>
        <td class="align-top"><p class="desc">Teléfono:</p></td>
        <td >
            <p class="desc">
                {{ $customer->telephone }}
            </p>
        </td>
    </tr>
    @endif
    @if ($document->payment_method_type)
    <tr>
        <td class="align-top"><p class="desc">T. Pago:</p></td>
        <td >
            <p class="desc">
                {{ $document->payment_method_type->description }}
            </p>
        </td>
    </tr>
    @endif

    @if ($document->account_number)
    <tr>
        <td class="align-top"><p class="desc">N° Cuenta:</p></td>
        <td colspan="">
            <p class="desc">
                {{ $document->account_number }}
            </p>
        </td>
    </tr>
    @endif
    @if ($document->sale_opportunity)
    <tr>
        <td class="align-top"><p class="desc">O. Venta:</p></td>
        <td >
            <p class="desc">
                {{ $document->sale_opportunity->number_full }}
            </p>
        </td>
    </tr>
    @endif
    @if(isset($configurationInPdf) && $configurationInPdf->show_seller_in_pdf)
        <tr>
            <td class="align-top"><p class="desc">Vendedor:</p></td>
            <td>
                <p class="desc">
                    @if ($document->seller->name)
                        {{ $document->seller->name }}
                    @else
                        {{ $document->user->name }}
                    @endif
                </p>
            </td>
        </tr>
    @endif
    @if ($document->description)
        <tr>
            <td class="align-top"><p class="desc">Observación:</p></td>
            <td><p class="desc">{!! str_replace("\n", "<br/>", $document->description) !!}</p></td>
            {{-- <td><p class="desc">{{ $document->description }}</p></td> --}}
        </tr>
    @endif

    @if ($document->contact)
        <tr>
            <td class="align-top"><p class="desc">Contacto:</p></td>
            <td><p class="desc">{{ $document->contact }}</p></td>
        </tr>
    @endif
    @if ($document->phone)
        <tr>
            <td class="align-top"><p class="desc">Telf. Contacto:</p></td>
            <td><p class="desc">{{ $document->phone }}</p></td>
        </tr>
    @endif

    @if ($document->purchase_order)
        <tr>
            <td><p class="desc">Orden de Compra:</p></td>
            <td><p class="desc">{{ $document->purchase_order }}</p></td>
        </tr>
    @endif
    @if ($document->quotation_id)
        <tr>
            <td><p class="desc">Cotización:</p></td>
            <td><p class="desc">{{ $document->quotation->identifier }}</p></td>
        </tr>
    @endif
</table>

@php
    $show_brand = $document->items->contains(function ($row) {
        return !empty($row->item->brand);
    });

    $show_model = $document->items->contains(function ($row) {
        return !empty($row->item->model);
    });

    $show_lot = $document->items->contains(function ($row) {
        return !empty($row->getSaleLotGroupCodeDescription());
    });

    $show_due = $document->items->contains(function ($row) {
        return !empty(optional($row->relation_item)->date_of_due);
    });
@endphp

<table class="full-width mt-10 mb-10 ticket">
    <thead class="">
    <tr>
        <th class="border-top-bottom desc-9 text-left">COD.</th>
        <th class="border-top-bottom desc-9 text-left">CANT.</th>
        <th class="border-top-bottom desc-9 text-left">UNIDAD</th>
        <th class="border-top-bottom desc-9 text-left">DESCRIPCIÓN</th>
        <th class="border-top-bottom desc-9 text-left">P.UNIT</th>
        <th class="border-top-bottom desc-9 text-left">TOTAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            @php
                $internal_id = optional($row->item)->internal_id;
            @endphp
            <td class="text-center desc-9 align-top font-bold">{{ $internal_id }}</td>
            <td class="text-center desc-9 align-top">
                @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                @else
                    {{ number_format($row->quantity, 0) }}
                @endif
            </td>
            <td class="text-center desc-9 align-top">{{ $row->item->unit_type_id }}</td>
            <td class="text-left desc-9 align-top">
                @if($row->item->name_product_pdf ?? false)
                    {!!$row->item->name_product_pdf ?? ''!!}
                @else
                    {!!$row->item->description!!}
                @endif
                @if (!empty($row->item->presentation)) {!!$row->item->presentation->description!!} @endif
                @if($row->attributes)
                    @foreach($row->attributes as $attr)
                        <br/>{!! $attr->description !!} : {{ $attr->value }}
                    @endforeach
                @endif
                @if($row->discounts)
                    @foreach($row->discounts as $dtos)
                        <br/><small>{{ $dtos->factor * 100 }}% {{$dtos->description }}</small>
                    @endforeach
                @endif
                @if($row->item->is_set == 1)
                 <br>
                    @inject('itemSet', 'App\Services\ItemSetService')
                    @foreach ($itemSet->getItemsSet($row->item_id) as $item)
                        {{$item}}<br>
                    @endforeach
                @endif
                  @if($row->item !== null && property_exists($row->item,'extra_attr_value') && $row->item->extra_attr_value != '')
                    <br/><span style="font-size: 9px">{{$row->item->extra_attr_name}}: {{ $row->item->extra_attr_value }}</span>
                @endif

                @if($show_lot)
                <small style="display:block; font-weight: normal; font-size: 7px;">
                    Lote: {{ $row->getSaleLotGroupCodeDescription() }}
                    <br>
                    FV: 
                    @if(isset($row->relation_item->date_of_due))
                        {{ $row->relation_item->date_of_due->format('Y-m-d') }}
                    @else
                        -
                    @endif
                    <br>
                </small>
                @endif
            </td>
            <td class="text-right desc-9 align-top">{{ number_format($row->unit_price, 2) }}</td>
            <td class="text-right desc-9 align-top">{{ number_format($row->total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="6" class="border-bottom"></td>
        </tr>
    @endforeach
        @if($document->total_exportation > 0)
            <tr>
                <td colspan="5" class="text-right font-bold desc">OP. EXPORTACIÓN: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->total_exportation, 2) }}</td>
            </tr>
        @endif
        @if($document->total_free > 0)
            <tr>
                <td colspan="5" class="text-right font-bold desc">OP. GRATUITAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->total_free, 2) }}</td>
            </tr>
        @endif
        @if($document->total_unaffected > 0)
            <tr>
                <td colspan="5" class="text-right font-bold desc">OP. INAFECTAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->total_unaffected, 2) }}</td>
            </tr>
        @endif
        @if($document->total_exonerated > 0)
            <tr>
                <td colspan="5" class="text-right font-bold desc">OP. EXONERADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->total_exonerated, 2) }}</td>
            </tr>
        @endif
        @if($document->total_taxed > 0)
            <tr>
                <td colspan="5" class="text-right font-bold desc">OP. GRAVADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold desc">{{ number_format($document->total_taxed, 2) }}</td>
            </tr>
        @endif
        @if($document->total_discount > 0)
            <tr>
                <td colspan="5" class="text-right font-bold">{{(($document->total_prepayment > 0) ? 'ANTICIPO':'DESCUENTO TOTAL')}}: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_discount, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="5" class="text-right font-bold desc">IGV: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold desc">{{ number_format($document->total_igv, 2) }}</td>
        </tr>
        <tr>
            <td colspan="5" class="text-right font-bold desc">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold desc">{{ number_format($document->total, 2) }}</td>
        </tr>
    </tbody>
</table>
<table class="full-width">
    <tr>

        @foreach(array_reverse((array) $document->legends) as $row)
            <tr>
                @if ($row->code == "1000")
                    <td class="desc pt-3" style="text-transform: uppercase;">Son: <span class="font-bold">{{ $row->value }} {{ $document->currency_type->description }}</span></td>
                    @if (count((array) $document->legends)>1)
                    <tr><td class="desc pt-3"><span class="font-bold">Leyendas</span></td></tr>
                    @endif
                @else
                    <td class="desc pt-3">{{$row->code}}: {{ $row->value }}</td>
                @endif
            </tr>
        @endforeach
    </tr>
    @if(isset($configurationInPdf) && $configurationInPdf->show_bank_accounts_in_pdf)
        <tr>
            <td class="desc pt-3">
                <br>
                @foreach($accounts as $account)
                    <span class="font-bold">{{$account->bank->description}}</span> {{$account->currency_type->description}}
                    <br>
                    <span class="font-bold">N°:</span> {{$account->number}}
                    @if($account->cci)
                    - <span class="font-bold">CCI:</span> {{$account->cci}}
                    @endif
                    <br>
                @endforeach

            </td>
        </tr>
    @endif

</table>
<br>
<table class="full-width">
<tr>
    <td class="desc pt-3">
    <strong>PAGOS:</strong> </td></tr>
        @php
            $payment = 0;
        @endphp
        @foreach($document->payments as $row)
            <tr><td class="desc ">- {{ $row->payment_method_type->description }} - {{ $row->reference ? $row->reference.' - ':'' }} {{ $document->currency_type->symbol }} {{ $row->payment }}</td></tr>
            @php
                $payment += (float) $row->payment;
            @endphp
        @endforeach
        <tr><td class="desc pt-3"><strong>SALDO:</strong> {{ $document->currency_type->symbol }} {{ number_format($document->total - $payment, 2) }}</td>
    </tr>

    @if($document->terms_condition)
    <tr>
        <td class="desc pt-5 ">
            {!!$document->terms_condition!!}
        </td>
    </tr>
    @endif
</table>
</html>
