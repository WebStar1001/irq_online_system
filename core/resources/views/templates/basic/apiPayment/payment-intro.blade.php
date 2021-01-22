<div class="row mb-4">
    <div class="col-md-12">
        <div class="invoice-box table-responsive bg-white">
            <table cellpadding="0" cellspacing="0">
                <tbody>
                <tr class="top">
                    <td colspan="3">
                        <table>
                            <tbody>
                            <tr>
                                <td class="title">
                                    <img src="{{get_image(config('constants.logoIcon.path') .'/logo.png')}}" style="width:100%; max-width:300px;" class="invoice-logo-filter">
                                </td>
                                <td></td>
                                <td>
                                    @lang('TRX') #: {{$apiPayment->transaction}}<br>
                                    @lang('Date'): {{date('d M Y h:i A ',strtotime($apiPayment->created_at))}}
                                    <br>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr class="information">
                    <td colspan="12">
                        <table>
                            <tbody>
                            <tr>
                                <td>
                                    <strong>@lang('Send To -')</strong> <span class="text-danger">{{$apiPayment->merchant->fullname}}</span><br>
                                    <strong>@lang('Send For -')</strong> <span class="text-danger">{{$allData->details}}</span><br>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>



