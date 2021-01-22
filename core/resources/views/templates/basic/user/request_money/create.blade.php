@extends(activeTemplate().'layouts.user')
@section('title','')
@section('content')
    <!--Dashboard area-->
    <section class="section-padding gray-bg blog-area" id="app">
        <div class="container">
            <div class="row">

                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-content">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                <div class="dashboard-inner-content">
                                    <div class="card">
                                        <h5 class="card-header">@lang('Request Now')</h5>
                                        <div class="card-body">
                                           @include(activeTemplate().'user.request_money.nav')


                                            <form action="{{route('user.request-money.store')}}" method="post">
                                                @csrf
                                                <div class="row">
                                                    <div class="col-xl-7 col-lg-7 col-md-7 col-sm-6">
                                                        <label for="a-trans">@lang('Title')</label>
                                                        <input type="text" name="title" value="{{old('title')}}" placeholder="@lang('Title ...')">
                                                    </div>

                                                    <div class="col-xl-5 col-lg-5 col-md-5 col-sm-6">
                                                        <label for="a-trans">@lang("Receiver (username / E-mail /Phone)")</label>
                                                        <input type="text" placeholder="@lang('Receiver Username / Email /Phone')"  id="receiver" name="receiver"  v-model="receiver"  v-on:change="getReceiver" value="{{old('receiver')}}">
                                                        <div v-html="message"></div>
                                                    </div>


                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                        <label for="currency">@lang('Currency')</label>
                                                        <select name="currency" id="currency" class="form-control form-control-lg" v-on:change="changeCurrency">

                                                            @foreach($currency as $data)
                                                                <option value="{{$data->id}}" data-resource="{{$data}}">{{__($data->code)}}</option>
                                                            @endforeach
                                                        </select>
                                                    </div>

                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                        <label for="amount">@lang('Request Amount')</label>
                                                        <input type="text" class="amount"  v-model="amount"  v-on:keyup.enter="amountCalc" v-on:keypress="reInputAmo" v-on:change="amountCalc" name="amount" value="{{old('amount')}}" id="amount" onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')" placeholder="0.00">
                                                    </div>


                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                        <label for="charge">@lang('Charge'): <small
                                                                class="money_transfer_charge">{{$request_money->percent_charge}} %  + @{{currencyMoneyRequestFixCharge}} @{{selectCurrency.code}} </small></label>
                                                        <input type="text" class="sum" name="sum" v-model="sum" value="{{old('sum')}}" readonly>

                                                    </div>


                                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                                                        <label for="textarea">@lang('Description')</label>
                                                        <textarea name="info"  rows="4"></textarea>
                                                    </div>

                                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-3">
                                                        <button  type="submit" class="custom-btn" v-if="reqMoneyBtn">@lang('Send Money Request')</button>
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
    </section><!--/Dashboard area-->
@endsection


@section('script')
    <script src="{{asset('assets/admin/js/axios.js')}}"></script>
    <script src="{{asset('assets/admin/js/vue.js')}}"></script>

    <script>


        var app = new Vue({
            el: "#app",
            data:{
                amount: 0,
                sum: 0,
                reqMoneyPercentCharge : "{{$request_money->percent_charge}}",
                reqMoneyFixCharge : "{{$request_money->fix_charge}}",

                currencyMoneyRequestFixCharge: 0,

                receiver: '',
                message: `<p class="text-muted">@lang('User must be registered in the system')</p>`,
                reqMoneyBtn: false,
                validUser: false,
                selectCurrency:{
                    id: null,
                    name: null,
                    code: null,
                    rate: null,
                }

            },

            mounted(){
                this.amountCalc();
                this.changeCurrency();

            },
            methods: {
                reInputAmo(){
                    this.sum = 0;
                },

                changeCurrency() {
                    var x = $("#currency option:selected").data('resource');
                    this.selectCurrency.id = x.id;
                    this.selectCurrency.name = x.name;
                    this.selectCurrency.code = x.code;
                    this.selectCurrency.rate = parseFloat(x.rate);
                    this.currencyMoneyRequestFixCharge = (this.reqMoneyFixCharge * parseFloat(x.rate)).toFixed(2);
                    this.amountCalc();
                },

                amountCalc(){
                    var _this = this;
                    var percentCharge = (this.amount * this.reqMoneyPercentCharge)/100;
                    var percentFix = percentCharge + parseFloat(this.reqMoneyFixCharge*this.selectCurrency.rate);
                    if(_this.amount > 0){
                        _this.sum = percentFix.toFixed(2);
                    }else {
                        _this.sum = 0;
                    }

                    if(_this.validUser == true && _this.amount > 0){
                        _this.reqMoneyBtn =  true
                    }else {
                        _this.reqMoneyBtn =  false
                    }
                },

                getReceiver(e){
                    var _this = this;
                    var username = this.receiver;
                    if (username.length > 0) {
                        axios.post("{{route('check.valid.user')}}", {
                            username,
                            _token: "{{csrf_token()}}"
                        })
                            .then(function (response) {
                                var output = response.data;

                                if(output.result == 'error'){
                                    _this.message = `<p class="text-danger">"${_this.receiver}" @lang('is not valid to Request money')</p>`;
                                    _this.reqMoneyBtn =  false
                                }else{
                                    _this.message = `<p class="text-success">"${_this.receiver}" @lang('is a valid user to Request money') </p>`;
                                    _this.validUser = true;
                                    if(_this.amount > 0){
                                        _this.reqMoneyBtn =  true
                                    }else {
                                        _this.reqMoneyBtn =  false
                                    }
                                }
                            })
                            .catch(function (error) {
                                console.log(error);
                            });
                    }else {
                        _this.message= `<p class="text-muted">@lang("User must be registered in the system")</p>`

                    }
                }
            }
        });


    </script>


@endsection
