@extends(activeTemplate().'layouts.user')
@section('title','')
@section('content')
    <!--Dashboard area-->
    <section class="section-padding gray-bg" id="app">
        <div class="container">
            <div class="row">

                @include(activeTemplate().'partials.left-content')
                <div class="col-xl-9 col-lg-9 col-md-9 col-sm-12">
                    <div class="dashboard-content">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="dashboard-inner-content">
                                    <div class="card">
                                        <div class="card-header">
                                            <h5 class="card-title float-left">@lang('Exchange Currency ')</h5>
                                            <a href="{{route('user.exchangeLog')}}" title="@lang('Exchange Log')" class="btn  btn-sqr float-right"><i class="fa fa-th"></i> </a>
                                        </div>
                                        <div class="card-body">
                                            <form action="" @submit.prevent="checkCalc"  >
                                                <div class="row justify-content-end">
                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                        <label for="a-trans">@lang('Amount Exchange')</label>
                                                        <input type="text"  class="form-control form-control-lg" v-model="amount" id="amount"
                                                               onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')"
                                                               placeholder="0.00" v-on:keypress="reInputAmo">
                                                    </div>
                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                        <label for="currency">@lang('From Curreny')</label>
                                                        <select  v-model="from_currency_id" class="form-control form-control-lg"  @click="fromCurrency" :required="from_currency_id">
                                                            <option value="">@lang('Select Currency')</option>
                                                            @foreach($currency as $data)
                                                                <option value="{{$data->id}}">{{$data->code}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                        <label for="currency">@lang('To Currency')</label>
                                                        <select class="form-control form-control-lg"  v-model="to_currency_id" @click="toCurrency" :required="to_currency_id">
                                                            <option value="">@lang('Select Currency')</option>
                                                            @foreach($currency as $data)
                                                                <option value="{{$data->id}}">{{$data->code}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>
                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                        <button type="button" class="custom-btn" @click="checkCalc" v-if="feedBack != true">@lang('Check calculation')</button>
                                                    </div>
                                                </div>
                                            </form>


                                            <div v-html="calcData"></div>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section><!--/Dashboard area-->


@endsection


@section('script')

    <script src="{{asset('assets/admin/js/axios.js')}}"></script>
    <script src="{{asset('assets/admin/js/vue.js')}}"></script>

    <script>
        var app = new Vue({
            el: "#app",
            data: {
                amount: null,
                from_currency_id: null,
                to_currency_id: null,
                alertStatus: "{{$general->alert}}",
                feedBack: false,
                calcData: null

            },
            methods: {
                reInputAmo(){
                    this.feedBack = false;
                    this.calcData =  null
                },
                fromCurrency(){
                    this.feedBack = false;
                    this.calcData =  null
                },

                toCurrency(){
                    this.feedBack = false;
                    this.calcData =  null
                },

                checkCalc: function () {
                    var _this = this;
                    if (this.amount == null){
                        if(this.alertStatus == 1){
                            iziToast.error({message:"Please enter amount", position: "topRight"});
                        }else if(this.alertStatus == 2){
                            toastr.error("Please enter amount");
                        }
                        return 0;
                    }
                    if (this.from_currency_id == null || this.from_currency_id == ''){
                        if(this.alertStatus == 1){
                            iziToast.error({message:"From Currency Must Be Selected", position: "topRight"});
                        }else if(this.alertStatus == 2){
                            toastr.error("From Currency Must Be Selected");
                        }
                        return 0;
                    }
                    if (this.to_currency_id == null || this.to_currency_id == ''){
                        if(this.alertStatus == 1){
                            iziToast.error({message:"To Currency Must Be Selected", position: "topRight"});
                        }else if(this.alertStatus == 2){
                        toastr.error("To Currency Must Be Selected");
                        }
                        return 0;
                    }


                    if (this.from_currency_id ==  this.to_currency_id){
                        if(this.alertStatus == 1){
                            iziToast.error({message:"Same Currency not eligible to exchange", position: "topRight"});
                        }else if(this.alertStatus == 2){
                            toastr.error("Same Currency not eligible to exchange");
                        }

                        return 0;
                    }


                    if(this.amount != null && this.from_currency_id != null && this.to_currency_id != null){


                        axios.post("{{route('user.exchange.calculation')}}", {
                            _token: "{{csrf_token()}}",
                            amount: _this.amount,
                            from_currency_id: _this.from_currency_id,
                            to_currency_id: _this.to_currency_id
                        })
                        .then(function (response) {

                            _this.feedBack =response.data.feedBack;
                            var result = response.data;

                            console.log(result)

                            _this.calcData = `<div class="row mt-60">
                                                <div class="col-xl-6 col-sm-6">
                                                    <div class="single-exchange-value">
                                                        <h3>${result.fromCurrency.code} <span>${result.fromCurrency.name}</span></h3>
                                                        <h2>${result.amount.toFixed(2)}</h2>
                                                        <div class="exchange-rate-amoount">
                                                            Charge
                                                            <div class="exchange-rate-amoount-sapn">
                                                                <span><i class="fa fa-money" aria-hidden="true"></i> ${result.charge.toFixed(2)} ${result.fromCurrency.code}</span>

                                                                <span> @lang('Rate'): 1 ${result.fromCurrency.code} <i class="fa fa-exchange" aria-hidden="true"></i>  ${result.exchangeRate.toFixed(2)} ${result.toCurrency.code} </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="col-xl-6 col-sm-6">
                                                    <div class="single-exchange-value single-exchange-value-none text-right">
                                                        <h3>${result.toCurrency.code} <span>${result.toCurrency.name}</span> </h3>
                                                        <h2>${result.totalExchangeAmount.toFixed(2)}</h2>
                                                        <div class="exchange-rate-amoount">
                                                            <div class="exchange-rate-amoount-sapn">
                                                                <a href="{{route('user.exchange.confirm')}}" class="bttn-mid btn-fill">@lang('Exchange Now')</a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>`;


                        })
                        .catch(function (error) {
                            console.log(error);
                        });


                    }

                }
            }
        });

    </script>
@endsection
