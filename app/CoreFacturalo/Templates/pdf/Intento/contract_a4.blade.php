@php
    $establishment = $document->establishment;
    $customer = $document->customer;
    //$path_style = app_path('CoreFacturalo'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.'pdf'.DIRECTORY_SEPARATOR.'style.css');
    $accounts = \App\Models\Tenant\BankAccount::all();
    $tittle = $document->prefix.'-'.str_pad($document->id, 8, '0', STR_PAD_LEFT);
@endphp
<html>
<head>
    {{--<title>{{ $tittle }}</title>--}}
    {{--<link href="{{ $path_style }}" rel="stylesheet" />--}}
</head>
<body>
<table class="full-width">
    <tr>
        @if($company->logo)
            <td width="20%">
                <div class="company_logo_box">
                    <img src="data:{{mime_content_type(public_path("storage/uploads/logos/{$company->logo}"))}};base64, {{base64_encode(file_get_contents(public_path("storage/uploads/logos/{$company->logo}")))}}" alt="{{$company->name}}" class="company_logo" style="max-width: 150px;">
                </div>
            </td>
            <td width="50%" class="text-center">
                <div class="text-left">
                    <h4 class="">{{ $company->name }}</h4>
                    <h5>{{ 'RUC '.$company->number }}</h5>
                    <h6 style="text-transform: uppercase;">
                        {{ ($establishment->address !== '-')? $establishment->address : '' }}
                        {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
                        {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
                        {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
                    </h6>

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
                </div>
            </td>
            <td width="30%" class="border-box py-4 px-2 text-center">
                <h5 class="text-center">CONTRATO</h5>
                <h3 class="text-center">{{ $tittle }}</h3>
            </td>
        @else
            <td width="70%" class="pl-1">
                <div class="text-left">
                    <h4 class="">{{ $company->name }}</h4>
                    <h5>{{ 'RUC '.$company->number }}</h5>
                    <h6 style="text-transform: uppercase;">
                        {{ ($establishment->address !== '-')? $establishment->address : '' }}
                        {{ ($establishment->district_id !== '-')? ', '.$establishment->district->description : '' }}
                        {{ ($establishment->province_id !== '-')? ', '.$establishment->province->description : '' }}
                        {{ ($establishment->department_id !== '-')? '- '.$establishment->department->description : '' }}
                    </h6>

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
                </div>
            </td>
            <td width="30%" class="border-box py-4 px-2 text-center">
                <h5 class="text-center">CONTRATO</h5>
                <h3 class="text-center">{{ $tittle }}</h3>
            </td>
        @endif        
    </tr>
</table>
<table class="full-width mt-5">
    <tr>
        <td width="15%">Cliente:</td>
        <td width="45%">{{ $customer->name }}</td>
        <td width="25%">Fecha de emisión:</td>
        <td width="15%">{{ $document->date_of_issue->format('Y-m-d') }}</td>
    </tr>
    <tr>
        <td>{{ $customer->identity_document_type->description }}:</td>
        <td>{{ $customer->number }}</td>
        @if($document->date_of_due)
            <td width="25%">Fecha de vencimiento:</td>
            <td width="15%">{{ $document->date_of_due->format('Y-m-d') }}</td>
        @endif
    </tr>
    @if ($customer->address !== '')
    <tr>
        <td class="align-top">Dirección:</td>
        <td colspan="">
            {{ $customer->address }}
            {{ ($customer->district_id !== '-')? ', '.$customer->district->description : '' }}
            {{ ($customer->province_id !== '-')? ', '.$customer->province->description : '' }}
            {{ ($customer->department_id !== '-')? '- '.$customer->department->description : '' }}
        </td>
        @if($document->delivery_date)
            <td width="25%">Fecha de entrega:</td>
            <td width="15%">{{ $document->delivery_date->format('Y-m-d') }}</td>
        @endif
    </tr>
    @endif
    @if ($document->payment_method_type)
    <tr>
        <td class="align-top">T. Pago:</td>
        <td colspan="">
            {{ $document->payment_method_type->description }}
        </td>
        @if($document->quotation)
            <td width="25%">Cotización:</td>
            <td width="15%">{{ $document->quotation->number_full }}</td>
        @endif
    </tr>
    @endif
    @if ($document->account_number)
    <tr>
        <td class="align-top">N° Cuenta:</td>
        <td colspan="3">
            {{ $document->account_number }}
        </td>
    </tr>
    @endif
    @if ($document->shipping_address)
    <tr>
        <td class="align-top">Dir. Envío:</td>
        <td colspan="3">
            {{ $document->shipping_address }}
        </td>
    </tr>
    @endif
    @if ($customer->telephone)
    <tr>
        <td class="align-top">Teléfono:</td>
        <td colspan="3">
            {{ $customer->telephone }}
        </td>
    </tr>
    @endif
    <tr>
        <td class="align-top">Vendedor:</td>
        <td colspan="3">
            {{ $document->user->name }}
        </td>
    </tr>
</table>

<table class="full-width mt-3">
    @if ($document->description)
        <tr>
            <td width="15%" class="align-top">Descripción: </td>
            <td width="85%">{{ $document->description }}</td>
        </tr>
    @endif
</table>

@if ($document->guides)
<br/>
{{--<strong>Guías:</strong>--}}
<table>
    @foreach($document->guides as $guide)
        <tr>
            @if(isset($guide->document_type_description))
            <td>{{ $guide->document_type_description }}</td>
            @else
            <td>{{ $guide->document_type_id }}</td>
            @endif
            <td>:</td>
            <td>{{ $guide->number }}</td>
        </tr>
    @endforeach
</table>
@endif

<table class="full-width mt-10 mb-10">
    <thead class="">
    <tr class="bg-grey">
        <th class="border-top-bottom text-center py-2" width="8%">CANT.</th>
        <th class="border-top-bottom text-center py-2" width="8%">UNIDAD</th>
        <th class="border-top-bottom text-left py-2">DESCRIPCIÓN</th>
        <th class="border-top-bottom text-right py-2" width="12%">P.UNIT</th>
        <th class="border-top-bottom text-right py-2" width="8%">DTO.</th>
        <th class="border-top-bottom text-right py-2" width="12%">TOTAL</th>
    </tr>
    </thead>
    <tbody>
    @foreach($document->items as $row)
        <tr>
            <td class="text-center align-top">
                @if(((int)$row->quantity != $row->quantity))
                    {{ $row->quantity }}
                @else
                    {{ number_format($row->quantity, 0) }}
                @endif
            </td>
            <td class="text-center align-top">{{ $row->item->unit_type_id }}</td>
            <td class="text-left">
                
                @if($row->name_product_pdf)
                    {!!$row->name_product_pdf!!}
                @else
                    {!!$row->item->description!!} 
                @endif
                
                @if (!empty($row->item->presentation)) {!!$row->item->presentation->description!!} @endif
                
                @if($row->attributes)
                    @foreach($row->attributes as $attr)
                        <br/><span style="font-size: 9px">{!! $attr->description !!} : {{ $attr->value }}</span>
                    @endforeach
                @endif
                @if($row->discounts)
                    @foreach($row->discounts as $dtos)
                        <br/><span style="font-size: 9px">{{ $dtos->factor * 100 }}% {{$dtos->description }}</span>
                    @endforeach
                @endif

                @if($row->item->is_set == 1)
                 <br>
                @inject('itemSet', 'App\Services\ItemSetService')
                    {{join( "-", $itemSet->getItemsSet($row->item_id) )}}
                @endif

            </td>
            <td class="text-right align-top">{{ number_format($row->unit_price, 2) }}</td>
            <td class="text-right align-top">
                @if($row->discounts)
                    @php
                        $total_discount_line = 0;
                        foreach ($row->discounts as $disto) {
                            $total_discount_line = $total_discount_line + $disto->amount;
                        }
                    @endphp
                    {{ number_format($total_discount_line, 2) }}
                @else
                0
                @endif
            </td>
            <td class="text-right align-top">{{ number_format($row->total, 2) }}</td>
        </tr>
        <tr>
            <td colspan="6" class="border-bottom"></td>
        </tr>
    @endforeach
        @if($document->total_exportation > 0)
            <tr>
                <td colspan="5" class="text-right font-bold">OP. EXPORTACIÓN: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_exportation, 2) }}</td>
            </tr>
        @endif
        @if($document->total_free > 0)
            <tr>
                <td colspan="5" class="text-right font-bold">OP. GRATUITAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_free, 2) }}</td>
            </tr>
        @endif
        @if($document->total_unaffected > 0)
            <tr>
                <td colspan="5" class="text-right font-bold">OP. INAFECTAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_unaffected, 2) }}</td>
            </tr>
        @endif
        @if($document->total_exonerated > 0)
            <tr>
                <td colspan="5" class="text-right font-bold">OP. EXONERADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_exonerated, 2) }}</td>
            </tr>
        @endif
        @if($document->total_taxed > 0)
            <tr>
                <td colspan="5" class="text-right font-bold">OP. GRAVADAS: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_taxed, 2) }}</td>
            </tr>
        @endif
       @if($document->total_discount > 0)
            <tr>
                <td colspan="5" class="text-right font-bold">{{(($document->total_prepayment > 0) ? 'ANTICIPO':'DESCUENTO TOTAL')}}: {{ $document->currency_type->symbol }}</td>
                <td class="text-right font-bold">{{ number_format($document->total_discount, 2) }}</td>
            </tr>
        @endif
        <tr>
            <td colspan="5" class="text-right font-bold">IGV: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total_igv, 2) }}</td>
        </tr>
        <tr>
            <td colspan="5" class="text-right font-bold">TOTAL A PAGAR: {{ $document->currency_type->symbol }}</td>
            <td class="text-right font-bold">{{ number_format($document->total, 2) }}</td>
        </tr>
    </tbody>
