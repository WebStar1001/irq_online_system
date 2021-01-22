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

                                            <a href="{{ route('user.invoice')}}" class="bttn-small btn-emt float-right"><i class="fa fa-list-alt"></i> @lang('Invoice')</a>
                                        </h5>

                                        <div class="card-body">
                                            <form  action="{{route('user.invoice.create')}}" role="form" class="myform" method="post" enctype="multipart/form-data" id="recaptchaForm">
                                                {{csrf_field()}}




                                                <div class="row">
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="title">@lang('Invoice To')</label>
                                                            <input type="text" class="form-control" name="name" value="{{old('name')}}"  placeholder="@lang('Invoice To') ...">
                                                        </div>
                                                    </div>
                                                    <div class="col-md-6">
                                                        <div class="form-group">
                                                            <label for="title">@lang('E-mail Address')</label>
                                                            <input type="email" class="form-control" name="email" value="{{old('email')}}" placeholder="@lang('E-mail Address')" autocomplete="off">
                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <label>@lang('Currency')</label>
                                                        <select class="form-control" name="currency" id="currency">
                                                            @foreach($currency as $data)
                                                                <option value="{{$data->id}}" data-source="{{$data}}">{{__($data->code)}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-md-8">
                                                            <label for="title">@lang('Address')</label>
                                                            <input type="text" class="form-control" name="address" value="{{old('address')}}" placeholder="@lang('Address')">
                                                    </div>
                                                </div>

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
                                                    <div class="row justify-content-between details-column">
                                                        <div class="col-md-8">
                                                            <div class="form-group">
                                                                <input type="text" name="details[]" class="form-control memo-txt-field" placeholder="Details" required>
                                                            </div>
                                                        </div>

                                                        <div class="col-md-3">
                                                            <div class="form-group">
                                                                <input type="text" name="amount[]" onkeypress="return allowNegativeNumber(event);" class="form-control input-amount memo-txt-field" value="" placeholder="Amount" required autocomplete="off">
                                                            </div>
                                                        </div>
                                                        <div class="col-md-1">
                                                            <div class="form-group addTrashBtn">
                                                            </div>
                                                        </div>
                                                    </div>
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






                                                <div class="row  justify-content-center mt-4">
                                                    <div class="col-12 form-group text-center">
                                                        <button v-if="validAmount" type="submit" class="custom-btn" id="sendmoeny-btn">@lang('Create Invoice')</button>
                                                    </div>
                                                </div>
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


    <script>
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



                    let currencyData = $("#currency option:selected").data('source');

                    _this.currencySource =  currencyData;


                    var invoice_percent_charge = "{{ $charge->percent_charge}}";
                    var  invoice_fix_charge =  "{{ $charge->fix_charge}}" ;


                    $(document).on('change', '#currency', function () {
                        let currency = $(this).find("option:selected").text();
                        $('.currency').text(currency);

                        let currencyData =  $(this).find("option:selected").data('source');
                        _this.currencySource = currencyData;

                        _this.invoiceFixCharge = invoice_fix_charge * _this.currencySource.rate;
                        total_amount();

                    });


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
                        var trash = `<button type="button" class="remove btn btn-danger "><i class="fa fa-times"></i></button>`;
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
