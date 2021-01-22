@extends(activeTemplate().'layouts.user')
@section('title','')
@section('content')

    <section class="section-padding gray-bg blog-area" id="app">
        <div class="container">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="dashboard-inner-content">

                                    <div class="card">
                                        <h5 class="card-header myCard"> {{__($page_title)}}

                                            <div class="float-right">
                                                @if($info->status == 1)
                                                        <span class="badge badge-success">@lang('Paid')</span>
                                                @elseif($info->status == -1)

                                                    <span class="badge badge-danger">@lang('Cancel')</span>
                                                @else
                                                    <a href="javascript:void(0)" class="btn btn-warning" data-toggle="modal" data-target="#cancelModal" title="Cancel Invoice"><i class="fa fa-hand-pointer-o"></i> @lang('UnPaid')</a>
                                                @endif

                                            </div>

                                        </h5>

                                        <div class="card-body">
                                            <form action="{{route('user.invoice.Update',encrypt($info->id))}}" method="post" class="myform" name="editForm">
                                                @csrf
                                                {{method_field('put')}}


                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="title">@lang('Invoice To')</label>
                                                            <input type="text" class="form-control" name="name" value="{{$info->name }}"  placeholder="@lang('Invoice To') ...">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="title">@lang('E-mail Address')</label>
                                                            <input type="email" class="form-control" name="email"  value="{{$info->email}}" placeholder="@lang('E-mail Address')" autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label>@lang('Currency') </label>
                                                        <select class="form-control" name="currency" id="currency">
                                                            @foreach($currency as $data)
                                                                <option value="{{$data->id}}" data-source="{{$data}}" @if($info->currency_id == $data->id) selected @endif>{{__($data->code)}}</option>
                                                            @endforeach
                                                        </select>

                                                    </div>
                                                    <div class="col-md-8">
                                                        <label for="title">@lang('Address')</label>
                                                        <input type="text" class="form-control" name="address" value="{{$info->address}}" placeholder="@lang('Address')">
                                                    </div>
                                                </div>



                                                <input type="hidden" name="published" value="0">



                                                <div class="row mt-4 justify-content-between">
                                                    <div class="col-md-9">
                                                        <h6 class="mb-3">@lang('Invoice Details')</h6>
                                                    </div>
                                                    <div class="col-md-1 ">
                                                        <button type="button" class="addNewRow btn btn-success mb-3 float-right">
                                                            <i class="fa fa-plus"></i>
                                                        </button>
                                                    </div>
                                                </div>

                                                <div id="addField">
                                                    @php $i = 0 @endphp
                                                    @foreach($info_details as $key=>$value)
                                                    <div class="row justify-content-between details-column">
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <input type="text" name="details[]" class="form-control memo-txt-field" value="{{$key}}" placeholder="Details" required>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <input type="text" name="amount[]" onkeypress="return allowNegativeNumber(event);"   value="{{$value}}" class="form-control input-amount memo-txt-field"  placeholder="Amount" required autocomplete="off">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-group addTrashBtn">
                                                                @if($i != 0)
                                                                <button type="button" class="remove btn btn-danger "><i class="fa fa-times"></i></button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>

                                                        @php  ++$i @endphp
                                                    @endforeach

                                                </div>

                                                <div class="row mt-3 ">


                                                    <div class="from-group col-md-4">
                                                        <label for="title">@lang('Charge') (@{{invoiceFixCharge}} <span class="currency"></span> + {{$charge->percent_charge}} %
                                                            )</label>
                                                        <div class="input-group ">
                                                            <input type="text" name="charge" class="form-control charge" id="charge" value="" readonly>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text currency"></span>
                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="from-group col-md-4">
                                                        <label>@lang('You Will Get')</label>
                                                        <div class="input-group">
                                                            <input type="text" name="will_get" class="form-control" id="will_get" value="" readonly>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text currency"></span>
                                                            </div>
                                                        </div>
                                                    </div>


                                                    <div class="from-group col-md-4">
                                                        <label>@lang('Total')</label>
                                                        <div class="input-group">
                                                            <input type="text" name="total_amount" class="form-control" id="total_amount" value="" readonly>
                                                            <div class="input-group-append">
                                                                <span class="input-group-text currency"></span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>


                                                @if($info->status != -1)
                                                <div class="row mt-5 justify-content-start">
                                                    <div class="col-md">
                                                        <div class="form-group ">
                                                            <label>@lang('Payment Url'):</label>
                                                            <div class="input-group">
                                                                <input type="text" value="{{route('getInvoice.payment', $info->trx)}}"
                                                                       class="form-control  payment-status" id="copyInput">
                                                                <div class="input-group-append">
                                                                    <button onclick="copyFunction()" type="button"
                                                                            class="btn btn-success">@lang('Copy URL')</button>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                @endif
                                                <div class="row mt-5 justify-content-start">

                                                    @if($info->status != -1)
                                                    <div class="col-md">
                                                        <div class="form-group ">
                                                            <a href="{{route('user.invoice.sendmail',encrypt($info->id))}}"
                                                               class="btn btn-success btn-block btn-lg">@lang('Send To Mail')
                                                            </a>
                                                        </div>
                                                    </div>


                                                    <div class="col-md">
                                                        <div class="form-group ">
                                                            <a href="{{route('getInvoice.pdf',$info->trx)}}"  class="btn btn-primary btn-block btn-lg"> @lang('Download') </a>
                                                        </div>
                                                    </div>

                                                    @endif


                                                        @if($info->status == 0)

                                                        <div class="col-md">
                                                            <div class="form-group ">
                                                                <button type="button" data-toggle="modal" data-target="#cancelModal" class="btn btn-danger btn-block btn-lg"> @lang('Cancel Invoice') </button>

                                                            </div>
                                                        </div>

                                                        @endif


                                                        @if($info->status != -1)
                                                        @if($info->published == 0)
                                                        <div class="col-md">
                                                            <div class="form-group ">
                                                                <button type="button" data-toggle="modal" data-target="#publishedModal" class="btn btn-secondary btn-block btn-lg"> @lang('Publish ') </button>
                                                            </div>
                                                        </div>
                                                        @endif
                                                            @endif

                                                </div>






                                            @if($info->published == 0)
                                                @if($info->status == 0)
                                                <div class="row  justify-content-center mt-4">
                                                    <div class="col-12 form-group text-center">
                                                        <button v-if="validAmount" type="submit" class="custom-btn" id="sendmoeny-btn">@lang('Update Invoice')</button>
                                                    </div>
                                                </div>
                                                    @endif
                                                @endif
                                            </form>
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





    <!-- Invoice Publish Modal -->
    <div class="modal fade" id="publishedModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('Invoice Publish Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <strong>@lang('Do you want to publish this?')</strong>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success publishedModal" name="publishedModal" value="1"
                            data-dismiss="modal">@lang('Yes')</button>
                    <button type="button" class="btn btn-danger publishedModal" name="publishedModal" value="0"
                            data-dismiss="modal">@lang('No')</button>
                </div>
            </div>
        </div>
    </div>


    <!-- Invoice Publish Modal -->
    <div class="modal fade" id="cancelModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">@lang('Invoice Cancel Confirmation')</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>


                <form action="{{route('user.invoice.cancel',encrypt($info->id))}}" method="post">
                    {{csrf_field()}}
                    {{method_field('PUT')}}
                    <div class="modal-body">
                        <strong>@lang('Do you want to cancel this?')</strong>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn-success">@lang('Yes')</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">@lang('No')</button>
                    </div>

                </form>


            </div>
        </div>
    </div>



    <script>
        function copyFunction() {
            var copyText = document.getElementById("copyInput");
            copyText.select();
            document.execCommand("copy");
        }

        function allowNegativeNumber(e) {
            var charCode = (e.which) ? e.which : event.keyCode
            if (charCode > 31 && (charCode < 45 || charCode > 57 || charCode == 106 || charCode == 111)) {
                return false;
            }
            return true;
        }
    </script>
