<?php

namespace App\Http\Controllers;

use App\Bank;
use App\Trx;
use App\Cart;
use App\User;
use App\Deposit;
use App\UserLogin;
use App\Withdrawal;
use App\SupportTicket;
use App\GeneralSetting;
use App\WithdrawMethod;
use App\StockistApplication;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Utils\CartService;
use App\Rules\FileTypeValidate;
use App\Lib\GoogleAuthenticator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Mail\GeneralApplicationMailable;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    protected $cartService;
    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function home()
    {
        $page_title = 'Dashboard';


        $user = Auth::user();
        $data['page_title'] = "Dashboard";
        $data['total_deposit'] = Deposit::whereUserId($user->id)->whereStatus(1)->sum('amount');
        $data['total_withdraw'] = Withdrawal::whereUserId($user->id)->whereStatus(1)->sum('amount');


        $data['total_orders'] = Trx::whereUserId($user->id)->whereType('payment')->count();
        $data['ref_com'] = Trx::whereUserId($user->id)->whereType(11)->sum('amount');
        $data['level_com'] = Trx::whereUserId($user->id)->whereType(4)->sum('amount');
        $data['total_epin_recharge'] = Trx::whereUserId($user->id)->whereType(9)->sum('amount');
        $data['total_epin_generate'] = Trx::whereUserId($user->id)->whereType(10)->sum('amount');
        $data['total_bal_transfer'] = Trx::whereUserId($user->id)->whereType(8)->sum('amount');
        $data['total_direct_ref'] = User::where('ref_id', $user->id)->count();

        $data['total_paid_width'] = User::where('position_id', $user->id)->count();


        if (\auth()->user()->ref_id != 0) {
            $data['ref_user'] = User::find(\auth()->user()->ref_id);
        }

        return view(activeTemplate() . 'user.dashboard', compact('page_title'), $data);
    }


    function profileIndex()
    {
        $data['page_title'] = "Account Settings";
        return view(activeTemplate() . '.user.profile', $data);
    }

    function passwordUpdate(Request $request)
    {


        $this->validate($request, [
            'current' => 'required|max:191',
            'password' => 'required|confirmed|max:191',
            'password_confirmation' => 'required|max:191'
        ]);
        $user = User::find(Auth::id());
        if (!Hash::check($request->current, $user->password)) {
            $notify[] = ['error', 'Current password does not match'];
            return back()->withNotify($notify);
        }
        if ($request->current == $request->password) {
            $notify[] = ['error', 'Current password and new password should not same'];
            return back()->withNotify($notify);
        }
        $user->password = Hash::make($request->password);
        $user->save();
        $notify[] = ['success', 'Password update successful'];
        return back()->withNotify($notify);
    }


    public function profile()
    {
        $page_title = 'Profile';
        return view(activeTemplate() . 'user.profile', compact('page_title'));
    }

    public function profileUpdate(Request $request)
    {
        $request->validate([
            'firstname' => 'required|max:160',
            'lastname' => 'required|max:160',
            'address' => 'nullable|max:160',
            'city' => 'nullable|max:160',
            'state' => 'nullable|max:160',
            'zip' => 'nullable|max:160',
            'country' => 'nullable|max:160',
            'image' => ['nullable', 'image', new FileTypeValidate(['jpeg', 'jpg', 'png'])],
            'store_address'=>'nullable|string',
            'store_city'=>'nullable|string',
            'store_state'=>'nullable|string',
            'store_zip'=>'nullable|string',
            'store_country'=>'nullable|string',
        ]);
        // dd($request->all());

        $filename = auth()->user()->image;
        if ($request->hasFile('image')) {
            try {
                $path = config('constants.user.profile.path');
                $size = config('constants.user.profile.size');
                $filename = upload_image($request->image, $path, $size, $filename);
            } catch (\Exception $exp) {
                $notify[] = ['success', 'Image could not be uploaded'];
                return back()->withNotify($notify);
            }
        }

        auth()->user()->update([
            'firstname' => $request->firstname,
            'lastname' => $request->lastname,
            'image' => $filename,
            'address' => [
                'address' => $request->address,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
                'country' => $request->country,
            ],
            'stockist_address' => [
                'address' => $request->store_address,
                'city' => $request->store_city,
                'state' => $request->store_state,
                'zip' => $request->store_zip,
                'country' => $request->store_country,
            ]
        ]);
        $notify[] = ['success', 'Your profile has been updated'];
        return back()->withNotify($notify);
    }

    public function passwordChange()
    {
        $page_title = 'Password Change';
        return view(activeTemplate() . 'user.password', compact('page_title'));
    }


    public function show2faForm()
    {
        $gnl = GeneralSetting::first();
        $ga = new GoogleAuthenticator();
        $user = auth()->user();
        $secret = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $gnl->sitename, $secret);
        $prevcode = $user->tsc;
        $prevqr = $ga->getQRCodeGoogleUrl($user->username . '@' . $gnl->sitename, $prevcode);
        $page_title = 'Google 2FA Auth';

        return view(activeTemplate() . 'user.go2fa', compact('page_title', 'secret', 'qrCodeUrl', 'prevcode', 'prevqr'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key' => 'required',
            'code' => 'required',
        ]);


        $ga = new GoogleAuthenticator();
        $secret = $request->key;
        $oneCode = $ga->getCode($secret);
        if ($oneCode === $request->code) {

            $user->tsc = $request->key;
            $user->ts = 1;
            $user->tv = 1;
            $user->save();

            if ($user->ev) {
                send_email($user, '2FA_ENABLE', [
                    'code' => $user->ver_code
                ]);
            } else {
                send_sms($user, '2FA_ENABLE', [
                    'code' => $user->ver_code
                ]);
            }

            $notify[] = ['success', 'Google Authenticator Enabled Successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['danger', 'Wrong Verification Code'];
            return back()->withNotify($notify);
        }
    }


    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user = auth()->user();
        $ga = new GoogleAuthenticator();

        $secret = $user->tsc;
        $oneCode = $ga->getCode($secret);
        $userCode = $request->code;

        if ($oneCode == $userCode) {

            $user->tsc = null;
            $user->ts = 0;
            $user->tv = 1;
            $user->save();

            if ($user->ev) {
                send_email($user, '2FA_DISABLE');
            } else {
                send_sms($user, '2FA_DISABLE');
            }

            $notify[] = ['success', 'Two Factor Authenticator Disable Successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong Verification Code'];
            return back()->with($notify);
        }
    }

    public function depositHistory()
    {

        $page_title = 'Deposit History';
        $empty_message = 'No history found.';
        $logs = auth()->user()->deposits()->with(['gateway'])->where([['method_code', '<', 1000], ['status', 1]])->orWhere('method_code', '>=', 1000)->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.deposit_history', compact('page_title', 'empty_message', 'logs'));
    }

    public function withdrawHistory()
    {
        $page_title = 'Withdraw History';
        $empty_message = 'No history found.';
        $logs = Withdrawal::where('status', 9)->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.withdraw_history', compact('page_title', 'empty_message', 'logs'));
    }

    public function withdraw()
    {


        $page_title = 'Withdraw';
        $methods = WithdrawMethod::where('status', 1)->get();
        $empty_message = 'No history found.';
        $logs = auth()->user()->withdrawals()->with(['method'])->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . 'user.withdraw', compact('page_title', 'methods', 'empty_message', 'logs'));
    }




    function transactions()
    {
        $data['page_title'] = "Transaction Log";
        $data['table'] = Trx::where('user_id', auth()->id())->orderBy('id', 'DESC')->paginate(config('constants.table.default'));
        return view(activeTemplate() . '.user.trans_history', $data);
    }

    public function balance_tf_log()
    {
        $data['page_title'] = 'Transferred Balance';
        $data['table'] = Trx::where('user_id', auth()->id())->where('type', 8)->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . '.user.trans_history', $data);
    }

    public function ref_com_Log()
    {
        $data['page_title'] = 'Referral Commission';
        $data['table'] = Trx::where('user_id', auth()->id())->where('type', 4)->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . '.user.trans_history', $data);
    }

    public function level_com_log()
    {
        $data['page_title'] = 'Level Commission';
        $data['table'] = Trx::where('user_id', auth()->id())->where('type', 11)->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . '.user.trans_history', $data);
    }


    public function withdrawInsert(Request $request)
    {


        $request->validate([
            'id' => 'required|integer',
            'amount' => 'required|numeric|gt:0',
        ]);
        $withdraw = WithdrawMethod::findOrFail($request->id);

        $multiInput = [];
        if ($withdraw->user_data != null) {
            foreach ($withdraw->user_data as $k => $val) {
                $multiInput[str_replace(' ', '_', $val)] = null;
            }
        }

        if ($request->amount < $withdraw->min_limit || $request->amount > $withdraw->max_limit) {
            $notify[] = ['error', 'Please follow the limit'];
            return back()->withNotify($notify);
        }

        if ($request->amount > auth()->user()->balance) {
            $notify[] = ['error', 'You do not have sufficient balance'];
            return back()->withNotify($notify);
        }

        $exchangeRate = $withdraw->rate > 1 ? 1 / $withdraw->rate : $withdraw->rate;
        $charge = $withdraw->fixed_charge + (($request->amount * $withdraw->percent_charge) / 100);
        $withoutCharge = $request->amount - $charge;
        $final_amo = formatter_money($withoutCharge * $exchangeRate);

        $data = new Withdrawal();
        $data->method_id = $request->id;
        $data->user_id = auth()->id();
        $data->amount = formatter_money($request->amount);
        $data->charge = formatter_money($charge);
        $data->rate = $withdraw->rate;
        $data->currency = $withdraw->currency;
        $data->delay = $withdraw->delay;
        $data->final_amo = $final_amo;
        $data->status = 0;
        $data->trx = getTrx();
        $data->save();
        Session::put('Track', $data->trx);
        return redirect()->route('user.withdraw.preview');
    }


    public function withdrawPreview()
    {
        $track = Session::get('Track');
        $data = Withdrawal::where('user_id', auth()->id())->where('trx', $track)->where('status', 0)->first();
        if (!$data) {
            return redirect()->route('user.withdraw');
        }
        $page_title = "Withdraw Preview";

        return view(activeTemplate() . 'user.withdraw_preview', compact('data', 'page_title'));
    }




    public function withdrawStore(Request $request)
    {


        $track = Session::get('Track');

        $withdraw = Withdrawal::where('user_id', auth()->id())->where('trx', $track)->orderBy('id', 'DESC')->first();



        $withdraw_method = WithdrawMethod::where('status', 1)->findOrFail($withdraw->method_id);


        if (!empty($withdraw_method->user_data)) {
            foreach ($withdraw_method->user_data as $data) {
                $validation_rule['ud.' . Str::slug($data)] = 'required';
            }
            $request->validate($validation_rule, ['ud.*' => 'Please provide all information.']);
        }



        $balance = auth()->user()->balance - $withdraw->amount;
        auth()->user()->update([
            'balance' => formatter_money($balance),
        ]);

        $withdraw->detail = $request->ud;
        $withdraw->status = 9;
        $withdraw->save();


        $trx = new Trx();
        $trx->user_id = auth()->id();
        $trx->amount = $withdraw->amount;
        $trx->charge = formatter_money($withdraw->charge);
        $trx->main_amo = formatter_money($withdraw->final_amo);
        $trx->balance = formatter_money(auth()->user()->balance);
        $trx->type = 'withdraw';
        $trx->trx = $withdraw->trx;
        $trx->title = 'withdraw Via ' . $withdraw->method->name;
        $trx->save();


        $general = GeneralSetting::first();

        send_email(auth()->user(), 'WITHDRAW_PENDING', [
            'trx' => $withdraw->trx,
            'amount' => $general->cur_sym . ' ' . formatter_money($withdraw->amount),
            'method' => $withdraw->method->name,
            'charge' => $general->cur_sym . ' ' . $withdraw->charge,
        ]);

        send_sms(auth()->user(), 'WITHDRAW_PENDING', [
            'trx' => $withdraw->trx,
            'amount' => $general->cur_sym . ' ' . formatter_money($withdraw->amount),
            'method' => $withdraw->method->name,
            'charge' => $general->cur_sym . ' ' . $withdraw->charge,
        ]);


        $notify[] = ['success', 'You withdraw request has been taken.'];


        return redirect()->route('user.home')->withNotify($notify);
    }


    function loginHistory()
    {
        $data['page_title'] = "Login History";
        $data['history'] = UserLogin::where('user_id', Auth::id())->latest()->paginate(config('constants.table.default'));
        return view(activeTemplate() . '.user.login_history', $data);
    }
    function orders()
    {
        $data['page_title'] = "My orders";
        $routeName = explode('/', request()->path());
        $route = $routeName[sizeof($routeName) - 1];
        $key = $this->cartService->convertRouteToCartFilterKey($route);
        $data['carts'] =  $this->cartService->getUserCarts($key);
        $data['general'] = GeneralSetting::first();
        return view(activeTemplate() . 'user.orders.list', $data);
    }
    function order(Cart $cart)
    {
        $data['page_title'] = "Order items";
        $data['cart'] =  $cart;
        $data['cartItems'] = $cart->cartItems()->get();
        return view(activeTemplate() . 'user.orders.order', $data);
    }

    public function applications()
    {
        $data['page_title'] = '"General" application';
        $data['applications'] = StockistApplication::all();      // dd($data['applications']);
        return view('admin.applications.index', $data);
    }

    public function viewApplication($id)
    {
        $application = StockistApplication::findOrFail($id);
        $page_title = "Application details";
        $bank = Bank::where('id', $application->bank_id)->first();

        return view('admin.applications.view', compact('page_title', 'application', 'bank'));
    }

    public function acceptApplication($id)
    {
        $application = StockistApplication::findOrFail($id);
        $username = substr(substr($application->country, 0, 2) . md5(rand()), 0, 6);
        $password = substr(md5(rand()), 0, 8);
        while (User::where('username', $username)->get()->count()) {
            $username = substr(substr($application->country, 0, 2) . md5(rand()), 0, 6);
        }
        // dd($application);
        $data['username'] = $username;
        $data['password'] = $password;
        $data['email'] = $application->email;
        $data['access_type'] = 'general';
        $data['firstname'] = $application->firstname;
        $data['lastname'] = $application->lastname;
        $data['mobile'] = $application->mobile;
        $data['bank_id'] = $application->bank_id;
        $data['bank_ac_no'] = $application->account_number;
        $data['address'] =  [
            'address' => $application['address'] ?? '',
            'state' => $application['state'] ?? '',
            'zip' => $application['zip'] ?? '',
            'country' => $application['country'] ?? '',
            'city' => $application['city'] ?? '',
        ];

        $data['stockist_address'] = [
            'address' => $application->store_address ?? '',
            'state' => $application->store_state ?? '',
            'zip' => $application->store_zip ?? '',
            'country' => $application->store_country ?? '',
            'city' => $application->store_city ?? '',
        ];
        // dd($data);
        User::create($data);
        $application->update(['status' => 'Accepted']);
        Mail::to($data['email'])->send(new GeneralApplicationMailable($data, 'approved'));
        $notify[] = ['success', 'User Approved successfully'];
        return back()->withNotify($notify);
    }

    public function declineApplication($id)
    {
        $application = StockistApplication::findOrFail($id);
        Mail::to($application->email)->send(new GeneralApplicationMailable(null, 'declined'));
        $notify[] = ['success', 'Application Declined'];
        return back()->withNotify($notify);
    }
}
