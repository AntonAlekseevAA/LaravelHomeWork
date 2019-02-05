@extends('layouts.app')

<?php $userId = Auth::user(); ?>
<!-- Source: https://www.cloudways.com/blog/comment-system-laravel-vuejs/ -->
@section('content')
    <link href="css/comment.css" rel="stylesheet" type="text/css">
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
        </div>
        <div class="row">
            <div class="col-md-12" style="background:white;">
            </div>
        </div>
    </div>
@endsection
