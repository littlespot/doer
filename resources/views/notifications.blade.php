@extends('layouts.zoomov')

@section('content')
<link href="/css/message.css" rel="stylesheet" />
<div class="container" ng-controller="notificationsCtrl" ng-init="init('{{$roles}}')">
    <div class="modal fade" id="acceptModal" tabindex="-1" role="dialog" aria-labelledby="acceptModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{trans('notification.HEADER.accept_application')}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center" id="modal-body">
                    <div>{{trans('notification.MESSAGES.accept_application')}}</div>
                </div>
                <div class="modal-footer d-flex px-5">
                    <button class="btn btn-danger mr-auto" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-primary" type="button" ng-click="applicationUpdated(1)" >
                        {{trans("project.BUTTONS.confirm")}}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="refuseModal" tabindex="-1" role="dialog" aria-labelledby="refuseModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    {{trans('notification.HEADER.refuse_application')}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center" id="modal-body">
                    <div>{{trans('notification.MESSAGES.refuse_application')}}</div>
                </div>
                <div class="modal-footer d-flex px-5">
                    <button class="btn btn-danger mr-auto" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-primary" type="button" ng-click="applicationUpdated(0)" >
                        {{trans("project.BUTTONS.confirm")}}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="text-uppercase" ng-if="objToDelete.type=='n'">{{trans('notification.HEADER.delete_notification')}}</span>
                    <span class="text-uppercase" ng-if="objToDelete.type=='a'">{{trans('notification.HEADER.delete_application')}}</span>
                    <span class="text-uppercase" ng-if="objToDelete.type=='r'">{{trans('notification.HEADER.delete_reminder')}}</span>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center" id="modal-body">
                    <div>{{trans('notification.MESSAGES.delete')}}</div>
                    <div ng-if="objToDelete.type=='a'" class="alert alert-danger">{{trans('notification.ALERTS.delete_application')}}</div>
                    <div ng-if="objToDelete.type=='r'" class="alert alert-danger">{{trans('notification.ALERTS.delete_reminder')}}</div>
                </div>
                <div class="modal-footer d-flex px-5">
                    <button class="btn btn-danger mr-auto" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-primary" type="button" ng-click="notificationDeleted()" >
                        {{trans("project.BUTTONS.confirm")}}
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade bd-example-modal-lg" id="reminderModal" tabindex="-1" role="dialog" aria-labelledby="reminderModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
            <form name="reminderForm" class="modal-content">
                <div class="modal-header">
                    <span class="text-uppercase">{{trans('notification.LABELS.remind')}}</span>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-5 py-3" id="modal-body">
                    <div class="input input--isao" id="project">
                        <select class="input__field input__field--isao" name="project" id="reminder_project" ng-options="p.title for p in projects" ng-model="reminder.project" required>
                        </select>
                        <label class="input__label input__label--isao" for="reminder_project"
                               ng-class="{'isao_error':reminderForm.project.$error}"
                               data-content="{{trans('notification.PLACES.choose_project')}}"
                               data-error="{{trans('notification.ERRORS.require_reminder_project')}}">
                            <span class="input__label-content input__label-content--isao">{{trans('notification.PLACES.choose_project')}}</span>
                        </label>
                    </div>
                    <div class="input input--isao">
                        <textarea name="subject" class="input__field input__field--isao" ng-model="reminder.subject" id="reminder_subject"
                               placeholder="{{trans('notification.LABELS.reminders')}}"
                               required ng-maxlength="40" ng-minlength="4">
                        </textarea>
                        <label class="input__label input__label--isao" for="reminder_subject"
                               ng-class="{'isao_error':reminderForm.$invalid}"
                               data-content="{{trans('notification.PLACES.reminder_subject')}}"
                               data-error="{{trans('notification.ERRORS.require_reminder_subject')}}">
                            <span class="input__label-content input__label-content--isao">{{trans('notification.PLACES.reminder_subject')}}</span>
                        </label>
                    </div>
                    <div class="alert alert-danger" ng-if="reminder.error" ng-bind="reminder.error"></div>
                </div>
                <div class="modal-footer d-flex px-5">
                    <button class="btn btn-danger mr-auto" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-primary" type="button" ng-click="sendReminder(reminderForm.$invalid)" >
                        {{trans("project.BUTTONS.confirm")}}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="my-5">
        <div class="d-flex" style="line-height: 4rem">
            <div class="text-right">
                <div>{{trans('notification.HEADER.prefix')}}</div>
                <div class="margin-top-sm">{{trans('notification.HEADER.got')}}</div>
            </div>
            <div>
                <div>
                    @if(sizeof($applications) == 0)
                        <span class="text-danger">{{trans('layout.LABELS.zero')}}</span>&nbsp;
                        <span>{{trans('notification.LABELS.applications')}}</span>,&nbsp;
                        <span>{{trans('notification.HEADER.sent')}}</span>
                        <span class="text-danger">{{trans('layout.LABELS.zero')}}</span>&nbsp;
                        <span>{{trans('notification.LABELS.applications')}}</span>
                    @elseif(sizeof($applications) == 1)
                        @if($applications[0]->outbox == 0)
                            <strong class="text-danger">{{$applications[0]->cnt}}</strong>&nbsp;
                            <span>{{trans('notification.LABELS.applications')}}</span>,&nbsp;
                            <span>{{trans('notification.HEADER.sent')}}</span>
                            <span class="text-danger">{{trans('layout.LABELS.zero')}}</span>&nbsp;
                            <span>{{trans('notification.LABELS.applications')}}</span>
                        @else
                            <span class="text-danger">{{trans('layout.LABELS.zero')}}</span>&nbsp;
                            <span>{{trans('notification.LABELS.applications')}}</span>,&nbsp;
                            <span>{{trans('notification.HEADER.sent')}}</span>
                            <strong class="text-danger">{{$applications[0]->cnt}}</strong>&nbsp;
                            <span>{{trans('notification.LABELS.applications')}}</span>
                        @endif
                    @else
                        <strong class="text-danger">{{$applications[0]->cnt}}</strong>&nbsp;
                        <span>{{trans('notification.LABELS.applications')}}</span>,&nbsp;
                        <span>{{trans('notification.HEADER.sent')}}</span>
                        <strong class="text-danger">{{$applications[1]->cnt}}</strong>&nbsp;
                        <span>{{trans('notification.LABELS.applications')}}</span>
                    @endif
                </div>
                <div>
                    @if(sizeof($reminders) == 0)
                        <span class="text-danger">{{trans('layout.LABELS.zero')}}</span>&nbsp;
                        <span>{{trans('notification.LABELS.reminders')}}</span>,&nbsp;
                        <span>{{trans('notification.HEADER.set')}}</span>&nbsp;
                        <strong id="reminders_cnt" class="text-danger">{{trans('layout.LABELS.zero')}}</strong>&nbsp;
                        <span>{{trans('notification.LABELS.reminders')}}</span>
                    @elseif(sizeof($reminders) == 1)
                        @if($reminders[0]->outbox == 0)
                            <strong class="text-danger">{{$reminders[0]->cnt}}</strong>&nbsp;
                            <span>{{trans('notification.LABELS.reminders')}}</span>,&nbsp;
                            <span>{{trans('notification.HEADER.set')}}</span>&nbsp;
                            <strong id="reminders_cnt" class="text-danger">{{trans('layout.LABELS.zero')}}</strong>&nbsp;
                            <span>{{trans('notification.LABELS.reminders')}}</span>
                        @else
                            <span class="text-danger">{{trans('layout.LABELS.zero')}}</span>&nbsp;
                            <span>{{trans('notification.LABELS.reminders')}}</span>,&nbsp;
                            <span>{{trans('notification.HEADER.set')}}</span>&nbsp;
                            <strong id="reminders_cnt" class="text-danger">{{$reminders[0]->cnt}}</strong>&nbsp;
                            <span>{{trans('notification.LABELS.reminders')}}</span>
                        @endif
                    @else
                        <strong class="text-danger">{{$reminders[0]->cnt}}</strong>&nbsp;
                        <span>{{trans('notification.LABELS.reminders')}}</span>,&nbsp;
                        <span>{{trans('notification.HEADER.set')}}</span>&nbsp;
                        <strong id="reminders_cnt" class="text-danger">{{$reminders[1]->cnt}}</strong>&nbsp;
                        <span>{{trans('notification.LABELS.reminders')}}</span>
                    @endif
                </div>
            </div>
        </div>
        <br/>
        <div class="text-center">
            <span translate="notification.Projects" translate-values="{cnt:owned.length, total:projects.length}"></span>
        </div>
    </div>
    <div class="d-flex nav-film">
        <ul class="col-8 nav nav-tabs nav-fill mr-auto" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="applications-tab" data-toggle="tab" href="#applications" role="tab" aria-controls="applications" aria-selected="true" ng-click="selectTopTab('applications', 'in')">
                    {{trans('notification.LABELS.application_received')}}
                    <sup id="sup_applications"  class="text-danger">{{$applications_cnt > 0 ? $applications_cnt : ''}}</sup></a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="reminders-tab" data-toggle="tab" href="#reminders" role="tab" aria-controls="reminders" aria-selected="false" ng-click="selectTopTab('reminders', 'in')">
                    {{trans('notification.LABELS.reminders')}}
                    <sup id="in_reminders"  class="text-danger">{{$reminderUnchecked > 0 ? $reminderUnchecked : ''}}</sup>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="applictionsSent-tab" data-toggle="tab" href="#applictionsSent" role="tab" aria-controls="applictionsSent" aria-selected="false" ng-click="selectTopTab('applications', 'out')">
                    {{trans('notification.LABELS.application_sent')}}
                    <sup id="sup_unchecked" class="text-danger">{{$appNoAnswer > 0 ? $appNoAnswer : ''}}</sup>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="remindersSent-tab" data-toggle="tab" href="#remindersSent" role="tab" aria-controls="remindersSent" aria-selected="false" ng-click="selectTopTab('reminders', 'out')">
                    {{trans('notification.LABELS.reminder_sent')}}
                    <sup id="out_reminders"  class="text-danger">{{$reminderUnread > 0 ? $reminderUnread : ''}}</sup>
                </a>
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <button  class="btn btn-primary my-2 my-sm-0" data-toggle="modal" data-target="#reminderModal">
                {{trans('notification.BUTTONS.remind')}}
            </button>
        </form>
    </div>
    <div class="row">
        <div class="{{sizeof($notifications) ? 'col-md-8':'col-md-12'}} col-sm-12 tab-content" id="myTabContent">
            <div class="tab-pane fade show active py-3" id="applications" role="tabpanel" aria-labelledby="applications-tab">
                <div ng-if="!messages">
                    @include('templates.empty');
                </div>
                <br/>
                <div class="content">
                    <div ng-repeat="a in messages" ng-class="{'unchecked': !a.checked}">
                        <div class="row" >
                            <div ng-switch="a.accepted" class="col-md-2 col-sm-4 col-xs-5">
                                <span ng-switch-default class="btn-sm btn-warning">{{trans('notification.LABELS.wait')}}</span>
                                <span ng-switch-when="1" class="btn-sm btn-primary">{{trans('notification.LABELS.accepted')}}</span>
                                <span ng-switch-when="0" class="btn-sm btn-danger">{{trans('notification.LABELS.refused')}}</span>
                            </div>

                            <div class="col-md-9 col-sm-6 col-xs-4 media">
                                <img ng-src="/storage/avatars/<%a.sender_id%>.small.jpg" class="rounded-circle img-fluid" />
                                <div class="media-body px-3">
                                    <a href="/profile/<%a.sender_id%>" target="_blank"
                                       class="title" ng-bind="a.username">
                                    </a>
                                    <span translate="notification.ApplicationSubject"
                                          translate-values='{project:a.title, role:a.name}'>
                                            </span>
                                    <strong ng-bind="a.name"></strong>
                                </div>

                            </div>
                            <div class="col-md-1 col-sm-2 col-xs-3 text-right">
                                <div ng-click="read(a)" class="btn">
                                    <span ng-if='message.id != a.id'>{{trans('notification.LABELS.read')}}</span>
                                    <span ng-if="message.id == a.id">{{trans('notification.LABELS.envelop')}}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row" >
                            <div class="col-md-2 col-sm-4 col-xs-5">
                                <span class="text-muted small" ng-bind="a.created_at|limitTo:16"></span>
                            </div>
                            <div class="col-md-9 col-sm-6 col-xs-4">
                                <div ng-if="message.id == a.id" class="px-4 py-3">
                                    <div class="bg-white px-4 py-3">
                                        <div ng-bind="message.letter"></div>
                                        <hr ng-if="a.accepted == null"  />
                                        <div ng-if="a.accepted == null" class="d-flex justify-content-between">
                                            <div class="btn btn-danger" translate="notification.refuse" ng-click="refuseApplication(a)"></div>
                                            <div class="btn btn-success" translate="notification.accept" ng-click="acceptApplication(a)"></div>
                                        </div>
                                    </div>
                                    <div class="loader-content" ng-if="a.deleting"><div class="loader"></div></div>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-2 col-xs-3 text-right">
                                <span class="btn text-danger" ng-click="deleteNotification(a, 'a')">{{trans('layout.BUTTONS.delete')}}</span>
                            </div>
                        </div>
                        <hr ng-if="!$last">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade py-3" id="reminders" role="tabpanel" aria-labelledby="reminders-tab">
                <div ng-if="!messages">
                    @include('templates.empty');
                </div>
                <div class="content">
                    <div ng-repeat="r in messages" ng-class="{'unchecked': !r.checked}">
                        <div class="row" >
                            <div ng-switch="r.checked" class="col-md-2 col-sm-12">
                                <span ng-switch-default class="btn-sm btn-warning" translate="notification.wait"></span>
                                <span ng-switch-when="1" class="btn-sm btn-primary" translate="notification.checked"></span>
                                <span ng-switch-when="2" class="btn-sm btn-danger" translate="notification.removed"></span>
                            </div>
                            <div class="col-md-9 col-sm-12 media">
                                <img ng-src="/storage/avatars/<%r.sender_id%>.small.jpg" class="rounded-circle img-fluid inner" />
                                <div class="media-body">
                                    <a href="/profile/<%r.sender_id%>" target="_blank" class="mr-5" ng-bind="r.username">
                                    </a>
                                    <span class="fa fa-quote-left"></span>
                                    <span ng-bind="r.subject"></span>
                                    <span class="fa fa-quote-right"></span>
                                </div>

                            </div>
                            <div class="col-md-1 col-sm-12 text-right">
                                <div ng-click="read(r, 1)" class="btn text-info" ng-if="!r.checked" >{{trans('notification.LABELS.check')}}</div>
                                <div ng-click="deleteNotification(r, 'x')" class="btn text-danger" ng-if="r.checked">{{trans('layout.BUTTONS.delete')}}</div>
                            </div>
                            <div class="loader-content" ng-if="r.deleting"><div class="loader"></div></div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 col-sm-12">
                                <span class="text-muted small" ng-bind="r.created_at|limitTo:16"></span> &nbsp;
                            </div>
                            <div class="col-md-9 col-sm-12 pl-5">
                               <a class="text-muted mx-1" href="/project/<%r.project_id%>" target="_blank" ng-bind="r.title"></a>
                            </div>

                        </div>
                        <hr ng-if="!$last">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade py-3" id="applictionsSent" role="tabpanel" aria-labelledby="applictionsSent-tab">
                <div ng-if="!messages">
                    @include('templates.empty');
                </div>
                <div class="content">
                    <div ng-repeat="a in messages"  ng-class="{'unchecked': !a.checked}">
                        <div class="row">
                            <div ng-switch="a.accepted" class="col-md-2 col-sm-4 col-xs-5">
                                <span ng-switch-default class="btn-sm btn-warning" translate="notification.wait"></span>
                                <span ng-switch-when="1" class="btn-sm btn-primary" translate="notification.accepted"></span>
                                <span ng-switch-when="0" class="btn-sm btn-danger" translate="notification.refused"></span>
                            </div>
                            <div class="col-md-9 col-sm-6 col-xs-4">
                                <span translate="notification.ApplicationSent"></span>
                                <img ng-src="/storage/avatars/<%a.receiver_id%>.small.jpg" class="rounded-circle img-fluid inner" />
                                <a class="title" href="/profile/<%a.receiver_id%>" target="_blank" ng-bind="a.username"></a>
                                &nbsp;
                                <span translate="notification.Receiver" translate-values="{project:a.title}"></span>&nbsp;
                                <strong ng-bind="a.name"></strong>
                            </div>
                            <div class="col-md-1 col-sm-2 col-xs-3 text-right">
                                <div ng-click="read(a)" class="btn text-info">
                                    <span ng-if='message.id != a.id' translate="notification.Read"></span>
                                    <span ng-if="message.id == a.id" translate="notification.Envelop"></span>
                                </div>
                            </div>
                            <div class="loader-content" ng-if="a.deleting"><div class="loader"></div></div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 col-sm-4 col-xs-5">
                                <span class="text-muted small" ng-bind="a.created_at|limitTo:16"></span>
                            </div>
                            <div class="col-md-9 col-sm-6">
                                <div ng-if="message.id == a.id" class="bg-white p-3">
                                    <div class="message" ng-bind="message.letter"></div>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-2 col-xs-3 text-right">
                                <div class="btn text-danger" ng-click="deleteNotification(a, 'a')">{{trans('layout.BUTTONS.delete')}}</div>
                            </div>
                        </div>
                        <hr ng-if="!$last">
                    </div>
                </div>
            </div>
            <div class="tab-pane fade py-3" id="remindersSent" role="tabpanel" aria-labelledby="remindersSent-tab">
                <div ng-if="!messages">
                    @include('templates.empty');
                </div>
                <div class="content">
                    <div ng-repeat="r in messages">
                        <div class="row">
                            <div class="col-md-2 col-sm-3 col-xs-4">
                                <span ng-if="!r.checked" class="btn-sm btn-warning" translate="notification.Unchecked"></span>
                                <span ng-if="r.checked" class="btn-sm btn-success" translate="notification.Checked"></span>
                            </div>
                            <div class="col-md-9 col-sm-7 col-xs-5">
                                <span translate="notification.For" translate-values="{id:r.project_id, title:r.title}"></span>
                                <span class="text-chocolate" ng-bind="r.subject"></span>
                            </div>
                            <div class="col-md-1 col-sm-2 col-xs-3 text-right">
                                <div ng-click="r.readed = !r.readed" class="btn text-info">
                                    <span ng-if='!r.readed' translate="notification.Read"></span>
                                    <span ng-if="r.readed" translate="notification.Envelop"></span>
                                </div>
                            </div>
                            <div class="loader-content" ng-if="r.deleting"><div class="loader"></div></div>
                        </div>
                        <div class="row">
                            <div class="col-md-2 col-sm-3 col-xs-4">
                                <span class="text-muted small" ng-bind="r.created_at|limitTo:16"></span>
                            </div>
                            <div class="col-md-9 col-sm-7 col-xs-5">
                                <ol class="breadcrumb bg-white" ng-show="r.readed">
                                    <li class="breadcrumb-item" ng-repeat="u in r.receivers">
                                        <a ng-class="{'text-danger':!u.checked, 'text-success':u.checked}" href="/profile/<%u.user_id%>" target="_blank" ng-bind="u.username"></a>
                                    </li>
                                </ol>
                            </div>
                            <div class="col-md-1 col-sm-2 col-xs-3 text-right">
                                <div class="btn text-danger" ng-click="deleteNotification(r, 'r')">{{trans('layout.BUTTONS.delete')}}</div>
                            </div>
                        </div>
                        <hr ng-if="!$last">
                    </div>
                </div>
            </div>
            @include('templates.pagination')
        </div>
        @if(sizeof($notifications))
        <div class="col-md-4 col-sm-12 px-3 py-5">
            @foreach($notifications as $notification)
                <div id="notification_{{$notification->id}}" class="alert alert-warning">
                    <h6>{{$notification->title}}</h6>
                    <div class="small">
                        <div>{{$notification->body}}</div>
                        <div class="text-right">
                            <span class="btn text-important fa fa-trash"
                                  ng-click="deleteNotification('{{$notification->id}}', 'n')"></span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        @endif
    </div>
</div>
@endsection
@section('script')
    <script src="/js/controllers/admin/notification.js"></script>
@endsection