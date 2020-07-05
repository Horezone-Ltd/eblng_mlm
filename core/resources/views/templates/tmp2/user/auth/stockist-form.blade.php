@extends(activeTemplate().'layouts.user-master')
@push('style-lib')
<link rel="stylesheet" href="{{asset(activeTemplate(true) .'build/css/intlTelInput.css')}}">
<style>
    .intl-tel-input {
        width: 100%;
    }
</style>
@endpush
@section('panel')

<div class="signin-section pt-5">
    <div class="container-fluid">
        <div class="row justify-content-center align-items-center">
            <div class="col-md-6 ">
                <div class="login-area registration-form-area">
                    <div class="login-header-wrapper text-center">
                        <a href="{{url('/')}}"> <img class="logo"
                                src="{{ get_image(config('constants.logoIcon.path') .'/logo.png') }}" alt="image"> </a>
                        <p class="text-center admin-brand-text">@lang('General Application Form')</p>
                    </div>
                    <form action="{{ route('user.handle-stockist-application') }}" method="POST" class="login-form"
                        id="recaptchaForm" enctype="multipart/form-data">
                        @csrf
                        <div class="login-inner-block">

                            <div class="form-row">
                                <div class="frm-grp form-group col-md-6">
                                    <label>@lang('Title')</label>
                                    <select name="title" id="title" class="form-control">
                                        <option value="">Choose title</option>
                                        <option value="Dr.">Dr.</option>
                                        <option value="Mr.">Mr.</option>
                                        <option value="Mrs.">Mrs.</option>
                                        <option value="Miss.">Miss.</option>
                                        <option value="Prof.">Prof.</option>
                                        <option value="Chief">Chief</option>
                                    </select>
                                </div>
                                <div class="frm-grp form-group col-md-6">

                                    <label>@lang('Passport Photograph')</label>
                                    <input type="file" name="passport">
                                </div>
                                <div class="frm-grp form-group col-md-6">

                                    <label>@lang('Your first name')</label>
                                    <input type="text" value="{{old('firstname')}}"
                                        placeholder="@lang('Enter your first name')" name="firstname">
                                </div>

                                <div class="frm-grp form-group col-md-6">

                                    <label>@lang('Your last name')</label>
                                    <input type="text" value="{{old('lastname')}}"
                                        placeholder="@lang('Enter your last name')" name="lastname">
                                </div>
                                <div class="frm-grp form-group col-md-6">

                                    <label>@lang('Gender')</label>
                                    <select name="gender" id="gender">
                                        <option value="">Select Gender</option>
                                        <option value="Male">Male</option>
                                        <option value="Female">Female</option>
                                    </select>
                                </div>
                                <div class="frm-grp form-group col-md-6">
                                    <label>@lang('Your mobile')</label>
                                    <input type="text" value="{{old('mobile')}}"
                                        placeholder="@lang('Enter your mobile number')" name="mobile">
                                </div>

                                <div class="frm-grp form-group col-md-12">
                                    <label>@lang('Your email')</label>
                                    <input type="text" value="{{old('email')}}" placeholder="@lang('Enter your email')"
                                        name="email">
                                </div>

                                <div class="form-group text-white col-md-12">
                                    <h5>Address Details</h5>
                                </div>

                                <div class="frm-grp form-group col-md-6">
                                    <label>@lang('Your Country')</label>

                                    <select class="frm-grp" name="country">
                                        @include('partials.country')

                                    </select>
                                </div>

                                <div class="frm-grp form-group col-md-6">
                                    <label>@lang('State / Province')</label>
                                    <input type="text" name="state" value="{{old('state')}}"
                                        placeholder="@lang('State / Province')">
                                </div>
                                <div class="frm-grp form-group col-md-6">
                                    <label>@lang('City / Town')</label>
                                    <input type="text" name="city" value="{{old('city')}}"
                                        placeholder="@lang('City / Town')">
                                </div>
                                <div class="frm-grp form-group col-md-6">
                                    <label>@lang('Postal / Zip code')</label>
                                    <input type="text" name="zip" value="{{old('zip')}}"
                                        placeholder="@lang('Postal / Zip code')">
                                </div>
                                <div class="frm-grp form-group col-md-12">
                                    <label>@lang('Address')</label>
                                    <textarea name="address" id="address" cols="30" rows="2"
                                        class="form-control signin-textarea"
                                        placeholder="@lang('Enter residential address')">{{old('address')}}</textarea>
                                </div>

                                <div class="form-group text-white col-md-12">
                                    <h5>Store Location Details</h5>
                                </div>

                                <div class="frm-grp form-group col-md-6">
                                    <label>@lang('Your Country')</label>

                                    <select class="frm-grp" name="store_country">
                                        @include('partials.country')

                                    </select>
                                </div>

                                <div class="frm-grp form-group col-md-6">
                                    <label>@lang('State / Province')</label>
                                    <input type="text" name="store_state" value="{{old('store_state')}}"
                                        placeholder="@lang('State / Province')">
                                </div>
                                <div class="frm-grp form-group col-md-6">
                                    <label>@lang('City / Town')</label>
                                    <input type="text" name="store_city" value="{{old('store_city')}}"
                                        placeholder="@lang('City / Town')">
                                </div>
                                <div class="frm-grp form-group col-md-6">
                                    <label>@lang('Postal / Zip code')</label>
                                    <input type="text" name="store_zip" value="{{old('store_zip')}}"
                                        placeholder="@lang('Postal / Zip code')">
                                </div>
                                <div class="frm-grp form-group col-md-12">
                                    <label>@lang('Propose store location')</label>
                                    <textarea name="store_address" id="address" cols="30" rows="2"
                                        class="form-control signin-textarea"
                                        placeholder="@lang('Propose store location')">{{old('store_address')}}</textarea>
                                </div>
                                <div class="form-group text-white col-md-12">
                                    <h5>Bank Details</h5>
                                </div>
                                <div class="frm-grp form-group col-md-6">
                                    <label for="bank">@lang('Bank name')</label>
                                    <select id="bank" class="frm-grp" name="bank_id">
                                        <option value="0">Select a bank</option>
                                        @isset($banks)
                                        @php
                                        $old_bank = old('bank_id') ?? false
                                        @endphp
                                        @foreach($banks as $bank)
                                        @if($old_bank == $bank->id)
                                        <option value="{{$bank->id}}" selected>{{$bank->name}}</option>
                                        @else
                                        <option value="{{$bank->id}}">{{$bank->name}}</option>
                                        @endif
                                        @endforeach
                                        @endisset
                                    </select>
                                </div>
                                <div class="frm-grp form-group col-md-6">
                                    <label>@lang('Account number')</label>
                                    <input type="text" name="account_number" value="{{old('account_number')}}"
                                        placeholder="@lang('Account number')">
                                </div>
                            </div>
                            <div class="btn-area text-center">
                                <button type="submit" id="recaptcha" class="submit-btn">@lang('Send
                                    Application')</button>
                            </div>
                            <br>
                            <div class="d-flex mt-3 justify-content-between">
                                <a href="{{route('user.login')}}" class="forget-pass">@lang('Sign In')</a>
                                <a href="{{route('user.register')}}" class="forget-pass">@lang('Sign Up as a
                                    member')</a>
                            </div>


                    </form>
                </div>
            </div>

        </div>
    </div>
</div>

@endsection

@push('style-lib')
<link rel="stylesheet" href="{{asset(activeTemplate(true) .'users/css/signin.css')}}">
<style>
    .registration-form-area .frm-grp+.frm-grp {
        margin-top: 0;
    }

    .registration-form-area .frm-grp label {
        color: white !important;
        font-weight: 400;
    }

    .registration-form-area select {
        /* border: 1px solid #5220c5; */
        width: 100%;
        padding: 12px 20px;
        color: #ffffff;
        z-index: 9;
        background-color: #dddd;
        border-radius: 3px;
    }

    .registration-form-area select option {
        color: #ffffff;
    }
</style>
@endpush


@push('js')
<script>
    var timer = false;
        function getReferer(elem){

            if(timer !== false) clearTimeout(timer)
            if($(elem).val() !== ''){
                timer = setTimeout(() => {
                    $.ajax({
                        method:'post',
                        url:'{{route("user.get.referer")}}',
                        data:{username: $(elem).val()}
                    })
                        .then(res => getRefererCallback(res,elem))
                    timer = false;
                },2000)
            }

        }
        function getRefererCallback(data,elem){
            if(!data.success){
                notify(data.message,'error')
            }else{
                notify(data.message,'success')
                $(elem).siblings('.ref_id').val(data.user_id);
            }
        }
</script>
@endpush
