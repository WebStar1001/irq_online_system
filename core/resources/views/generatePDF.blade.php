<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>{{$basic->sitename}} </title>

    <style>
        @page {
            size: 8.27in 11.7in;
            margin: .5in;
        }
        .invoice-page-content-area {
            position: relative;
            left: -.5in;
            top: -.5in;
            width: 8.27in;
        }
        .foot{
            position: fixed;
            bottom: 0;
            left: -.5in;
        }

        .invoice-page-content-area {
            padding: 0; }
        .invoice-page-content-area .invoice-inner .footer-content {
            background-color: #000036;
            color: #fff;
            font-weight: 700;
            font-size: 14px;
            font-family: "Rubik", sans-serif;
            padding: 15px 0;
            text-align: center;
            text-transform: capitalize;
        }
        .invoice-page-content-area .invoice-inner .top-content {
            background-color: #232b37;
            padding: 30px; }
        .invoice-page-content-area .invoice-inner .top-content .subtitle {
            font-size: 14px;
            line-height: 24px;
            text-transform: uppercase;
            font-weight: 700;
            font-family: "Rubik", sans-serif;
            color: #9298a2;
            letter-spacing: 1px;
        }
        .invoice-page-content-area .invoice-inner .top-content .title {
            font-family: "Rubik", sans-serif;
            font-size: 30px;
            line-height: 30px;
            font-weight: 400;
            color: #fff;
            margin:0;}
        .invoice-page-content-area .invoice-inner .top-content .left-content {
            display: inline-block; }
        .invoice-page-content-area .invoice-inner .top-content .right-content {
            display: inline-block;
            float: right; }
        .invoice-page-content-area .invoice-inner .bottom-content {
            /*background-color: #f5f5f5;*/
            padding: 0px 40px}
        .invoice-page-content-area .invoice-inner .bottom-content .header-content .left-content {
            max-width: 250px;
            display: inline-block; }
        .invoice-page-content-area .invoice-inner .bottom-content .header-content .left-content .title {
            color: #232b37;
            font-size: 26px;
            line-height: 36px;
            font-family: "Rubik", sans-serif;
            font-weight: 500;
            margin-bottom: 25px; }
        .invoice-page-content-area .invoice-inner .bottom-content .header-content .left-content .details {
            font-size: 16px;
            line-height: 26px;
            color: #838b97;
            display: block;
            font-family: "Rubik", sans-serif;
            margin-bottom: 5px; }
        .invoice-page-content-area .invoice-inner .bottom-content .header-content .right-content {
            display: inline-block;
            min-width: 250px;
            float: right;
            font-family: "Rubik", sans-serif;
            margin-top: 60px; }
        .invoice-page-content-area .invoice-inner .bottom-content .header-content .right-content .rheading {
            display: block;
            font-size: 16px;
            line-height: 26px;
            font-weight: 600;
            color: #232b37;
            margin-bottom: 5px; }
        .invoice-page-content-area .invoice-inner .bottom-content .header-content .right-content .rheading .right {
            float: right;
            font-weight: 400;
            color: #838b97; }
        .invoice-page-content-area .invoice-inner .bottom-content .header-content .right-content .heading {
            display: block;
            font-size: 16px;
            line-height: 26px;
            font-weight: 400;
            color: #232b37;
            margin-bottom: 5px; }
        .invoice-page-content-area .invoice-inner .bottom-content .header-content .right-content .heading .right {
            float: right;
            font-weight: 400;
            color: #838b97; }
        .invoice-page-content-area .invoice-inner .bottom-content .body-content {
            padding: 30px 0 0 0; }
        .invoice-page-content-area .invoice-inner .bottom-content .body-content table {
            font-family: "Rubik", sans-serif; }

        .invoice-page-content-area .invoice-inner .bottom-content .body-content table thead tr th {
            background-color: #000036;
            border: none;
            font-size: 18px;
            font-family: "Rubik", sans-serif;
            font-weight: 400;
            color: #fff;
            padding: 4px 0; }

        .invoice-page-content-area .invoice-inner .bottom-content .body-content table tbody tr {
            background-color: #efefef; }
        .invoice-page-content-area .invoice-inner .bottom-content .body-content table tbody tr:nth-of-type(even) {
            background-color: #fff; }
        .invoice-page-content-area .invoice-inner .bottom-content .body-content table tbody tr td {
            border: none; }
        .invoice-page-content-area .invoice-inner .bottom-content .body-content table tbody tr td .service .title {
            color: #000000;
            font-size: 14px;
            font-weight: 400;
            line-height: 24px;
            margin-bottom: 0; }
        .invoice-page-content-area .invoice-inner .bottom-content .body-content table tbody tr td .service .subtitle {
            color: #838b97; }
        .invoice-page-content-area .invoice-inner .bottom-content .body-content .total {
            /*display: block;*/
            /*float: right;*/
            font-family: "Rubik", sans-serif;
            /*min-width: 420px;*/
            margin-right: 20px;
            /*clear: both;*/
            margin-top: 10px;
            text-align: right;
        }
        .invoice-page-content-area .invoice-inner .bottom-content .body-content .total .rheading {
            display: block;
            font-size: 16px;
            line-height: 26px;
            font-weight: 700;
            color: #838b97;
            /*margin-bottom: 5px;*/
            clear: both; }
        .invoice-page-content-area .invoice-inner .bottom-content .body-content .total .rheading .right {
            float: right;
            font-weight: 400;
            color: #000036; }

        .bgc{
            background-color: #000036 !important;
        }

        .clientdata .name{
            font-family: "Rubik", sans-serif;
            font-size: 20px;
            line-height: 20px;
            font-weight: 500;
            color: #fff;
        }

        .clientdata .email{
            font-family: "Rubik", sans-serif;
            font-size: 12px;
            line-height: 22px;
            color: #ccc;
            margin-top: 10px;
        }

        .clientdata .address{
            font-family: "Rubik", sans-serif;
            font-size: 12px;
            line-height: 22px;
            color: #ccc;
            margin-top: 10px;
        }
        .stat {
            color: #fff;
            font-family: "Rubik", sans-serif;
            font-size: 18px;
            line-height: 22px;
            font-weight: 500;
            margin-top: 20px;
            padding: 6px 15px !important;
            border-radius: 5px !important;
            display: inline-block;
            text-transform: uppercase;
        }
        .trxssubtitle {
            font-family: "Rubik", sans-serif;
            font-size: 20px;
            font-weight: 700;
            color: #000036;
            line-height: 50px;
        }
    </style>
</head>




<body>
<div class="invoice-page-content-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12">
                <div class="invoice-inner"><!-- invoice inner -->
                    <div class="top-content bgc"><!-- top content -->
                        <div class="left-content">
                            <img src="{{asset('assets/images/logoIcon/logo.png')}}" alt="{{$basic->sitename}}" style="width: 220px;">
                        </div>
                        <div class="right-content" style="margin-top: -15px;">
                            <span class="subtitle">Invoice</span>
                            <h3 class="title"> #{{$info->trx}}</h3>
                        </div>
                    </div><!-- //.top content -->

                    <div class="top-content"><!-- top content -->
                        <div class="left-content">
                            <span class="subtitle">Invoice To</span><br>
                            <div class="clientdata" style="max-width: 400px; margin-top: 20px !important;">
                                <span class="name">{{$info->name}} </span><br>
                                <span class="email">{{$info->email}}</span><br>
                                <span class="address">{{$info->address}} </span><br>
                            </div>
                        </div>
                        <div class="right-content" style="margin-top: -20px;">
                            <span class="subtitle">Total Amount</span><br>
                            <h3 class="title" style="margin-top: 20px !important;">{{$info->amount}} {{$info->currency->code}}</h3>
                            <span class="stat" style="@if($info->status==0)
                                background: #eb4d4b;
                            @elseif($info->status==1)
                                background: #2ecc71;
                            @elseif($info->status  ==-1)
                                background: #353b48;
                            @endif">
                                @if($info->status == 0)
                                    unpaid
                                @elseif($info->status == 1)
                                    paid
                                @elseif($info->status == -1)
                                    cancelled
                                @endif
              </span>
                        </div>
                    </div><!-- //.top content -->

                    <div class="bottom-content">
                        <div class="body-content">
                            <table class="table table-default" style="width: 100%;">
                                <thead>
                                <tr>
                                    <th style="text-align: center; width: 40px;">Sl#</th>
                                    <th style="text-align: center;">Description</th>
                                    <th style="text-align: center; width: 180px;">Amount</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php $i = 0; @endphp
                                @foreach(json_decode($info->details) as $key=>$value)
                                    <tr>
                                        <td style="text-align: center;">{{++$i}}</td>
                                        <td>
                                            <div class="service" style="margin: 4px 20px;">
                                                {{$key}}
                                            </div>
                                        </td>
                                        <td>
                                            <span style="margin: 4px 20px; text-align: right;"> {{$value}} {{$info->currency->code}}</span>
                                        </td>
                                    </tr>

                                    @php $i++ @endphp
                                @endforeach

                                <tr style="background-color: #000036; color:#fff; font-size: 20px;">
                                    <td colspan="2">
                                        <div class="service" style="margin: 4px 20px; text-align: right;">
                                            TOTAL
                                        </div>
                                    </td>
                                    <td>
                                        <span style="margin: 4px 20px;">{{$info->amount}} {{$info->currency->code}}</span>
                                    </td>
                                </tr>
                                </tbody>
                            </table>



                        </div>
                    </div>


                    <div class="foot">
                        <div class="footer-content">
                            {{$contact->value->contact_details}}
                        </div>
                    </div>
                </div><!-- //. invoice inner  -->
            </div>
        </div>
    </div>
</div>

</body>
</html>
