@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">Reset Password</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form class="form-horizontal" method="POST" action="{{ route('password.answercheck') }}">
                        {{ csrf_field() }}
                        <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                            <label for="name" class="col-md-4 control-label">Name</label>

                            <div class="col-md-6">
                                <input id="name" type="text" class="form-control" onchange="getQuestions(this.value);" name="name" value="{{ old('name') }}" required>

                                @if ($errors->has('name'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('name') }}</strong>
                                    </span>
                                @endif
                                <p id="name-check-prompt" style="display: none; color: red;">该用户不存在,请先注册</p>
                            </div>
                        </div>
                        <div class="form-group" id="questions" style="display: none;">
                            <label for="password-question" class="col-md-4 control-label">Answer Password Question</label>
                            <div class="col-md-6">
                                <label for="first-question" id ="first-question" class="control-label"></label>
                                <input type="hidden" id="first-question-num" name="first-question-num" value="">
                                <input id="first-question-answer" class="form-control" type="text" name="first-question-answer" required>
                                <label for="second-question" id ="second-question" class="control-label"></label>
                                <input type="hidden" id="second-question-num" name="second-question-num" value="">
                                <input id="second-question-answer" class="form-control" type="text" name="second-question-answer" required>
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
    function getQuestions(val) {
        if (val.length > 0) {
            $.ajax({
                type: 'GET',
                url: '/getRegisterQuestions',
                data: {name: val},
                dataType: 'json',
                success: function ($data) {
                    if ($data.code != 1){
                        $("#name-check-prompt").text($data.msg);
                        $("#name-check-prompt").css('display','block');
                        $("#questions").css('display', 'none');
                    } else {
                        $("#name-check-prompt").css('display','none');
                        $("#first-question").text($data.data[0]['ques']);
                        $("#first-question-num").val($data.data[0]['num']);
                        $("#second-question").text($data.data[1]['ques']);
                        $("#second-question-num").val($data.data[1]['num']);
                        $("#questions").css('display', 'block');
                    }
                }
            })
        }
    }
</script>