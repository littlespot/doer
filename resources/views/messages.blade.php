@extends('layouts.zoomov')

@section('content')
<link href="/css/message.css" rel="stylesheet" />
<div class="container" ng-controller="messagesCtrl" ng-init="init('{{$roles}}', '{{$invitations}}', '{{$messages}}')">
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="text-uppercase" ng-if="objToDelete.type=='n'">{{trans('notification.HEADER.delete_notification')}}</span>
                    <span class="text-uppercase" ng-if="objToDelete.type=='a'">{{trans('notification.HEADER.delete_application')}}</span>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center" id="modal-body">
                    <div>{{trans('notification.MESSAGES.delete')}}</div>
                    <div ng-if="objToDelete.type=='a'" class="alert alert-danger">{{trans('notification.ALERTS.delete_application')}}</div>
                </div>
                <div class="modal-footer d-flex px-5">
                    <button class="btn btn-danger mr-auto" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-primary" type="button" ng-click="messageDeleted()" >
                        {{trans("project.BUTTONS.confirm")}}
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade bd-example-modal-lg" id="messageModal" tabindex="-1" role="dialog" aria-labelledby="messageModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <form name="messageForm" class="modal-content">
                <div class="modal-header">
                    {{trans('notification.LABELS.messages')}}
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body px-5" id="modal-body">
                    <div class="row py-5">
                        <div class="col-md-1 col-sm-2 col-xs-4 text-right">
                            <span class="text-uppercase small" translate="notification.Tos"></span>
                        </div>
                        <div class="col-md-11 col-sm-10 col-xs-8">
                            <angucomplete-alt id="author" input-name="member"
                                              placeholder="{{trans('notification.LABELS.receivers')}}"
                                              pause="100"
                                              selected-object="selectedUser"
                                              local-data="userIndex"
                                              search-fields="username"
                                              title-field="username"
                                              description-field="location"
                                              image-uri="/storage/avatars"
                                              image-field="id"
                                              minlength="1"
                                              input-class="form-control"
                                              match-class="highlight"
                                              text-no-results="{{trans('layout.MENU.none')}}"
                                              text-searching="{{trans('layout.MENU.searching')}}"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-1 col-sm-2 col-xs-4 text-right">
                        </div>
                        <div class="col-md-11 col-sm-10 col-xs-8">
                            <input type="text" ng-model="mail.subject" name="subject" class="form-control"
                                   placeholder="{{trans('notification.LABELS.message_subject')}}"
                                   ng-maxlength="40" ng-minlength="4" required />
                            <div class="error" role="alert" ng-class="{'visible':messageForm.subject.$touched || messageForm.$submitted}">
                                <span ng-show="messageForm.subject.$error.required" translate="notification.ERRORS.minlength.Subject"></span>
                                <span ng-show="messageForm.subject.$error.minlength" translate="notification.ERRORS.minlength.Subject"></span>
                                <span ng-show="messageForm.subject.$error.maxlength" translate="notification.ERRORS.maxlength.Subject"></span>
                            </div>
                            <div class="mt-5 input input--isao">
                                <textarea class="input__field input__field--isao" ng-model="mail.body" name="mailBody"
                                          rows="10" ng-minlength="10" ng-maxlength="800" required></textarea>
                                <label class="input__label input__label--isao" for="reminder_project"
                                       ng-class="{'isao_error':messageForm.mailBody.$error}"
                                       data-content="{{trans('notification.PLACES.message_body')}}"
                                       data-error="{{trans('notification.ERRORS.require_message_body')}}">
                                    <span class="input__label-content input__label-content--isao">{{trans('notification.PLACES.message_body')}}</span>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer d-flex px-5">
                    <button class="btn btn-danger mr-auto" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-primary" type="button" ng-disabled="messageForm.$invalid" ng-click="sendMail(selectedUser, messageForm.$invalid)" >
                        {{trans("project.BUTTONS.confirm")}}
                    </button>
                </div>
            </form>
        </div>
    </div>
    <div class="modal fade" id="updateInvitationModal" tabindex="-1" role="dialog" aria-labelledby="updateInvitationModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <span class="text-uppercase" ng-if="invitationToUpdate.type == 0">{{trans('notification.HEADER.refuse_invitation')}}</span>
                    <span class="text-uppercase" ng-if="invitationToUpdate.type== 1">{{trans('notification.HEADER.accept_invitation')}}</span>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body text-center" id="modal-body">
                    <div class="text-uppercase" ng-if="invitationToUpdate.type == 0">{{trans('notification.MESSAGES.refuse_invitation')}}</div>
                    <div class="text-uppercase" ng-if="invitationToUpdate.type== 1">{{trans('notification.MESSAGES.accept_invitation')}}</div>
                </div>
                <div class="modal-footer d-flex px-5">
                    <button class="btn btn-danger mr-auto" type="button" data-dismiss="modal" >
                        {{trans("project.BUTTONS.cancel")}}
                    </button>
                    <button class="btn btn-primary" type="button" ng-click="invitationUpdated()" >
                        {{trans("project.BUTTONS.confirm")}}
                    </button>
                </div>
            </div>
        </div>
    </div>
    @include('templates.invitation')

    <div class="my-5">
        <div class="d-flex" style="line-height: 4rem">
            <div class="text-right">
                <div>{{trans("notification.HEADER.prefix")}}</div>
                <div>{{trans("notification.HEADER.got")}}</div>
            </div>
            <div class="mr-auto">
                <div>
                    <span id="in_invitations_cnt" class="text-danger">
                        <span  ng-if="!in_invitations_cnt" class="text-danger">{{trans('layout.LABELS.zero')}}</span>&nbsp;
                        <strong ng-if="in_invitations_cnt" ng-bind="in_invitations_cnt"></strong>
                    </span>&nbsp;
                    <span>{{trans("notification.LABELS.invitations")}}</span>,&nbsp;
                    <span>{{trans("notification.HEADER.sent")}}</span>
                    <span id="out_invitations_cnt" class="text-danger">
                        <span  ng-if="!out_invitations_cnt" class="text-danger">{{trans('layout.LABELS.zero')}}</span>&nbsp;
                            <strong ng-if="out_invitations_cnt" ng-bind="out_invitations_cnt"></strong>
                        </span>&nbsp;
                    <span>{{trans("notification.LABELS.invitations")}}</span>
                </div>
                <div class="margin-top-sm">
                        <span id="in_messages_cnt" class="text-danger">
                            <span  ng-if="!in_messages_cnt" class="text-danger">{{trans('layout.LABELS.zero')}}</span>&nbsp;
                            <strong ng-if="in_messages_cnt" ng-bind="in_messages_cnt"></strong>
                        </span>&nbsp;
                    <span>{{trans("notification.LABELS.messages")}}</span>,&nbsp;
                    <span>{{trans("notification.HEADER.sent")}}</span>
                    <span id="out_messages_cnt" class="text-danger">
                        <span  ng-if="!out_messages_cnt" class="text-danger">{{trans('layout.LABELS.zero')}}</span>&nbsp;
                            <strong ng-if="out_messages_cnt" ng-bind="out_messages_cnt"></strong>
                        </span>&nbsp;
                    <span>{{trans("notification.LABELS.messages")}}</span>
                </div>
            </div>
        </div>
        <br/>
        <div class="text-center">
            <span translate="notification.Owns" translate-values="{cnt:projects.length}"></span>
        </div>
    </div>
    <div class="d-flex nav-film">
        <ul class="col-8 nav nav-tabs nav-fill mr-auto" id="myTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="invitations-tab" data-toggle="tab" href="#invitations" role="tab" aria-controls="invitations" aria-selected="true" ng-click="selectTopTab('invitations', 'in')">
                    <span>{{trans("notification.LABELS.received_invitation")}}</span>
                    <sup id="in_invitations" class="text-danger">{{$invitations_cnt > 0 ? $invitations_cnt : ''}}</sup>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="messages-tab" data-toggle="tab" href="#messages" role="tab" aria-controls="reminders" aria-selected="false" ng-click="selectTopTab('messages', 'in')">
                    <span>{{trans("notification.LABELS.received_message")}}</span>
                    <sup id="in_messages"  class="text-danger">{{$messages_cnt > 0 ? $messages_cnt : ''}}</sup>
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="invitationsSent-tab" data-toggle="tab" href="#invitationsSent" role="tab" aria-controls="invitationsSent" aria-selected="false" ng-click="selectTopTab('invitations', 'out')">
                    {{trans("notification.LABELS.sent_invitation")}}
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="messagesSent-tab" data-toggle="tab" href="#messagesSent" role="tab" aria-controls="messagesSent" aria-selected="false" ng-click="selectTopTab('messages', 'out')">
                    {{trans("notification.LABELS.sent_message")}}
                </a>
            </li>
        </ul>
        <form class="form-inline my-2 my-lg-0">
            <button ng-if="projects.length>0" class="btn btn-outline-primary mx-1 my-2 my-sm-0" data-toggle="modal" ng-click="invite()">
                {{trans("notification.BUTTONS.invite")}}
            </button>
            <button  class="btn btn-primary my-2 my-sm-0" data-toggle="modal" ng-click="write()">
                {{trans("notification.BUTTONS.message")}}
            </button>
        </form>
    </div>
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active py-3" id="invitations" role="tabpanel" aria-labelledby="invitations-tab">
            <div ng-if="!messages.length">
                @include('templates.empty');
            </div>
            <br/>
            <div class="content">
                <div ng-repeat="a in messages" ng-class="{'unchecked': !a.checked}">
                    <div class="row" >
                        <div ng-switch="a.accepted" class="col-md-2 col-sm-3 col-xs-4" id="<%a.id%>_acception">
                            <span ng-switch-default class="btn-sm btn-warning">
                                {{trans("notification.LABELS.wait")}}
                            </span>
                            <span ng-switch-when="1" class="btn-sm btn-primary">
                                {{trans("notification.LABELS.accepted")}}
                            </span>
                            <span ng-switch-when="0" class="btn-sm btn-danger">
                                {{trans("notification.LABELS.refused")}}
                            </span>
                        </div>
                        <div class="col-md-9 col-sm-7 col-xs-12 media">
                            <img ng-src="/storage/avatars/<%a.user_id%>.small.jpg" class="rounded-circle img-fluid" />
                            <div class="media-body ml-1">
                                <a href="/profile/<%a.user_id%>" target="_blank"
                                   class="mr-1" ng-bind="a.username">
                                </a>
                                <span class="margin-left-md" >
                                    <span ng-if="!a.quit" translate="notification.InvitationSubject" translate-values='{project:a.title}'></span>
                                    <span ng-if="a.quit">
                                        <span translate="notification.QuitSubject" translate-values='{project:a.title}'></span>
                                        <span ng-if="a.name" translate="notification.Member"></span>
                                    </span>
                                    <strong class="text-info" ng-bind="a.name"></strong>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-2 col-xs-12 text-right">
                            <div ng-click="read(a)" class="btn text-info">
                                <span ng-if='message.id != a.id'>{{trans('notification.LABELS.read')}}</span>
                                <span ng-if="message.id == a.id">{{trans('notification.LABELS.envelop')}}</span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-sm-3 col-xs-4">
                            <span class="text-muted small" ng-bind="a.created_at|limitTo:16"></span>
                        </div>
                        <div class="col-md-9 col-sm-7 col-xs-12">
                            <div ng-if="message.id == a.id" class="bg-white px-4 py-3">
                                <div class="message" ng-bind="message.letter"></div>
                                <hr ng-if="a.accepted == null" />
                                <div ng-if="a.accepted == null" class="d-flex justify-content-between">
                                    <div class="btn btn-danger" translate="notification.refuse" ng-click="updateInvitation(a, 0)"></div>
                                    <div class="btn btn-success" translate="notification.accept" ng-click="updateInvitation(a, 1)"></div>
                                </div>
                                <div class="loader-content" ng-if="a.deleting"><div class="loader"></div></div>
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-2 col-xs-12 text-right">
                            <span class="btn text-danger" ng-click="deleteInvitation(a)">{{trans('layout.BUTTONS.delete')}}</span>
                        </div>
                    </div>
                    <hr ng-if="!$last">
                </div>
            </div>
        </div>
        <div class="tab-pane fade py-3" id="messages" role="tabpanel" aria-labelledby="messages-tab">
            <div ng-if="!messages.length">
                @include('templates.empty');
            </div>
            <br/>
            <div class="content">
                <div ng-repeat="m in messages" ng-class="{'unchecked': !m.checked}">
                    <div class="row" >
                        <div class="col-md-2 col-sm-3 col-xs-12">
                            <span ng-if="!m.replied" class="btn-sm btn-warning">
                                 {{trans("notification.LABELS.unreplied")}}
                            </span>
                            <span ng-if="m.replied" class="btn-sm btn-primary">
                                 {{trans("notification.LABELS.replied")}}
                            </span>
                        </div>
                        <div class="col-md-9 col-sm-7 col-xs-6 media">
                            <img ng-src="/storage/avatars/<%m.sender_id%>.small.jpg" class="rounded-circle img-fluid" />
                            <div class="media-body pl-3">
                                <a href="/profile/<%m.sender_id%>" target="_blank" ng-bind="m.username">
                                </a>
                                <span class="ml-3">
                                    <span ng-if="m.parent_id">{{trans('notification.LABELS.reply')}}</span>
                                    <label class="text-primary" ng-bind="m.subject"></label>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-2 col-xs-3 text-right">
                            <div ng-click="read(m)" class="btn text-info">
                                <span ng-if='message.id != m.id' translate="notification.Read"></span>
                                <span ng-if="message.id == m.id" translate="notification.Envelop"></span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-sm-3 col-xs-12">
                            <span class="text-muted small" ng-bind="m.created_at|limitTo:16"></span>
                        </div>
                        <div class="col-md-9 col-sm-7 col-xs-12">
                            <div ng-if="message.id == m.id">
                                <blockquote class="mb-3" ng-bind="message.letter"></blockquote>
                                <div class="ml-3" ng-if="message.replies.length">
                                    <div class="retro d-flex mr-5" ng-show='!message.shown' ng-click="message.shown=true;">
                                        <div translate="notification.Replies" translate-values="{cnt:message.replies.length}"></div>
                                        <div class="btn btn-sm btn-link">
                                            {{trans('notification.LABELS.read')}}
                                        </div>
                                    </div>
                                    <div class="p-1 small" ng-show="message.shown">
                                        <div ng-repeat="e in message.replies">
                                            <div ng-if="!e.sender">
                                                <div class="media">
                                                    <img ng-src="/storage/avatars/<%e.user_id%>.small.jpg" class="rounded-circle img-fluid" />
                                                    <div class="media-body pl-2">
                                                        <a href="/profile/<%e.user_id%>" target="_blank"
                                                           class="title" ng-bind="e.username">
                                                        </a>
                                                        &nbsp;
                                                        <span class="text-muted small" ng-bind="e.created_at|limitTo:16"></span>
                                                    </div>
                                                </div>
                                                <div ng-bind="e.body">
                                                </div>
                                                <hr />
                                            </div>
                                            <div ng-if="e.sender" class="blockquote-reverse">
                                                <div >
                                                    {{trans('layout.LABELS.you')}}
                                                    &nbsp;
                                                    <span class="text-muted small" ng-bind="e.created_at|limitTo:16"></span>
                                                </div>
                                                <div ng-bind="e.body">
                                                </div>
                                                <hr />
                                            </div>
                                        </div>
                                        <div class="text-right">
                                            <span class="btn btn-sm btn-link" ng-click="message.shown=false;">{{trans('notification.LABELS.envelop')}}</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-light px-4 py-3">
                                    <form name="responseForm">
                                            <textarea class="form-control" name="response" ng-model="response" rows="5"
                                                      ng-minlength="15" ng-maxlength="2000" required></textarea>
                                        <div class="error" role="alert" ng-class="{'visible':responseForm.response.$touched || responseForm.$submitted}">
                                            <span ng-show="responseForm.response.$error.required" translate="notification.ERRORS.minlength.Message"></span>
                                            <span ng-show="responseForm.response.$error.minlength" translate="notification.ERRORS.minlength.Message"></span>
                                            <span ng-show="responseForm.response.$error.maxlength" translate="notification.ERRORS.maxlength.Message"></span>
                                        </div>
                                        <div class="text-right">
                                            <div class="btn btn-default" ng-click="cancel()">
                                                <span class="fa fa-undo"></span>
                                            </div>
                                            <div class="btn btn-primary" ng-disabled="responseForm.$invalid" ng-click="sendResponse(m, response, responseForm.$invalid)">
                                                <span class="fa fa-send-o"></span>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-2 col-xs-12 text-right">
                            <div class="btn text-danger" ng-click="deleteMessage(m)">{{trans('layout.BUTTONS.delete')}}</div>
                        </div>
                    </div>
                    <div ng-if="m.parent_id && message.id == m.id" class="text-right">
                        <span class="btn btn-sm btn-danger" ng-click="deleteMessage(m, 1)">{{trans('layout.BUTTONS.delete_all')}}</span>
                    </div>
                    <div class="loader-content" ng-if="m.deleting"><div class="loader"></div></div>
                    <hr ng-if="!$last">
                </div>
            </div>
        </div>
        <div class="tab-pane fade py-3" id="invitationsSent" role="tabpanel" aria-labelledby="invitationsSent-tab">
            <div ng-if="!messages.length">
                @include('templates.empty');
            </div>
            <div class="content">
                <div ng-repeat="a in messages">
                    <div class="row" ng-class="{'unchecked': !a.checked}">
                        <div ng-switch="a.accepted" class="col-md-2 col-sm-3 col-xs-12">
                            <span ng-switch-default class="btn-sm btn-warning">
                                 {{trans("notification.LABELS.wait")}}
                            </span>
                            <span ng-switch-when="1" class="btn-sm btn-primary">
                                 {{trans("notification.LABELS.accepted")}}
                            </span>
                            <span ng-switch-when="0" class="btn-sm btn-danger">
                                {{trans("notification.LABELS.refused")}}
                            </span>
                        </div>
                        <div class="col-md-9 col-sm-7 col-xs-12 d-flex">
                            <span translate="notification.You"></span>
                            <img ng-src="/storage/avatars/<%a.receiver_id%>.small.jpg" class="img-circle img-fluid inner" />
                            <a class="title" href="/profile/<%a.receiver_id%>" target="_blank" ng-bind="a.username"></a>
                            &nbsp;
                            <span ng-if="!a.quit" translate="notification.Receiver" translate-values='{project:a.title}'></span>
                            <span ng-if="a.quit">
                                <span translate="notification.Quit" translate-values='{project:a.title}'></span>
                                <span ng-if="a.name" translate="notification.Member"></span>
                            </span>
                            <strong class="text-info" ng-bind="a.name"></strong>
                        </div>
                        <div class="col-md-1 col-sm-2 col-xs-12 text-right">
                            <div ng-click="read(a, 1)" class="btn text-info">
                                <span ng-if='message.id != a.id'>
                                     {{trans("notification.LABELS.read")}}
                                </span>
                                <span ng-if="message.id == a.id">
                                    {{trans("notification.LABELS.envelop")}}
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-2 col-sm-3 col-xs-12">
                            <span class="text-muted small" ng-bind="a.created_at|limitTo:16"></span>
                        </div>
                        <div class="col-md-9 col-sm-7 col-xs-12">
                            <div ng-if="message.id == a.id" class="p-3 bg-white">
                                <div ng-bind="message.letter"></div>
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-2 col-xs-12 text-right">
                             <div class="btn text-danger" ng-click="deleteInvitation(a)">{{trans('layout.BUTTONS.delete')}}</div>
                        </div>
                    </div>
                    <hr ng-if="!$last">
                </div>
            </div>
        </div>
        <div class="tab-pane fade py-3" id="messagesSent" role="tabpanel" aria-labelledby="messagesSent-tab">
            <div ng-if="!messages.length">
                @include('templates.empty');
            </div>
            <div ng-repeat="m in messages">
                <div class="row">
                    <div class="col-md-2 col-sm-3 col-xs-4 media">
                        <img ng-src="/storage/avatars/<%m.receivers[0].user_id%>.small.jpg" class="rounded-circle img-fluid" />
                        <div class="media-body ml-1">
                            <a class="text-primary" href="/profile/<%m.receivers[0].user_id%>" target="_blank" ng-bind="m.receivers[0].username"></a>
                        </div>
                    </div>
                    <div class="col-md-9 col-sm-7 col-xs-8">
                        <span ng-if="m.parent_id">{{trans('notification.LABELS.reply')}}: </span>
                        <span ng-bind="m.subject"></span>
                    </div>
                    <div class="col-md-1 col-sm-2 col-xs-12 text-right">
                        <div ng-click="read(m, 1)" class="btn">
                            <span ng-if='message.id != m.id'>
                                 {{trans("notification.LABELS.read")}}
                            </span>
                            <span ng-if="message.id == m.id">
                                {{trans("notification.LABELS.envelop")}}
                            </span>
                        </div>
                    </div>
                    <div class="loader-content" ng-if="m.deleting"><div class="loader"></div></div>
                </div>
                <div class="row">
                    <div class="col-md-2 col-sm-3 col-xs-4">
                        <span class="text-muted" ng-bind="m.created_at|limitTo:16"></span>
                    </div>
                    <div class="col-md-9 col-sm-7 col-xs-8">
                        <div ng-if="message.id == m.id" class="bg-white px-4 py-3">
                            <div class="message" ng-bind="message.letter"></div>
                            <hr>
                            <div ng-repeat="e in message.replies">
                                <div ng-if="!e.sender">
                                    <div >
                                        <img ng-src="/storage/avatars/<%e.user_id%>.small.jpg" class="rounded-circle img-fluid inner" />&nbsp;
                                        <a href="/profile/<%e.user_id%>" target="_blank"
                                           class="title" ng-bind="e.username">
                                        </a>
                                        &nbsp;
                                        <span class="text-muted small" ng-bind="e.created_at|limitTo:16"></span>
                                    </div>
                                    <blockquote class="padding-left-md" ng-bind="e.body"></blockquote>
                                </div>
                                <div ng-if="e.sender" class="blockquote-reverse">
                                    <div >
                                        {{trans('layout.LABELS.you')}}
                                        &nbsp;
                                        <span class="text-muted small" ng-bind="e.created_at|limitTo:16"></span>
                                    </div>
                                    <div ng-bind="e.body">
                                    </div>
                                </div>
                                <hr>
                            </div>

                        </div>
                    </div>
                    <div class="col-lg-1 col-md-2 col-sm-2 col-xs-12 text-right">
                        <div class="btn text-danger" ng-click="deleteMessage(m)">{{trans('layout.BUTTONS.delete')}}</div>
                    </div>
                </div>
                <hr ng-if="!$last">
            </div>
        </div>
        @include('templates.pagination')
    </div>

</div>
@endsection
@section('script')
    <script src="/js/directives/message.js"></script>
    <script src="/js/controllers/admin/message.js"></script>
@endsection