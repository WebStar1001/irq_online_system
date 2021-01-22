@extends(activeTemplate().'layouts.user')
@section('title','')
@section('content')

    <!--Dashboard area-->
    <section class="section-padding gray-bg">
        <div class="container">
            <div class="row">

                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <div class="dashboard-content" id="app">
                        <div class="row">
                            <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12" >
                                <div class="dashboard-inner-content">
                                    <div class="card">
                                        <h5 class="card-header">@lang('Send Money')</h5>
                                        <div class="card-body">

                                            @include(activeTemplate().'user.transfer.nav')


                                            <form action="#" method="post" accept-charset="utf-8">
                                                {{csrf_field()}}
                                                <input type="hidden" v-bind:value="protection" name="protection">
                                                <div class="row">
                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                        <label for="a-trans">@lang('Amount Transfer')</label>
                                                        <input type="text" class="amount" id="amount"  name="amount" value="{{old('amount')}}" v-model="amount" v-on:keyup.enter="amountCalc" v-on:change="amountCalc"
                                                               onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')"
                                                               placeholder="0.00">
                                                    </div>
                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                        <label class="currency">@lang('Currency')</label>
                                                        <select name="currency" id="currency" class="form-control form-control-lg" v-on:change="changeCurrency">
                                                            @foreach($currency as $data)
                                                            <option value="{{$data->id}}" data-resource="{{$data}}">{{$data->name}}</option>
                                                            @endforeach
                                                        </select>

                                                    </div>

                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                        <label for="charge">@lang('Charge'): <small
                                                            class="money_transfer_charge">{{$money_transfer->percent_charge}} %  + @{{currencyMoneyTransferFixCharge}} @{{selectCurrency.code}} </small></label>
                                                        <input type="text" class="form-control sum" v-model="sum" name="sum" value="{{old('sum')}}" readonly>
                                                    </div>
                                                    <div class="col-xl-8 col-lg-8 col-md-8 col-sm-6">
                                                        <label>@lang('Receiver Username / E-mail /Phone')</label>
                                                        <input type="text" class="form-control" placeholder="@lang('Receiver Username / Email /Phone')" id="receiver" value="{{old('receiver')}}"
                                                               name="receiver" v-model="receiver"  v-on:change="getReceiver">

                                                        <div v-html="message"></div>

                                                    </div>
                                                    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6">
                                                        <br>
                                                        <button type="button"  v-bind:class="[protection ? 'bg-success' : 'site-bg']"  class="mt-2 custom-btn" data-toggle="collapse" data-target="#protect" aria-expanded="false" aria-controls="protect"><span v-if="protection == true">@lang('Protected')</span> <span v-else> @lang('Protection')</span></button>
                                                    </div>
                                                    <div class="collapse " id="protect" aria-expanded="false">
                                                        <div class="col-md-12">
                                                            <div class="card text-white card-body site-bg">



                                                                <label>@lang('Code protection')</label>
                                                                <input type="text" class="form-control" name="code_protect" v-model="code_protect"
                                                                       onkeyup="this.value = this.value.replace (/^\.|[^\d\.]/g, '')"
                                                                       placeholder="Enter Four Digit Numeric Code" maxlength="4" value="{{old('code_protect')}}">

                                                                <div v-html="messageCode"></div>

                                                                <small class="form-text text-white">
                                                                    @lang("Transaction will be performed when the recipient enters the protection code. Send the protection code to the recipient when you make sure the deal is completed.")
                                                                </small>



                                                                <div class="card-footer">

                                                                    <div class="row justify-content-end">
                                                                        <div class="col-md-3">
                                                                            <button type="button" class="custom-btn"  @click="changeProtection" > <span v-if="protection == false"> <i class="fa fa-lock"></i> @lang('Protect Now')</span> <span v-else> <i class="fa fa-unlock"></i> @lang('Remove Protection')</span></button>
                                                                        </div>
                                                                    </div>
                                                                </div>


                                                            </div>
                                                        </div>
                                                    </div>

                                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-3">
                                                        <label for="textarea">@lang('Note for recipient')</label>
                                                        <textarea class="form-control" rows="5" name="note">{{old('note')}}</textarea>
                                                    </div>

                                                    <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12 mt-3">
                                                        <button class="custom-btn" type="submit" v-if="sendMoney">@lang('Send money')</button>
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

                moneyTransferPercentCharge : "{{$money_transfer->percent_charge}}",
                moneyTransferFixCharge : "{{$money_transfer->fix_charge}}",
                currencyMoneyTransferFixCharge : null,
                receiver: '',
                message: `<p class="text-info">@lang("User must be registered in the system")</p>`,
                messageCode: '',
                code_protect:null,
                protection: false,
                sendMoney: false,
                validUser: false,
                selectCurrency:{
                    id: null,
                    name: null,
                    code: null,
                    rate: null,
                }

            },

            mounted(){
                this.getReceiver();
                this.amountCalc();
                this.changeCurrency();

            },
            methods: {
                changeCurrency() {
                    var x = $("#currency option:selected").data('resource');
                    this.selectCurrency.id = x.id;
                    this.selectCurrency.name = x.name;
                    this.selectCurrency.code = x.code;
                    this.selectCurrency.rate = parseFloat(x.rate);
                    this.currencyMoneyTransferFixCharge = (this.moneyTransferFixCharge * parseFloat(x.rate)).toFixed(2);
                    this.amountCalc();
                },

                amountCalc(){
                    var _this = this;
                    var percentCharge = (this.amount * parseFloat(this.moneyTransferPercentCharge))/100;
                    var percentFix = parseFloat(percentCharge) + parseFloat(this.moneyTransferFixCharge*this.selectCurrency.rate);
                    if(_this.amount > 0){
                        _this.sum = percentFix.toFixed(2);
                    }else {
                        _this.sum = 0;
                    }

                    if(_this.validUser == true && _this.amount > 0){

                        _this.sendMoney =  true
                    }else {
                        _this.sendMoney =  false
                    }
                },

                changeProtection(){
                    if(this.protection ==  false){
                         this.protection = true;
                        $('.collapse').collapse('hide');
                    }else if(this.protection ==  true){
                         this.protection = false;
                        $('.collapse').collapse('hide');
                    }
                    return this.protection;
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
                                    _this.message = `<p class="text-danger">"${_this.receiver}" is not valid to send money</p>`;
                                    _this.sendMoney =  false
                                }else{
                                    _this.message = `<p class="text-success">"${_this.receiver}" is a valid user to send money </p>`;
                                    _this.validUser = true;
                                    if(_this.amount > 0){
                                        _this.sendMoney =  true
                                    }else {
                                        _this.sendMoney =  false
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
