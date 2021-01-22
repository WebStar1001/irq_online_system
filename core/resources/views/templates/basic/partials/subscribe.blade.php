
<!-- Newslatter -->
<section class="section-padding gradient-overlay" id="subscribe">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-xl-8 centered cl-white">
                <div class="section-title mb-20">
                    <h2>@lang("Don't miss any update")</h2>
                </div>
                <div class="newslatter">
                    <form action="{{route('home.subscribe')}}" method="post">
                        @csrf
                        <input type="email" name="email" placeholder="@lang('Enter your email ..')" required>
                        <button type="submit" class=""><i class="fa fa-paper-plane"></i></button>
                    </form>
                    <p><i class="fa fa-info-circle"></i>@lang('We will never send spam')</p>
                </div>
            </div>
        </div>
    </div>
</section><!-- /Newslatter -->

