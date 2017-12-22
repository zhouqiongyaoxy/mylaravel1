@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <ul class="nav nav-tabs" role="tablist" id="nav-li">
            <li role="presentation" class="active" onclick="changePage(0)"><a href="#">我的书库</a></li>
            <li role="presentation"  onclick="changePage(1)"><a href="#">设置</a></li>
        </ul>
        <div id="pageContent">
            <div class="pages" id="bookPage">
                <book-list></book-list>
            </div>
            <div class="pages" id="settingPage" style="display: none">
                <setting-page></setting-page>
            </div>

            {{--<div class="panel panel-default">
                <div class="panel-heading">Dashboard</div>

                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    You are logged in!
                </div>
            </div>--}}
        </div>
    </div>
</div>
@endsection
<script>
    function changePage(val) {
        $('#nav-li li').removeClass('active');
        $('#nav-li li').eq(val).addClass('active');
        $('.pages').css('display','none');
        if (val == 0) {
            $("#bookPage").css('display','block');
        } else if (val == 1) {
            $("#settingPage").css('display','block');
        }
    }
</script>