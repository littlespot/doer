@extends('layouts.zoomov')

@section('content')
<div class="container">
    <div class="jumbotron bg-transparent text-center">
        <h2>{{$film->title}}</h2>
        <h5 class="py-3">{{$step>0 ? trans('film.'.$film->type.'.'.$step) : trans('film.header.'.$film->type.'_upload')}}</h5>
    </div>
    <div class="modal fade bd-example-modal-lg" id="deleteFilmModal" tabindex="-1" role="dialog" aria-labelledby="deleteFilmModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <form action="/archives/{{$film->id}}"  method="POST" class="modal-content">
                <div class="modal-header">
                    {{trans('film.buttons.delete')}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <input type="hidden" value="delete" name="_method">
                <div class="modal-body px-5" id="modal-body">
                    <div class="text-info py-1">{!!trans('film.alert.entry_film_delete')!!} </div>
                    <div class="checkbox-inline checkbox-primary  py-3">
                        <input type="checkbox" value="1" name="confirmed" id="delete_film_chx" ng-model="confirmed">
                        <label></label>
                        <span>{!!trans('film.declaration.delete_film')!!} <label>{{$film->title}}</label></span>
                    </div>
                    <div class="alert alert-danger">{!!trans('film.alert.delete_film')!!}</div>
                </div>
                <div class="modal-footer d-flex px-5">
                    <button class="btn btn-danger mr-auto" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-primary" type="submit" ng-disabled="!confirmed">
                        {{trans("project.BUTTONS.confirm")}}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-3 col-md-4 col-sm-12" style="padding-right:0;">
            <ul class="list-group">
                @foreach(trans('film.'.$film->type) as $key=>$label)
                    <li class="list-group-item list-group-item-secondary {{$key == $step?'active':'ml-3 border-white'}} d-flex">
                        <a class="mr-auto" href="/{{$film->type}}s/{{$film->id}}?step={{$key}}">
                            {{$label}}
                        </a>
                        @if($key != $step)
                            <span class="fa fa-check-circle {{$key == 1 || $film->status[$key-1] ? 'text-primary' : 'text-light'}}"></span>
                        @endif
                    </li>
                @endforeach
                @if(is_numeric(strpos($film->status, '0')))
                        <li class="list-group-item list-group-item-secondary disabled">
                            {{trans('film.buttons.upload')}}
                        </li>
                @else
                        <li class="list-group-item list-group-item-dark {{$step==0 ? 'active':'ml-3 border-white'}} d-flex">
                            <a class="mr-auto " href="/{{$film->type}}s/{{$film->id}}?step=0">{{trans('film.buttons.upload')}}</a>
                            @if($step != 0)
                                <span class="fa fa-check-circle {{$film->uploaded ? 'text-primary' : 'text-light'}}"></span>
                            @endif
                        </li>

                @endif
            </ul>
        </div>
        <div class="col-lg-9 col-md-8 col-sm-12 p-5 border border-primary d-flex flex-column justify-content-between">
            @yield('filmForm')
        </div>
    </div>
</div>
    @endsection
@section('script')

@endsection