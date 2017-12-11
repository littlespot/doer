@extends('layouts.zoomov')

@section('content')
<style>
    button{
        background: transparent;
    }
</style>
<script type="text/ng-template" id="confirm.html">
    <div class="modal-body" id="modal-body">
        <h3 translate="project.MESSAGES.<%confirm%>"></h3>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" type="button" ng-click="$close(false)">{{trans("project.BUTTONS.cancel")}}</button>
        <button class="btn btn-danger" type="button" ng-click="$close(true)">{{trans("project.BUTTONS.confirm")}}</button>
    </div>
</script>
<div class="container">
    <div class="row">
        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6">

        </div>
        <div class="col-lg-10 col-md-9 col-sm-8 col-xs-6">
            <h1>{{$film->title}}</h1>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-2 col-md-3 col-sm-4 col-xs-6 collapse navbar-collapse">
            <ul class="nav nav-pills nav-stacked">
                <?php
                    $labels = trans('film.card');
                    $size = sizeof($labels);
                    $status = str_pad(decbin($film->completed),$size, '0');

                    $i = 0;
                    $completed = true;
                    foreach($labels as $label){
                        $completed &= $status[$i];
                        echo '<li><a href="/film/'.$film->id.'/'.($i+1).'">'.$label.'<span class="fa fa-'.($status[$i] ? 'check text-success' : 'times text-danger').'"></span></a></li>';
                        $i++;
                    }

                    if($completed){
                        echo '<li><a href="/film/'.$film->id.'">'.trans('film.buttons.upload').'</a></li>';
                    }
                    else{
                        echo '<li><a href="javascript:void(0)">'.trans('film.buttons.upload').'</a></li>';
                    }
                ?>
            </ul>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-7 col-xs-6">
            @yield('filmForm')
        </div>
    </div>
</div>
    @endsection
@section('script')

@endsection