@extends(activeTemplate().'invoicePayment.layout')
@section('title','| '.$page_title)
@section('content')

    <section class="section-padding">
        <div class="container">


            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="invoice-box table-responsive">
                        <table cellpadding="0" cellspacing="0">
                            <tbody>
                            <tr class="top">
                                <td colspan="3">
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td colspan="3">
                                                <a href="{{route('getInvoice.pdf',$invoice->trx )}}" class="btn btn-success float-right"><i class="fa fa-download"></i> Download Invoice</a>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td class="title">
                                                <img src="{{get_image(config('constants.logoIcon.path') .'/logo.png')}}" style="width:100%; max-width:300px;" class="invoice-logo-filter">
                                            </td>
                                            <td></td>
                                            <td>
                                                @lang('TRX') #: {{$invoice->trx}}<br>
                                                @lang('Date'): {{date('d M Y h:i A ',strtotime($invoice->updated_at))}}
                                                <br>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>

                            <tr class="information">
                                <td colspan="9">
                                    <table>
                                        <tbody>
                                        <tr>
                                            <td>
                                                <strong>@lang('Sender')</strong><br>
                                                Name: {{$invoice->user->username}}<br>
                                                Phone: {{$invoice->user->phone }}<br>
                                                E-mail: {{$invoice->user->email }}
                                            </td>

                                            <td>
                                                <strong>@lang('Receiver')</strong><br>
                                                Name: {{$invoice->name }}<br>
                                                E-mail: {{$invoice->email }}<br>
                                                Address: {{$invoice->address }}
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>




                            <tr class="heading">
                                <td colspan="3">@lang('Details')</td>
                                <td>@lang('Amount')</td>
                            </tr>

                            @foreach($invoice_details as $k => $val)
                                <tr class="item">
                                    <td  colspan="3">{{$k}}</td>
                                    <td>   {{formatter_money($val)}} {{$invoice->currency->code }}</td>
                                </tr>
                            @endforeach

                            <tr class="total">
                                <td colspan="3"></td>
                                <td>
                                    <p>@lang('Charge'): {{formatter_money($invoice->charge)}} {{$invoice->currency->code}}</p>
                                </td>
                            </tr>

                            <tr class="total">
                                <td colspan="3"></td>
                                <td>
                                    <b>@lang('Total'): {{formatter_money($invoice->amount)}} {{$invoice->currency->code}}</b>
                                </td>
                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
            </div>


            <div class="row">

                <div class="col-lg-12">
                    <div class="contact-form-area form-area">



                        <div class="row mt-4 mb-4">
                            <div class="col-md-12">

                                <div class="card panel-custom">
                                    <div class="card-header myCard text-center">
                                        <h4 class="card-title text-white">Payment Preview </h4>
                                    </div>

                                    <div class="card-body text-center">

                                        <div class="row">

                                            <div class="col-md-4">
                                                <img src="http://ronnie.thesoftking.com/ewallet/assets/images/gateway/101.jpg" style="width:100%; margin:0 auto;">

                                            </div>
                                            <div class="col-md-8">

                                                <ul class="list-group">
                                                    <li class="list-group-item text-color"> Amount : 40.60
                                                        <strong>USD</strong>
                                                    </li>

                                                    <li class="list-group-item text-color"> Charge :
                                                        <strong>1.53 </strong>USD</li>
                                                    <li class="list-group-item text-color"> Payable :
                                                        <strong>42.13 </strong>USD</li>


                                                    <li class="list-group-item text-color"> In USD :
                                                        <strong>$42.13</strong>
                                                    </li>
                                                </ul>

                                                <div class="row mt-4">
                                                    <div class="col-md-6">
                                                        <a href="http://ronnie.thesoftking.com/ewallet/invoice/getPayment/1SUNvQRyGLQUfoNDSg7hC86qa5BoUQop" class="btn btn-danger btn-lg btn-block">Cancel</a>
                                                        <br>
                                                    </div>

                                                    <div class="col-md-6">
                                                        <a href="http://ronnie.thesoftking.com/ewallet/invoice/deposit-confirm" class="btn btn-primary custom-sbtn btn-lg btn-block" id="btn-confirm">Pay Now</a>
                                                    </div>

                                                </div>



                                            </div>


                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>



                    </div>
                </div>
            </div>
        </div>
    </section>



@stop
