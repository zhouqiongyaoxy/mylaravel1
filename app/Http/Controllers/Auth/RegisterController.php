<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;
use App\Http\Helper\Configs;
use Illuminate\Support\Facades\DB;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    //复写RegistersUsers里的此方法
    public function showRegistrationForm()
    {
        $questions = Configs::passwordQuestion();
        return view('auth.register',['questions' => $questions]);
    }

    //注册时名称唯一性验证
    public function namecheck()
    {
        $params = Input::get();
        $params['name'] = trim($params['name']);
        if (empty($params['name'])) {
            return ['code' => -1, 'msg' => '用户名长度不能为0或全空格'];
        }
        $user = DB::table('users')->where('name', $params['name'])->first();
        if (!empty($user)) {
            return ['code' => 0, 'msg' => '该用户已存在,请选用其它用户名'];
        }
        return ['code' => 1, 'msg' => '用户名唯一性验证通过'];
    }
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|string|max:255',
            //'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        $secretQues = [];
        if ($data['first-question'] != -1) {
            $secretQues[1] = $data['first-question-answer'];
        }
        if ($data['second-question'] != -1) {
            $secretQues[2] = $data['second-question-answer'];
        }
       // print_r($data);print_r(json_encode($secretQues));exit();
        return User::create([
            'name' => $data['name'],
            //'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'password_question_answers'  => json_encode($secretQues),
        ]);
    }
}