@endsection



@section('script')
    <script src="{{asset('assets/admin/js/axios.js')}}"></script>
    <script src="{{asset('assets/admin/js/vue.js')}}"></script>

    <script>
        var app = new Vue({
            el: "#app",
            data: {
                total_amount: 0,
                validAmount: false,
                invoiceFixCharge: 0,
                currencySource: {},
            },
            mounted() {
                var _this = this;
                $(document).ready(function () {
                    let currency = $("#currency option:selected").text();
                    $('.currency').text(currency);

                    let currencyData =  $(this).find("option:selected").data('source');
                    _this.currencySource = currencyData;


                    $(document).on('change', '#currency', function () {
                        let currency = $(this).find("option:selected").text();
                        $('.currency').text(currency);

                        let currencyData =  $(this).find("option:selected").data('source');
                        _this.currencySource = currencyData;

                        _this.invoiceFixCharge = invoice_fix_charge * _this.currencySource.rate;
                        total_amount();

                    });

                    $('.publishedModal').on('click', function (e) {
                        e.preventDefault();
                        if ($(this).val() == 1) {
                            $('.published_log').text('Yes');
                            $('input[name="published"]').val(1);
                        }
                        if ($(this).val() == 0) {
                            $('.published_log').text('No');
                            $('input[name="published"]').val(0);
                        }
                    });


                    var invoice_percent_charge = "{{ $charge->percent_charge}}";
                    var invoice_fix_charge = "{{ $charge->fix_charge}}";;

                    total_amount();



                    _this.invoiceFixCharge = invoice_fix_charge * _this.currencySource.rate;

                    total_amount();


                    function total_amount() {
                        var total_amount = 0;
                        $('.input-amount').each(function () {
                            total_amount += Number($(this).val());
                        });

                        let charge = (((total_amount * parseFloat(invoice_percent_charge)) / 100) + parseFloat(_this.invoiceFixCharge));
                        $('.charge').val(charge.toFixed(2));

                        var calc = (parseFloat(total_amount)).toFixed(2);
                        $('#total_amount').val(calc);

                        var willGet = parseFloat(total_amount-charge).toFixed(2);
                        $('#will_get').val(willGet);

                        _this.total_amount = calc;
                        if (_this.total_amount > 0) {
                            _this.validAmount = true
                        } else {
                            _this.validAmount = false
                        }

                    }

                    $(document).on('change, keyup', '.input-amount', function () {
                        total_amount();
                    });

                    $('.addNewRow').on('click', function () {
                        var rowFrm = $( ".details-column:eq(0)").clone();
                        rowFrm.find('.memo-txt-field').val('');
                        var trash = `<button type="button" class="remove btn btn-danger "><i class="fa fa-times"></i></button>`
                        $('#addField').append(rowFrm).find('.addTrashBtn:eq(-1)').append(trash);
                    });

                    $(document).on('click','.remove', function () {
                        var parnetInd =  $(this).parents('.details-column').index();
                        if(parnetInd != 0){
                            $(this).parents('.details-column').remove();
                            total_amount();
                        }
                    });

                });


            },
            methods: {}
        })
    </script>
@endsection

