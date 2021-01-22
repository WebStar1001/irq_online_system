@extends(activeTemplate().'layouts.master')
@section('title',"$page_title")
@section('content')

    <!--breadcrumb area-->
    <section class="breadcrumb-area fixed-head blue-bg">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-xl-8 col-lg-8 col-md-12 col-sm-12 centered">
                    <div class="banner-title">
                        <h2>{{__($page_title)}}</h2>
                    </div>
                    <ul>
                        <li><a href="{{route('home')}}">@lang('Home')</a></li>
                        <li>{{__($page_title)}}</li>
                    </ul>
                </div>
            </div>
        </div>
    </section><!--/breadcrumb area-->







    <section class="section-padding">
        <div class="container">


            <div class="row">

                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <h2 class="section-title">Initiate API Payment</h2>

                    <strong>Endpoint: </strong> <span class="text-danger">{{route('express.initiate')}}</span> <br>
                    <strong>Method: </strong> <span class="text-danger">GET</span>

                    <div class="row">
                        <div class="col-md-7 mt-5">


                            <p style="font-size: 20px;">Just request to that endpoint with all parameter listed below: </p>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Parameter</th>
                                        <th scope="col">Details</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr class="">
                                        <td class="weight--medium">amount</td>
                                        <td>Your Amount , Must be rounded at 2 precision.</td>
                                    </tr>
                                    <tr class="">
                                        <td class="weight--medium">currency</td>
                                        <td>Currency Code, Must be in Upper Case.<br> Supported Currency : @foreach($currency as $cur) <code> {{$cur->code}} </code> @endforeach
                                            <div class="pm-markdown docs-request-table__desc"></div>
                                        </td>
                                    </tr>
                                    <tr class="">
                                        <td class="weight--medium">details</td>
                                        <td>Details of the Transaction. <br> <code>string</code> not more than 100 char.</td>
                                    </tr>
                                    <tr class="">
                                        <td class="weight--medium">custom</td>
                                        <td>Custom Code/Transaction Number for Identify at your end. <br> <code>string</code> not more than 16 char.</td>
                                    </tr>
                                    <tr class="">
                                        <td class="weight--medium">ipn_url</td>
                                        <td>URL of your IPN Listener</td>
                                    </tr>
                                    <tr class="">
                                        <td class="weight--medium">success_url</td>
                                        <td>Redirect url after successful Payment</td>
                                    </tr>
                                    <tr class="">
                                        <td class="weight--medium">cancel_url</td>
                                        <td>Redirect url after Cancel the Payment</td>
                                    </tr>
                                    <tr class="">
                                        <td class="weight--medium">public_key</td>
                                        <td>Your API Public Key</td>
                                    </tr>
                                    <tr class="">
                                        <td class="weight--medium">name</td>
                                        <td>Name of the Client. <br> <code>string</code> not more than 100 char.</td>
                                    </tr>
                                    <tr class="">
                                        <td class="weight--medium">email</td>
                                        <td>Email of the Client. <br> <code>string</code> not more than 100 char.</td>
                                    </tr>

                                    </tbody>
                                </table>
                                <code>** All Parameters are required!</code>
                            </div>

                        </div>

                        <div class="col-md-5 mt-5 text-white" style="background: #303030;">
                            <h5 class="my-3">Example PHP Code to Request</h5>


                            <pre style="color: #f1c40f;">
<span style="color:#bbb;">// create array With Parameters</span>
$parameters = array(
    'amount' => '10.33',
    'currency' => 'USD',
    'details' => 'Purchase Software',
    'custom' => 'ABCD1234',
    'ipn_url' => 'http://www.abc.com/ipn.php',
    'success_url' => 'http://www.abc.com/success.php',
    'cancel_url' => 'http://www.abc.com/cancelled.php',
    'public_key' => 'ABCDEFGH123456789',
    'name' => 'Mr. ABC XYZ',
    'email' => 'abc@abc.com'
);

<span style="color:#bbb;">// Generate The Url</span>
$endpoint = '{{route('express.initiate')}}';
$call = $endpoint . "?" . http_build_query($parameters);

