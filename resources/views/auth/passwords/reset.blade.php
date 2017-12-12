@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Reset Password</div>

                <div class="panel-body">
                    <form class="form-horizontal" method="POST" action="{{ route('password.savepassword') }}">
                        {{ csrf_field() }}

                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" readonly="readonly" name="name" value="{{ $name }}" required autofocus>
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password') ? ' has-error' : '' }}">
                            <label for="password" class="col-md-4 control-label">Password</label>

                            <div class="col-md-6">
                                <input id="password" onchange="passwordCheck(this.value)"  type="password" class="form-control" name="password" required>
                                <p id="password-check-prompt" style="display: none; color: red;">密码长度不能小于6位</p>
                                @if ($errors->has('password'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group{{ $errors->has('password_confirmation') ? ' has-error' : '' }}">
                            <label for="password-confirm" class="col-md-4 control-label">Confirm Password</label>
                            <div class="col-md-6">
                                <input id="password-confirm" onchange="passwordConfirm(this.value)"  type="password" class="form-control" name="password_confirmation" required>
                                <p id="password-confirm-prompt" style="display: none; color: red;">两次输入的密码不一致</p>
                                @if ($errors->has('password_confirmation'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('password_confirmation') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-md-6 col-md-offset-4">
                                <button type="submit" class="btn btn-primary">
                                    Submit
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