</table>
<table class="full-width">
    <tr>
        <td width="65%" style="text-align: top; vertical-align: top;">
            <br>
            @foreach($accounts as $account)
                <p>
                <span class="font-bold">{{$account->bank->description}}</span> {{$account->currency_type->description}}
                <span class="font-bold">N°:</span> {{$account->number}}
                @if($account->cci)
                - <span class="font-bold">CCI:</span> {{$account->cci}}
                @endif
                </p>
            @endforeach
        </td>
    </tr>
    <tr>
        {{-- <td width="65%">
            @foreach($document->legends as $row)
                <p>Son: <span class="font-bold">{{ $row->value }} {{ $document->currency_type->description }}</span></p>
            @endforeach
            <br/>
            <strong>Información adicional</strong>
            @foreach($document->additional_information as $information)
                <p>@if(\App\CoreFacturalo\Helpers\Template\TemplateHelper::canShowNewLineOnObservation())
                            {!! \App\CoreFacturalo\Helpers\Template\TemplateHelper::SetHtmlTag($information) !!}
                        @else
                            {{$information}}
                        @endif</p>
            @endforeach
        </td> --}}
    </tr>
</table>
<br>
@if(!$document->quotation)
<table class="full-width">
<tr>
    <td>
    <strong>PAGOS:</strong> </td></tr>
        @php
            $payment = 0;
        @endphp
        @foreach($document->payments as $row)
            <tr><td>- {{ $row->payment_method_type->description }} - {{ $row->reference ? $row->reference.' - ':'' }} {{ $document->currency_type->symbol }} {{ $row->payment }}</td></tr>
            @php
                $payment += (float) $row->payment;
            @endphp
        @endforeach
        <tr><td><strong>SALDO:</strong> {{ $document->currency_type->symbol }} {{ number_format($document->total - $payment, 2) }}</td>
    </tr>

</table>
@endif
@if ($document->seller->name)
<br>
<table class="full-width">
    <tr>
        <td>
            <strong>Vendedor:</strong>
        </td>
    </tr>
    <tr>
        <td>{{ $document->seller->name }}</td>
    </tr>
</table>
@endif
</body>
</html>