<span style="color:#bbb;">// Send Request</span>
$ch = curl_init();
curl_setopt($ch, CURLOPT_AUTOREFERER, TRUE);
curl_setopt($ch, CURLOPT_HEADER, 0);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_URL, $add);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
$response = curl_exec($ch);
curl_close($ch);

<span style="color:#bbb;">// $response contain the response from server</span>

                            </pre>


                            <h5 class="my-3">Response (Error)</h5>


                            <pre style="color: #eb4d4b;">
{
    "error": "error",
    "message": {
        "amount": [
            "The amount format is invalid."
        ],
        "details": [
            "The details field is required."
        ]
    }
}

                            </pre>



                            <h5 class="my-3">Response (Success)</h5>


                            <pre style="color: #2ecc71;">

{
    "error": "ok",
    "message": "Payment Initiated. Redirect to url",
    "url": "{{route('express.payment',['UNIQUE_CODE'])}}"
}

                            </pre>




                        </div>


                    </div>

                </div>
            </div>


            <div class="row mt-5">

                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <h2 class="section-title">Get Payment</h2>

                    <p style="font-size: 20px;">After Successful response from last step, Redirect the User to the URL you get as response. User can transact over our system and we will notify on your IPN after Successful payment. Remember, we send response to IPN only once per successful Transaction. </p>

                </div>
            </div>


            <div class="row mt-5">

                <div class="col-xl-12 col-lg-12 col-md-12 col-sm-12">
                    <h2 class="section-title">IPN and Validate The Payment</h2>

                    <strong>Endpoint: </strong> <span class="text-danger">Your IPN URL</span> <br>
                    <strong>Method: </strong> <span class="text-danger">POST</span>

                    <div class="row">
                        <div class="col-md-7 mt-5">


                            <p style="font-size: 20px;">You will get below parameter on your IPN: </p>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead class="thead-dark">
                                    <tr>
                                        <th scope="col">Parameter</th>
                                        <th scope="col">Details</th>
                                    </tr>
                                    </thead>
                                    <tbody>

                                    <tr class="">
                                        <td class="weight--medium">amount</td>
                                        <td>Your Requested Amount</td>
                                    </tr>
                                    <tr class="">
                                        <td class="weight--medium">currency</td>
                                        <td>Currency Code, as You passed on first step.</td>
                                    </tr>
                                    <tr class="">
                                        <td class="weight--medium">custom</td>
                                        <td>Custom String for Identify the payment, as You passed on first step.</td>
                                    </tr>
                                    <tr class="">
                                        <td class="weight--medium">trx_num</td>
                                        <td>Transaction Number on our platform.</td>
                                    </tr>
                                    <tr class="">
                                        <td class="weight--medium">signature</td>
                                        <td>A Hash to Verify the Payment. See code on right side to get more on it.</td>
                                    </tr>


                                    </tbody>
                                </table>
                            </div>

                        </div>

                        <div class="col-md-5 mt-5 text-white" style="background: #303030;">
                            <h5 class="my-3">Example PHP Code to validate The Payment</h5>


                            <pre style="color: #f1c40f;">

$amount = $_POST['amount'];
$currency = $_POST['currency'];
$custom = $_POST['custom'];
$trx_num = $_POST['trx_num'];
$sentSign = $_POST['signature'];
<span style="color:#bbb;white-space: pre-wrap;">// with the 'custom' you can find your original amount and currency. just cross check yourself. if that check is pass, proceed to next step. </span>


<span style="color:#bbb;">// Generate your signature</span>
$string = $amount.$currency.$custom.$trx_num;
$secret = 'YOUR_SECRET_KEY';
$mySign = strtoupper(hash_hmac('sha256', $string , $secret));

if($sentSign == $mySign){
 <span style="color:#bbb;white-space: pre-wrap;">// if sentSign and your generated signature match, The Payment is verified. Never Share 'SECRET KEY' with anyone else.</span>
}

                            </pre>




                        </div>


                    </div>

                </div>
            </div>



        </div>
    </section>


@endsection

@section('style')
    <style>
        pre {
            line-height: 16px;
        }
    </style>
@endsection
