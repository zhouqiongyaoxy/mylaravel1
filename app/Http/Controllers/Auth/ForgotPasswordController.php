<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Guzzle\Http\Message\Request;

use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\Configs;


class ForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /*public function showLinkRequestForm(){
        // todo 做手机发送短信后,再跳到重置密码页面
        // 发短信需要专用承运商购买并写入phpsms对应的id之类的信息  http://www.jianshu.com/p/5454014c1b0c
        // 所以本次不做
       //return view('auth.passwords.phone');

    }*/

    public function showAnswerQuestionForm()
    {
        return view('auth.passwords.answerquestion');
    }

    //忘记密码时问题验证
    public function getRegisterQuestions(){
        $name = trim(Input::get('name'));
        if (empty($name)) {
            return ['code' => -1, 'msg' => '用户名长度不能为0或全空格'];
        }
        $user = DB::table('users')->where('name', $name)->first();
        if (empty($user)) {
            return ['code' => 0, 'msg' => '该用户不存在,请重新输入或先注册'];
        }
        $password_question_answers = $user->password_question_answers;
        $password_question_answers = json_decode($password_question_answers,true);
        $questions = Configs::passwordQuestion();
        $data = [];
        if ($password_question_answers) {
            foreach ($password_question_answers as $k=>$v) {
                $data[] = ['num'=> $k, 'ques' => $questions[$k]];
            }
        }
        return ['code' => 1,'data' => $data, 'msg' => '用户存在'];
    }


   /* //密保问题回答检验
    public function answerCheck(Request $request)
    {
        //$name = Input::post();
        print_r($request);exit;
    }*/

    public function sendResetInfo(Request $request)
    {
        /*$this->validateEmail($request);

        // We will send the password reset link to this user. Once we have attempted
        // to send the link, we will examine the response then see the message we
        // need to show to the user. Finally, we'll send out a proper response.
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($response)
            : $this->sendResetLinkFailedResponse($request, $response);*/
    }
}
