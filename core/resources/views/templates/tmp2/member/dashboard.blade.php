@extends(activeTemplate() .'member.layouts.app')

@section('content')



<div class="row">
    <div class="col-xl-4 col-lg-6 col-sm-6">
        <div class="dashboard-w2 slice border-radius-5" data-bg="2ecc71" data-before="27ae60"
            style="background: #2ecc71; --before-bg-color:#27ae60;">
            <div class="details">
                <h2 class="amount mb-2 font-weight-bold">{{$general->cur_sym}}{{formatter_money(Auth::user()->balance)}}
                </h2>
                <h4 class="mb-3">@lang('Current Balance')</h4>
                <a href="{{route('user.deposit.history')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-6 col-sm-6">
        <div class="dashboard-w2 slice bg-warning border-radius-5" data-bg="2ecc71" data-before="27ae60"
            style="background: #2ecc71; --before-bg-color:#27ae60;">
            <div class="details">
                <h2 class="amount mb-2 font-weight-bold">{{Auth::user()->point_value}} </h2>
                <h4 class="mb-3">@lang('Accumulated Product Rate')</h4>
                <a href="{{route('user.orders-completed')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>
            </div>
            <div class="icon">
                <i class="fa fa-history"></i>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-6 col-sm-6">
        <div class="dashboard-w2 slice bg-primary border-radius-5">
            <div class="details">
                <h2 class="amount mb-2 font-weight-bold">{{$general->cur_sym}}{{formatter_money($total_deposit)}} </h2>
                <h4 class="mb-3">@lang('Total Deposit')</h4>
                <a href="{{route('user.deposit.history')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
        </div>
    </div>


    {{--<div class="col-xl-4 col-lg-6 col-sm-6">--}}
    {{--<div class="dashboard-w2 slice bg-info border-radius-5">--}}
    {{--<div class="details">--}}
    {{--<h2 class="amount mb-2 font-weight-bold">{{$general->cur_sym}}{{formatter_money($total_withdraw)}} </h2>--}}
    {{--<h4 class="mb-3">@lang('Total Withdraw')</h4>--}}
    {{--<a href="{{route('user.withdraw')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>--}}
    {{--</div>--}}
    {{--<div class="icon">--}}
    {{--<i class="fa fa-money"></i>--}}
    {{--</div>--}}
    {{--</div>--}}
    {{--</div>--}}

    <div class="col-xl-4 col-lg-6 col-sm-6">
        <div class="dashboard-w2 slice bg-warning border-radius-5">
            <div class="details">
                <h2 class="amount mb-2 font-weight-bold">{{$general->cur_sym}}{{formatter_money($ref_com)}}</h2>
                <h4 class="mb-3">@lang('Total Referral Commission')</h4>
                <a href="{{route('user.level.com')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
        </div>
    </div>


    <div class="col-xl-4 col-lg-6 col-sm-6">
        <div class="dashboard-w2 slice bg-info border-radius-5">
            <div class="details">
                <h2 class="amount mb-2 font-weight-bold">{{$general->cur_sym}}{{formatter_money($level_com)}}</h2>
                <h4 class="mb-3">@lang('Total Level Commission')</h4>
                <a href="{{route('user.level.com')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>
            </div>
            <div class="icon">
                <i class="fa fa-money"></i>
            </div>
        </div>
    </div>



    <div class="col-xl-4 col-lg-6 col-sm-6">
        <div class="dashboard-w2 slice bg-blue border-radius-5">
            <div class="details">
                <h2 class="amount mb-2 font-weight-bold">{{$general->cur_sym}}{{formatter_money($total_bal_transfer)}}
                </h2>
                <h4 class="mb-3">@lang('Total Transferred Balance')</h4>
                <a href="{{route('user.balance.tf.log')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>
            </div>
            <div class="icon">
                <i class="fa fa-random"></i>
            </div>
        </div>
    </div>
    <div class="col-xl-4 col-lg-6 col-sm-6">
        <div class="dashboard-w2 slice bg-red border-radius-5">
            <div class="details">
                <h2 class="amount mb-2 font-weight-bold">{{$total_direct_ref}}</h2>
                <h4 class="mb-3">@lang('Total My Direct Referral')</h4>
                <a href="{{route('user.ref.index')}}" class="btn btn-sm btn-neutral">@lang('View all')</a>
            </div>
            <div class="icon">
                <i class="fa fa-sitemap"></i>
            </div>
        </div>
    </div>
</div>


@endsection