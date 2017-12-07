<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Jobs\SendVerifyCode;

class ApiController extends Controller
{
    public function sendVerifyCode(Request $request)
    {
        $this->validate($request, ['phone' => 'required|size:11']);
        dispatch(new SendVerifyCode($request->phone));

        return ['success' => true];
    }
}
?>