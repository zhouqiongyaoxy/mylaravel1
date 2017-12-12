@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Register</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('register') }}">
                        {{ csrf_field() }}

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input id="name" onchange="nameCheck(this.value)" type="text" class="form-control" name="name" value="{{ old('name') }}" required autofocus>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                                <p id="name-check-prompt" style="display: none; color: red;">该用户已存在,请选用其它用户名</p>
                            </div>
                        </div>

                        {{--<div class="form-group{{ $errors->has('email') ? ' has-error' : '' }}">
                            <label for="email" class="col-md-4 control-label">E-Mail Address</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control" name="email" value="{{ old('email') }}" required>

                                @if ($errors->has('email'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('email') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>--}}

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" onchange="passwordCheck(this.value)" type="password" class="form-control" name="password" required>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                                <p id="password-check-prompt" style="display: none; color: red;">密码长度不能小于6位</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>

                            <div class="col-md-6">
                                <input id="password-confirm" onchange="passwordConfirm(this.value)" type="password" class="form-control" name="password_confirmation" required>
                                <p id="password-confirm-prompt" style="display: none; color: red;">两次输入的密码不一致</p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="password-question" class="col-md-4 control-label">Choose Password Question</label>

                            <div class="col-md-6">
                                <select name="first-question" class="form-control" id="first-question" onchange="changeSecondQuestion(this.value)">
                                    <option value="-1">Please Choose One Password Question</option>
                                    @foreach ($questions as $k=>$q)
                                        <option value="{{$k}}">{{ $q }}</option>
                                    @endforeach
                                </select>
                                <input id="first-question-answer" class="form-control" type="text" name="first-question-answer" required>
                                <select name="second-question" class="form-control" id="second-question" style="margin-top: 10px;">
                                    <option value="-1">Please Choose Another Password Question</option>
                                    @foreach ($questions as $k=>$q)
                                        <option value="{{$k}}">{{ $q }}</option>
                                    @endforeach
                                </select>
                                <input id="second-question-answer" class="form-control" type="text" name="second-question-answer" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Register
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

<script>
    function changeSecondQuestion(val) {
        var options = '<option value="-1">Please Choose Another Password Question</option>';
        var questions = @json($questions);
        for(var i in questions){
            if (i != val) {
                options += '<option value="' + i + '">' + questions[i] + '</option>';
            }
        }
        $("#second-question").html(options);
    }
    function nameCheck(val) {
        if (val.length > 0) {
            $.ajax({
                type: 'GET',
                url: '/registerNamecheck',
                data: {name: val},
                dataType: 'json',
                success: function ($data) {
                    if ($data.code != 1){
                        $("#name-check-prompt").text($data.msg);
                        $("#name-check-prompt").css('display','block');
                    } else {
                        $("#name-check-prompt").css('display','none');
                    }
                }
            })
        }
    }
    function passwordCheck(val) {
        if(val.length < 6) {
            $("#password-check-prompt").css('display','block');
        } else {
            $("#password-check-prompt").css('display','none');
        }
    }
    function passwordConfirm(val) {
        if (val != $("#password").val()) {
            $("#password-confirm-prompt").css('display','block');
        } else {
            $("#password-confirm-prompt").css('display','none');
        }
    }
</script>
