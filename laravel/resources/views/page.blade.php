@extends('layouts.app')

<?php $userId = Auth::user(); ?>
<!-- Source: https://www.cloudways.com/blog/comment-system-laravel-vuejs/ -->
@section('content')

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>

    <!--User custom scripts -->
    <script type="text/javascript" src="{{ URL::asset('js/commentsPage.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/md5.js') }}"></script>
    <!------------------------>

    <!--Not need now -->
    <!--<link href="css/comment.css" rel="stylesheet" type="text/css">-->
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">UserId: {{ isset($userId) ? Auth::user()->id : '' }} </div>
                    <div id="hfUserId" style="display: none;" value="{{isset($userId) ? Auth::user()->id : ''}}"></div>
                    <div class="panel-body">
                        Comments list here
                    </div>
                </div>
            </div>

            <div class="card-header col-md-10 text-center border border-dark">
                Комментарии
                <div id="comments" class="card-body">

                </div>
            </div>


        </div>

        <div class="jumbotron vertical-center">
        </div>

        <div class="row">
            <div class="col-md-12" style="background:white;">
            </div>
        </div>
    </div>
@endsection
