@extends('layouts.zoomov')

@section('content')
<link href="/css/message.css" rel="stylesheet" />
<script type="text/ng-template" id="confirm.html">
    <div class="modal-body" id="modal-body">
        <h3 translate="notification.message.<%confirm%>"></h3>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" type="button" ng-click="$close(false)">
            {{trans("project.BUTTONS.cancel")}}
        </button>
        <button class="btn btn-danger" type="button" ng-click="$close(true)">
            {{trans("project.BUTTONS.confirm")}}
        </button>
    </div>
</script>
<div class="container" ng-controller="notificationsCtrl" ng-init="init('{{$roles}}')">
    <div class="row">
        <div class="col-md-10 col-sm-12">
            <div class="margin-top-lg">
                <div class="flex-rows">
                    <div class="flex-left">
                        <div class="text-right">
                            <div>{{trans('notification.HEADER.prefix')}}</div>
                            <div class="margin-top-sm">{{trans('notification.HEADER.got')}}</div>
                        </div>
                        <div class="text-muted padding-left-md">
                            <div>
                                @if(sizeof($applications) == 0)
                                    <span class="text-important" translate="NONE"></span>&nbsp;
                                    <span>{{trans('notification.LABELS.applications')}}</span>,&nbsp;
                                    <span>{{trans('notification.HEADER.sent')}}</span>
                                    <span class="text-important" translate="NONE"></span>&nbsp;
                                    <span>{{trans('notification.LABELS.applications')}}</span>
                                @elseif(sizeof($applications) == 1)
                                    @if($applications[0]->outbox == 0)
                                        <strong class="text-important">{{$applications[0]->cnt}}</strong>&nbsp;
                                        <span>{{trans('notification.LABELS.applications')}}</span>,&nbsp;
                                        <span>{{trans('notification.HEADER.sent')}}</span>
                                        <span class="text-important" translate="NONE"></span>&nbsp;
                                        <span>{{trans('notification.LABELS.applications')}}</span>
                                    @else
                                        <span class="text-important" translate="NONE"></span>&nbsp;
                                        <span>{{trans('notification.LABELS.applications')}}</span>,&nbsp;
                                        <span>{{trans('notification.HEADER.sent')}}</span>
                                        <strong class="text-important">{{$applications[0]->cnt}}</strong>&nbsp;
                                        <span>{{trans('notification.LABELS.applications')}}</span>
                                    @endif
                                @else
                                    <strong class="text-important">{{$applications[0]->cnt}}</strong>&nbsp;
                                    <span>{{trans('notification.LABELS.applications')}}</span>,&nbsp;
                                    <span>{{trans('notification.HEADER.sent')}}</span>
                                    <strong class="text-important">{{$applications[1]->cnt}}</strong>&nbsp;
                                    <span>{{trans('notification.LABELS.applications')}}</span>
                                @endif
                            </div>
                            <div class="margin-top-sm">
                                @if(sizeof($reminders) == 0)
                                    <span class="text-important" translate="NONE"></span>&nbsp;
                                    <span>{{trans('notification.LABELS.reminders')}}</span>,&nbsp;
                                    <span>{{trans('notification.HEADER.set')}}</span>&nbsp;
                                    <strong id="reminders_cnt" class="text-important" translate="NONE"></strong>&nbsp;
                                    <span>{{trans('notification.LABELS.reminders')}}</span>
                                @elseif(sizeof($reminders) == 1)
                                    @if($reminders[0]->outbox == 0)
                                        <strong class="text-important">{{$reminders[0]->cnt}}</strong>&nbsp;
                                        <span>{{trans('notification.LABELS.reminders')}}</span>,&nbsp;
                                        <span>{{trans('notification.HEADER.set')}}</span>&nbsp;
                                        <strong id="reminders_cnt" class="text-important" translate="NONE"></strong>&nbsp;
                                        <span>{{trans('notification.LABELS.reminders')}}</span>
                                    @else
                                        <span class="text-important" translate="NONE"></span>&nbsp;
                                        <span>{{trans('notification.LABELS.reminders')}}</span>,&nbsp;
                                        <span>{{trans('notification.HEADER.set')}}</span>&nbsp;
                                        <strong id="reminders_cnt" class="text-important">{{$reminders[0]->cnt}}</strong>&nbsp;
                                        <span>{{trans('notification.LABELS.reminders')}}</span>
                                    @endif
                                @else
                                    <strong class="text-important">{{$reminders[0]->cnt}}</strong>&nbsp;
                                    <span>{{trans('notification.LABELS.reminders')}}</span>,&nbsp;
                                    <span>{{trans('notification.HEADER.set')}}</span>&nbsp;
                                    <strong id="reminders_cnt" class="text-important">{{$reminders[1]->cnt}}</strong>&nbsp;
                                    <span>{{trans('notification.LABELS.reminders')}}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="flex-cols">
                        <div>
                            <div ng-if="invitation.projects.length" ng-click="invite()" class="btn-squash text-center"
                                 ng-class="{'btn-text-info': selectedView != 1, 'btn-text-important': selectedView == 1}">
                                <span class="text-uppercase" translate="personal.buttons.invitation"></span>
                            </div>
                        </div>
                        <div>
                            <div ng-if="projects.length" class="btn-squash text-center" ng-click="remind()"
                                 ng-class="{'btn-text-info': selectedView != 2, 'btn-text-important': selectedView == 2}">
                                <span class="fa fa-bullhorn"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <br/>
                <div class="text-center">
                    <span translate="notification.Projects" translate-values="{cnt:owned.length, total:projects.length}"></span>
                </div>
            </div>
            <div class="margin-top-lg">
                <div ng-if="selectedView == 1" class="letter">
                    @include('templates.invitation')
                </div>
                <form name="reminderForm" ng-if="selectedView == 2" class="letter margin-bottom-md" style="position: relative">
                    <div class="row  margin-top-sm">
                        <div class="col-md-6 col-sm-12 flex-center">
                            <span class="text-uppercase">{{trans('notification.LABELS.remind')}}</span>&nbsp;
                            <div class="dropdown" id="project">
                                <div class="dropdown-toggle" type="button" id="dropdownProject" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <span class="text-important" ng-bind="reminder.project.title"></span>
                                    <span class="caret"></span>
                                </div>
                                <ul class="dropdown-menu">
                                    <li ng-repeat="p in projects" ng-show="reminder.project.id != p.id">
                                        <a class="btn" href="#" ng-click="reminder.project = p">
                                            <span  ng-bind="p.title"></span>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </div>
                        <div class="col-md-6 col-sm-12">
                            <input type="text" name="subject" class="form-text" ng-model="reminder.subject"
                                   placeholder="{{trans('notification.LABELS.reminders')}}"
                                   required ng-maxlength="40" ng-minlength="4"/>
                            <div class="error" role="alert" ng-class="{'visible':reminderForm.subject.$touched || reminderForm.$submitted}">
                                <span ng-show="reminderForm.subject.$error.required" translate="notification.ERRORS.require.Reminder"></span>
                                <span ng-show="reminderForm.subject.$error.minlength" translate="notification.ERRORS.minlength.Subject"></span>
                                <span ng-show="reminderForm.subject.$error.maxlength" translate="notification.ERRORS.maxlength.Subject"></span>
                            </div>
                        </div>
                    </div>

                    <div class="margin-top-md">
                <textarea class="form-control" ng-model="reminder.body" name="mailBody"
                          rows="10" ng-minlength="10" ng-maxlength="800" required></textarea>
                        <div class="error" role="alert" ng-class="{'visible':reminderForm.mailBody.$touched || reminderForm.$submitted}">
                            <span ng-show="reminderForm.mailBody.$error.required" translate="notification.ERRORS.require.Reminder"></span>
                            <span ng-show="reminderForm.mailBody.$error.minlength" translate="notification.ERRORS.minlength.Reminder"></span>
                            <span ng-show="reminderForm.mailBody.$error.maxlength" translate="notification.ERRORS.maxlength.Reminder"></span>
                        </div>
                        <div class="text-right">
                            <div class="btn btn-default" ng-click="remind()">
                                <span class="fa fa-undo"></span>
                            </div>
                            <div class="btn btn-primary" ng-disabled="reminderForm.$invalid" ng-click="sendReminder(reminderForm.$invalid)">
                                <span class="fa fa-send-o"></span>
                            </div>
                        </div>
                    </div>
                    <div class="loader-content" ng-if="reminder.loading"><div class="loader"></div></div>
                </form>
                <uib-tabset justified="true" ng-show="!selectedView">
                    <uib-tab select="selectTopTab('applications', 'in')">
                        <uib-tab-heading>
                            <span translate="notification.Application"></span>
                            <sup id="sup_applications">{{$applications_cnt > 0 ? $applications_cnt : ''}}</sup>
                        </uib-tab-heading>
                        <div ng-if="messages.length == 0">
                            @include('templates.empty');
                        </div>
                        <br/>
                        <div class="content">
                            <div ng-repeat="a in messages">
                                <div class="message-container row" ng-class="{'unchecked': !a.checked}">
                                    <div ng-switch="a.accepted" class="col-md-2 col-sm-4 col-xs-5">
                                        <span ng-switch-default class="btn-sm btn-warning">{{trans('notification.LABELS.wait')}}</span>
                                        <span ng-switch-when="1" class="btn-sm btn-primary">{{trans('notification.LABELS.accepted')}}</span>
                                        <span ng-switch-when="0" class="btn-sm btn-danger">{{trans('notification.LABELS.refused')}}</span>
                                    </div>

                                    <div class="col-md-9 col-sm-6 col-xs-4 padding-left-lg">
                                        <div>
                                            <img ng-src="/context/avatars/<%a.sender_id%>.small.jpg" class="img-circle img-responsive inner" />
                                            <a href="/profile/<%a.sender_id%>" target="_blank"
                                               class="title" ng-bind="a.username">
                                            </a>
                                            &nbsp;
                                            <span class="text-muted small" ng-bind="a.created_at|limitTo:16"></span>
                                            <span class="margin-left-md" >
                                                <span translate="notification.ApplicationSubject"
                                                      translate-values='{project:a.title}'>
                                                </span>
                                                <strong ng-bind="a.name"></strong>
                                            </span>
                                        </div>
                                        <div ng-if="message.id == a.id" class="padding-md">
                                            <div class="letter">
                                                <div class="message" ng-bind-html="message.letter"></div>
                                                <div class="flex-rows margin-top-sm">
                                                    <span class="btn text-danger" translate="project.BUTTONS.delete" ng-click="deleteApplication(a)"></span>
                                                    <div ng-if="a.accepted == null">
                                                        <div class="btn text-warning" translate="notification.refuse" ng-click="updateApplication(a, 0)"></div>
                                                        <div class="btn text-success" translate="notification.accept" ng-click="updateApplication(a, 1)">
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="loader-content" ng-if="a.deleting"><div class="loader"></div></div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-sm-2 col-xs-3">
                                        <div ng-click="read(a)" class="btn btn-link">
                                            <span ng-if='message.id != a.id'>{{trans('notification.LABELS.read')}}</span>
                                            <span ng-if="message.id == a.id">{{trans('notification.LABELS.envelop')}}</span>
                                        </div>
                                    </div>
                                </div>

                                <hr ng-if="!$last">
                            </div>
                        </div>
                    </uib-tab>
                    <uib-tab select="selectTopTab('reminders', 'in')">
                        <uib-tab-heading>
                            <span>{{trans('notification.LABELS.reminders')}}</span>
                            <sup id="in_reminders">{{$reminders_cnt > 0 ? $reminders_cnt : ''}}</sup>
                        </uib-tab-heading>
                        <div ng-if="messages.length == 0">
                            @include('templates.empty');
                        </div>
                        <div class="content">
                            <div ng-repeat="r in messages">
                                <div class="message-container row" ng-class="{'unchecked': !r.checked}">
                                    <div class="col-md-11 col-sm-10 col-xs-9">
                                        <div>
                                            <img ng-src="/context/avatars/<%r.sender_id%>.small.jpg" class="img-circle img-responsive inner" />
                                            <a href="/profile/<%r.sender_id%>" target="_blank"
                                               class="title" ng-bind="r.username">
                                            </a>
                                            &nbsp;
                                            <span class="text-muted small" ng-bind="r.created_at|limitTo:16"></span> &nbsp;
                                            <span translate="notification.For" translate-values="{id:r.project_id, title:r.title}"></span>
                                            <span class="text-chocolate" ng-bind="r.subject"></span>
                                        </div>
                                        <div ng-if="message.id == r.id" class="padding-md">
                                            <div class="letter">
                                                <div class="message" ng-bind-html="message.letter"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-sm-2 col-xs-3">
                                        <div ng-click="read(r)" class="btn btn-link">
                                            <span ng-if='message.id != r.id' translate="notification.Read"></span>
                                            <span ng-if="message.id == r.id" translate="notification.Envelop"></span>
                                        </div>
                                    </div>
                                    <div class="loader-content" ng-if="r.deleting"><div class="loader"></div></div>
                                </div>

                                <hr ng-if="!$last">
                            </div>
                        </div>
                    </uib-tab>
                    <uib-tab select="selectTopTab('reminders', 'out')">
                        <uib-tab-heading>
                            <span translate="notification.Outbox"></span>
                            <sup id="out_reminders">{{$unread > 0 ? $unread : ''}}</sup>
                        </uib-tab-heading>
                        <div ng-if="messages.length == 0">
                            @include('templates.empty');
                        </div>
                        <div class="content">
                            <div ng-repeat="r in messages">
                                <div class="message-container row">
                                    <div class="col-md-2 col-sm-3 col-xs-4">
                                        <span ng-if="!r.checked" class="btn-sm btn-warning" translate="notification.Unchecked"></span>
                                        <span ng-if="r.checked" class="btn-sm btn-success" translate="notification.Checked"></span>
                                    </div>
                                    <div class="col-md-9 col-sm-7 col-xs-5 padding-left-lg">
                                        <div class="flex-rows">
                                            <div>
                                                <span class="text-muted small" ng-bind="r.created_at|limitTo:16"></span>
                                                <span translate="notification.For" translate-values="{id:r.project_id, title:r.title}"></span>
                                                <span class="text-chocolate" ng-bind="r.subject"></span>
                                            </div>
                                            <div ng-if="r.checked" class="btn text-danger" translate="project.BUTTONS.delete" ng-click="removeApplication(a)"></div>
                                            <div ng-if="!r.checked" class="btn text-success" translate="project.BUTTONS.check" ng-click="checkApplication(a)"></div>
                                        </div>
                                        <div class="small">
                                            <span translate="notification.Tos"></span>&nbsp;<span ng-repeat="u in r.receivers">
                                    <a ng-class="{'text-important':!u.checked, 'text-success':u.checked}" href="/profile/<%u.user_id%>" target="_blank" ng-bind="u.username"></a>
                                    <span ng-if="!$last">,&nbsp;</span>
                                </span>
                                        </div>
                                        <div ng-if="message.id == r.id" class="padding-md">
                                            <div class="letter">
                                                <div class="message" ng-bind-html="message.letter"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-sm-2 col-xs-3">
                                        <div ng-click="read(r, 1)" class="btn btn-link">
                                            <span ng-if='message.id != r.id' translate="notification.Read"></span>
                                            <span ng-if="message.id == r.id" translate="notification.Envelop"></span>
                                        </div>
                                    </div>
                                    <div class="loader-content" ng-if="r.deleting"><div class="loader"></div></div>
                                </div>

                                <hr ng-if="!$last">
                            </div>
                        </div>
                    </uib-tab>
                    <uib-tab select="selectTopTab('applications', 'out')">
                        <uib-tab-heading>
                            <span translate="notification.Recruitment"></span>
                            <sup id="sup_unchecked">{{$unchecked > 0 ? $unchecked : ''}}</sup>
                        </uib-tab-heading>
                        <div ng-if="messages.length == 0">
                            @include('templates.empty');
                        </div>
                        <div class="content">
                            <div ng-repeat="a in messages">
                                <div class="message-container row" ng-class="{'unchecked': !a.checked}">
                                    <div ng-switch="a.accepted" class="col-md-2 col-sm-4 col-xs-5">
                                        <span ng-switch-default class="btn-sm btn-warning" translate="notification.wait"></span>
                                        <span ng-switch-when="1" class="btn-sm btn-primary" translate="notification.accepted"></span>
                                        <span ng-switch-when="0" class="btn-sm btn-danger" translate="notification.refused"></span>
                                    </div>

                                    <div class="col-md-9 col-sm-6 col-xs-4 padding-left-lg">
                                        <div class="flex-rows">
                                            <div>
                                                <span class="text-muted small" ng-bind="a.created_at|limitTo:16"></span>

                                                <span translate="notification.You"></span>
                                                <img ng-src="/context/avatars/<%a.receiver_id%>.small.jpg" class="img-circle img-responsive inner" />
                                                <a class="title" href="/profile/<%a.receiver_id%>" target="_blank" ng-bind="a.username"></a>
                                                &nbsp;
                                                <span translate="notification.Receiver" translate-values="{project:a.title}"></span>&nbsp;
                                                <strong translate="occupation.<%a.name%>"></strong>
                                            </div>
                                            <div ng-if="a.checked" class="btn text-danger" translate="project.BUTTONS.delete" ng-click="removeApplication(a)"></div>
                                            <div ng-if="!a.checked" class="btn text-success" translate="project.BUTTONS.check" ng-click="checkApplication(a)"></div>
                                        </div>
                                        <div ng-if="message.id == a.id" class="padding-md">
                                            <div class="letter">
                                                <div class="message" ng-bind-html="message.letter"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-1 col-sm-2 col-xs-3">
                                        <div ng-click="read(a)" class="btn btn-link">
                                            <span ng-if='message.id != a.id' translate="notification.Read"></span>
                                            <span ng-if="message.id == a.id" translate="notification.Envelop"></span>
                                        </div>
                                    </div>
                                    <div class="loader-content" ng-if="a.deleting"><div class="loader"></div></div>
                                </div>

                                <hr ng-if="!$last">
                            </div>
                        </div>
                    </uib-tab>
                </uib-tabset>

                <div class="text-center" ng-show="pagination.show">
                    <ul uib-pagination ng-change="pageChanged()"
                        max-size="5"
                        rotate = true
                        items-per-page = 'pagination.perPage'
                        boundary-links="true"
                        total-items="pagination.total"
                        ng-model="pagination.currentPage"
                        class="pagination-sm"
                        previous-text="&lsaquo;"
                        next-text="&rsaquo;"
                        first-text="&laquo;"
                        last-text="&raquo;"></ul>
                </div>
            </div>
        </div>
        <div class="col-md-2 col-sm-12">
            @foreach($notifications as $notification)
                <div id="notification_{{$notification->id}}" class="alert alert-warning">
                    <h3>{{$notification->title}}</h3>
                    <div>
                        <div>{{$notification->body}}</div>
                        <div class="text-right">
                            <span class="btn text-important fa fa-trash"
                                  ng-click="deleteNotification('{{$notification->id}}')"></span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

</div>
@endsection
@section('script')
    <script src="/js/directives/message.js"></script>
    <script src="/js/controllers/admin/notification.js"></script>
@endsection