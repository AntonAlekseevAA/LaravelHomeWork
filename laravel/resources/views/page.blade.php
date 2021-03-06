@extends('layouts.app')

<?php $userId = Auth::user(); ?>
<!-- Source: https://www.cloudways.com/blog/comment-system-laravel-vuejs/ -->
@section('content')

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.1/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">

    <!--User custom scripts -->
    <script type="text/javascript" src="{{ URL::asset('js/commentsPage.js') }}"></script>
    <script type="text/javascript" src="{{ URL::asset('js/md5.js') }}"></script>
    <!------------------------>

    <link href="css/comments.css" rel="stylesheet" type="text/css">
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div id="hfUserId" style="display: none;" value="{{isset($userId) ? Auth::user()->id : ''}}"></div>
                    <div id="hfUserName" style="display: none;" value="{{isset($userId) ? Auth::user()->name : ''}}"></div>
                </div>
            </div>

            <div class="card-header col-md-10 text-center border border-dark border-3">
                <div style="display: block" class="h4 mb-3">Комментарии</div>
                <div class="ml-10" style="height: 40px;">
                    <button type="button" class="btn btn-dark btn-order-by float-right mb-3" data-direction="asc" onclick="sortByDate()">Сортировка по дате</button>
                    <button type="button" class="btn btn-dark btn-order-by float-right mb-3 mr-2" data-direction="desc" onclick="sortByVotes()">Сортировка по рейтингу</button>
                </div>
                <div class="comment-wrapper">
                    <div id="comments" class="card-body border border-right border-2 comment-block" data-id="0">

                    </div>
                    <div class="form-group col-md-12 pt-2" style="padding:0;" data-id="0">
                        <textarea class="form-control comments-textarea mb-2" id="exampleFormControlTextarea1" rows="3" data-id="0"></textarea>
                        <button type="button" class="col-md-6 float-right btn btn-secondary btn-sm btnSendComment" onClick="createNewComment()" data-id="0">Comment</button>
                    </div>
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
