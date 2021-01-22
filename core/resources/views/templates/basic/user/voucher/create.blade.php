@extends(activeTemplate().'layouts.user')
@section('title','')
@section('content')
    <section class="section-padding gray-bg blog-area" id="app">
        <div class="container">
            <div class="row">
                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-content">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="dashboard-inner-content">
                                    <div class="card">
                                        <h5 class="card-header">{{__($page_title)}}</h5>
                                        <div class="card-body">
                                            @include(activeTemplate().'user.voucher.nav')


                                            <form  class="" v-on:submit.prevent="checkCalc" v-if="feedBack != true">
                                                <div class="row mt-5">
                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                        <label for="a-trans"> @lang('Amount') </label>
                                                        <input type="text"  name="amount"  v-model="amount" @keypress="reInputAmo" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" >
                                                    </div>
                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                        <label>@lang('Currency')</label>
                                                        <select name="currency"  v-on:change="changeCurrency" id="currency" class="form-control form-control-lg">
                                                            @foreach($currency as $data)
                                                                <option value="{{$data->id}}" data-resource="{{$data}}"> {{$data->code}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                        <br>
                                                        <button type="button" class="mt-2 custom-btn" @click="checkCalc"  >@lang('Create Voucher')</button>
                                                    </div>

                                                </div>
                                            </form>



                                            <div class="result"  v-if="feedBack == true">
                                                <div v-html="calcResult"></div>

                                                <div class="row mt-2  justify-content-end">
                                                    <div class="col-md-3">
                                                        <button type="button" class=" btn btn-danger btn-block mt-1" @click="resetCal">@lang('Cancel')</button>
                                                    </div>

                                                    <div class="col-md-3">
                                                        <a href="{{route('user.vouchers.create_voucher')}}" class="btn  btn-block btn-success mt-1">@lang('Confirm')</a>
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


@endsection


@section('script')
    <script src="{{asset('assets/admin/js/axios.js')}}"></script>
    <script src="{{asset('assets/admin/js/vue.js')}}"></script>

    <script>
        var app = new Vue({
            el: "#app",
            data: {
                amount: null,
                percentCharge: "{{$voucher->new_voucher->percent_charge}}" ,
                fixedCharge: "{{$voucher->new_voucher->fix_charge}}",
                minimumAmo: "{{$voucher->new_voucher->minimum_amount}}",


                currencyFixedCharge: null,
                alertStatus: "{{$general->alert}}",
                feedBack: false,
                calcResult: null,
                selectCurrency:{
                    id: null,
                    name: null,
                    code: null,
                    rate: null,
                }

            },
            mounted(){
                 this.changeCurrency();
            },

            methods: {
                reInputAmo(){
                    this.feedBack =  false;
                },
                changeCurrency(){
                    var x = $("#currency option:selected").data('resource');
                    this.selectCurrency.id = x.id;
                    this.selectCurrency.name = x.name;
                    this.selectCurrency.code = x.code;
                    this.selectCurrency.rate = parseFloat(x.rate);
                    this.currencyFixedCharge = (this.fixedCharge * parseFloat(x.rate)).toFixed(2);

                    this.feedBack =  false;
                },
                resetCal(){
                    this.feedBack =  false;
                    this.amount= null
                },



                checkCalc: function () {

                    var _this = this;
                    if (this.amount == null || this.amount == ''){
                        if(this.alertStatus == 1){
                            iziToast.error({message:"Please enter amount", position: "topRight"});
                        }else if(this.alertStatus == 2){
                            toastr.error("Please enter amount");
                        }
                        return 0;
                    }
                    if (this.selectCurrency.id == null || this.selectCurrency.id == ''){
                        if(this.alertStatus == 1){
                            iziToast.error({message:"Currency Must Be Selected", position: "topRight"});
                        }else if(this.alertStatus == 2){
                            toastr.error("From Currency Must Be Selected");
                        }
                        return 0;
                    }

                    if(parseFloat(_this.minimumAmo * _this.selectCurrency.rate) >_this.amount){
                        var errorAmoMsg = "Minimum "+ parseFloat(_this.minimumAmo * _this.selectCurrency.rate) +" "+_this.selectCurrency.code+" need to make voucher";
                        if(this.alertStatus == 1){
                            iziToast.error({message:errorAmoMsg, position: "topRight"});
                        }else if(this.alertStatus == 2){
                            toastr.error(errorAmoMsg);
                        }
                        return 0;
                    }


                        axios.post("{{route('user.vouchers.preview')}}", {
                            _token: "{{csrf_token()}}",
                            amount: _this.amount,
                            currency: _this.selectCurrency.id,
                        })
                            .then(function (response) {
                                var result = response.data;
                                _this.feedBack =  result.feedBack;
                                var currCode = result.currency.code;

                                _this.calcResult = `<div class="row text-center mt-5">
                                                    <div class="col-md-4">
                                                        <strong>@lang('Amount')</strong>
                                                        <p class="pt-1">${result.amount} ${currCode} </p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong>@lang('Charge') ( ${result.percent_charge}% + ${result.fixed_charge} ) ${currCode}</strong>
                                                        <p class="pt-1">${result.total_charge} ${currCode}</p>
                                                    </div>
                                                    <div class="col-md-4">
                                                        <strong>@lang('Total Payable')</strong>
                                                        <p class="pt-1">${result.payable} ${currCode}</p>
                                                    </div>
                                                </div>`;


                                console.log(result)
                            })
                            .catch(function (error) {
                                console.log(error);
                            });


                }
            }
        });

    </script>
@endsection
