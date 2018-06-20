@extends('layouts.zoomov')

@section('content')
    <link href="/css/form.css" rel="stylesheet" />
    <link rel="stylesheet" href="/css/message.css" />
    <link rel="stylesheet" href="/css/tag.css" />
    <div class="container">
        <div ng-controller="menuCtrl">
            <div class="modal fade" id="informationErrorModal" tabindex="-1" role="dialog" aria-labelledby="informationErrorModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center" id="modal-body">
                            <div ng-bind="msg"></div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-info" type="button" data-dismiss="modal">
                                {{trans("project.BUTTONS.confirm")}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade bd-example-modal-lg" id="submitPreparationModal" tabindex="-1" role="dialog" aria-labelledby="submitPreparationModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="modal-body">
                            {!! trans('project.MESSAGES.submit_preparation') !!}
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-outline-secondary" type="button" data-dismiss="modal" >
                                {{trans("project.BUTTONS.cancel")}}
                            </button>
                            <button class="btn btn-success" type="button" ng-click="preparationSubmit()">{{trans("project.BUTTONS.confirm")}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade bd-example-modal-lg" id="deletePreparationModal" tabindex="-1" role="dialog" aria-labelledby="deletePreparationModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body" id="modal-body">
                            {!! trans('project.MESSAGES.delete_project') !!}
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-outline-secondary" type="button" data-dismiss="modal" >
                                {{trans("project.BUTTONS.cancel")}}
                            </button>
                            <button class="btn btn-success" type="button" ng-click="preparationDeleted('{{$project->id}}')">{{trans("project.BUTTONS.confirm")}}</button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="informationChangedModal" tabindex="-1" role="dialog" aria-labelledby="informationChangedModalTitle" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body text-center" id="modal-body">
                            <div>{{trans('project.ALERTS.info_changed')}}</div>
                            <div class="alert alert-warning">{{trans('personal.ALERTS.page_jump')}}</div>
                            <div>{{trans('personal.MESSAGES.page_jump')}}</div>
                        </div>
                        <div class="modal-footer">
                            <button class="btn btn-success" type="button" ng-click="confirmPageJump()" >
                                {{trans("project.BUTTONS.confirm")}}
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5 px-3">
                @include('templates.synopsis')
            </div>
            <div id="project-status" class="mt-5 pt-5 fixed-right btn-group-vertical">
                <div class='btn btn-danger' data-toggle="modal" data-target="#deletePreparationModal">
                    {{trans("project.BUTTONS.delete")}}
                </div>
                @if($step>1 || $project->count > 199 ||(!is_null($project->description) && strlen($project->description)> 199))
                    <a class='btn btn-info' href="/admin/preparation/{{$project->id}}" target="_blank">
                        {{trans("project.BUTTONS.preview")}}
                    </a>
                    <div class="btn btn-success" id="btnSubmit" ng-click="send('{{$step}}')">
                        <span class="text-uppercase">{{trans("project.BUTTONS.submit")}}</span>
                    </div>
                    <form name="sendForm" id="sendForm" action="/admin/send" method="POST">
                        <input type="hidden" name="id" value="{{$project->id}}">
                    </form>
                @endif
            </div>
            <ul class="nav nav-tabs nav-fill">
                <li role="presentation" class="nav-item">
                    <a class="nav-link {{$step==0 ? 'active':''}}" ng-click="changeStep('{{$project->id}}', '{{$step}}', 0)" href="javascript:void(0)">
                        <span>{{trans("project.CREATION.pitch")}}</span>
                    </a>
                </li>
                <li role="presentation" class="nav-item">
                    <a class="nav-link {{$step==1 ? 'active':''}}" ng-click="changeStep('{{$project->id}}', '{{$step}}', 1)" href="javascript:void(0)">
                        <span>{{trans("project.CREATION.description")}}</span>
                    </a>
                </li>
                @if($step > 1 || $project->count > 199 || (!is_null($project->description) && strlen($project->description)> 199))
                    <li role="presentation" class="nav-item">
                        <a class="nav-link {{$step==2 ? 'active':''}}" ng-click="changeStep('{{$project->id}}', '{{$step}}', 2)" href="javascript:void(0)">
                            <span>{{trans("project.CREATION.container")}}</span>
                        </a>
                    </li>
                    <li role="presentation" class="nav-item">
                        <a class="nav-link {{$step==3 ? 'active':''}}" ng-click="changeStep('{{$project->id}}', '{{$step}}', 3)" href="javascript:void(0)">
                            <span>{{trans("project.CREATION.recruitment")}}</span>
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link disabled" href="javascript:void(0)" ng-click="alert()">
                            <span>{{trans("project.CREATION.container")}}</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link disabled" href="javascript:void(0)" ng-click="alert()">
                            <span>{{trans("project.CREATION.recruitment")}}</span>
                        </a>
                    </li>
                @endif
            </ul>
        </div>
        @yield('tabcontent')
    </div>

    @endsection
@section('script')
    <script src="/js/controllers/admin/project.js"></script>
    @yield('tabscript')

@endsection
