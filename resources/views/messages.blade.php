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
<div class="container" ng-controller="messagesCtrl" ng-init="init('{{$roles}}', '{{$invitations}}', '{{$messages}}')">
    <div class="margin-top-lg">
        <div class="flex-rows">
            <div class="flex-left">
                <div class="text-right">
                    <div>{{trans("notification.HEADER.prefix")}}</div>
                    <div class="margin-top-sm">{{trans("notification.HEADER.got")}}</div>
                </div>
                <div class="text-muted padding-left-md">
                    <div>
                        <span id="in_invitations_cnt" class="text-important">
                            <strong ng-if="!in_invitations_cnt" translate="NONE"></strong>
                            <strong ng-if="in_invitations_cnt" ng-bind="in_invitations_cnt"></strong>
                        </span>&nbsp;
                        <span>{{trans("notification.LABELS.invitations")}}</span>,&nbsp;
                        <span>{{trans("notification.HEADER.sent")}}</span>
                        <span id="out_invitations_cnt" class="text-important">
                            <strong ng-if="!out_invitations_cnt" translate="NONE"></strong>
                            <strong ng-if="out_invitations_cnt" ng-bind="out_invitations_cnt"></strong>
                        </span>&nbsp;
                        <span>{{trans("notification.LABELS.invitations")}}</span>
                    </div>
                    <div class="margin-top-sm">
                        <span id="in_messages_cnt" class="text-important">
                            <strong ng-if="!in_messages_cnt" translate="NONE"></strong>
                            <strong ng-if="in_messages_cnt" ng-bind="in_messages_cnt"></strong>
                        </span>&nbsp;
                        <span>{{trans("notification.LABELS.messages")}}</span>,&nbsp;
                        <span>{{trans("notification.HEADER.sent")}}</span>
                        <span id="out_messages_cnt" class="text-important">
                            <strong ng-if="!out_messages_cnt" translate="NONE"></strong>
                            <strong ng-if="out_messages_cnt" ng-bind="out_messages_cnt"></strong>
                        </span>&nbsp;
                        <span>{{trans("notification.LABELS.messages")}}</span>
                    </div>
                </div>
            </div>
            <div class="flex-cols">
                <div>
                    <div ng-if="invitation.projects.length" ng-click="invite()" class="btn-squash text-center"
                        ng-class="{'btn-text-info': selectedView != 1, 'btn-text-important': selectedView == 1}">
                        <span class="text-uppercase">{{trans("notification.LABELS.invitations")}}</span>
                    </div>
                </div>
                <div>
                    <div class="btn-squash text-center" ng-click="write()"
                         ng-class="{'btn-text-info': selectedView != 2, 'btn-text-important': selectedView == 2}">
                        <span class="text-uppercase">{{trans("notification.LABELS.messages")}}</span>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <div class="text-center">
            <span translate="notification.Owns" translate-values="{cnt:projects.length}"></span>
        </div>
    </div>
    <div class="margin-top-lg">
        <div ng-if="selectedView == 1" class="letter margin-bottom-md">
            @include('templates.invitation')
        </div>
        <form name="messageForm" ng-if="selectedView == 2" class="letter margin-bottom-md" style="position: relative">
            <div class="row  margin-top-sm">
                <div class="row">
                    <div class="col-sm-1 col-xs-2 text-right flex-bottom">
                        <span class="text-uppercase small" translate="notification.Tos"></span>
                    </div>
                    <div class="col-sm-10 col-xs-9">
                        <angucomplete-alt id="author" input-name="member"
                                          placeholder="{{trans('notification.LABELS.receivers')}}"
                                          pause="100"
                                          selected-object="selectedUser"
                                          local-data="userIndex"
                                          search-fields="username"
                                          title-field="username"
                                          description-field="location"
                                          image-uri="/context/avatars"
                                          image-field="id"
                                          minlength="1"
                                          input-class="form-text"
                                          match-class="highlight"
                                          text-no-results="{{trans('layout.MENU.none')}}"
                                          text-searching="{{trans('layout.MENU.searching')}}"/>
                    </div>
                </div>
                <div class="row margin-top-sm">
                    <div class="col-sm-offset-1 col-xs-offset-2 col-sm-10 col-xs-9">
                        <input type="text" ng-model="mail.subject" name="subject" class="form-text"
                               placeholder="{{trans('notification.LABELS.subject')}}"
                               ng-maxlength="40" ng-minlength="4" required />
                        <div class="error" role="alert" ng-class="{'visible':messageForm.subject.$touched || messageForm.$submitted}">
                            <span ng-show="messageForm.subject.$error.required" translate="notification.ERRORS.minlength.Subject"></span>
                            <span ng-show="messageForm.subject.$error.minlength" translate="notification.ERRORS.minlength.Subject"></span>
                            <span ng-show="messageForm.subject.$error.maxlength" translate="notification.ERRORS.maxlength.Subject"></span>
                        </div>
                    </div>
                </div>
            </div>

            <div class="margin-top-md">
                <textarea class="form-control" ng-model="mail.body" name="mailBody"
                  rows="10" ng-minlength="10" ng-maxlength="800" required></textarea>
                <div class="error" role="alert" ng-class="{'visible':messageForm.mailBody.$touched || messageForm.$submitted}">
                    <span ng-show="messageForm.mailBody.$error.required" translate="notification.ERRORS.minlength.Message"></span>
                    <span ng-show="messageForm.mailBody.$error.minlength" translate="notification.ERRORS.minlength.Message"></span>
                    <span ng-show="messageForm.mailBody.$error.maxlength" translate="notification.ERRORS.maxlength.Message"></span>
                </div>
                <div class="text-right">
                    <div class="btn btn-default" ng-click="cancel()">
                        <span class="fa fa-undo"></span>
                    </div>
                    <div class="btn btn-primary" ng-disabled="messageForm.$invalid" ng-click="sendMail(selectedUser, messageForm.$invalid)">
                        <span class="fa fa-send-o"></span>
                    </div>
                </div>
            </div>
            <div class="loader-content" ng-if="mail.loading"><div class="loader"></div></div>
        </form>
        <uib-tabset justified="true" ng-show="!selectedView">
            <uib-tab select="selectTopTab('invitations', 'in')">
                <uib-tab-heading>
                    <span>{{trans("notification.LABELS.received_invitation")}}</span>
                    <sup id="in_invitations">{{$invitations_cnt > 0 ? $invitations_cnt : ''}}</sup>
                </uib-tab-heading>
                <div ng-if="messages.length == 0">
                    @include('templates.empty');
                </div>
                <br/>
                <div class="content">
                    <div ng-repeat="a in messages">
                        <div class="message-container row" ng-class="{'unchecked': !a.checked}">
                            <div ng-switch="a.accepted" class="col-md-1 col-sm-2 col-xs-3">
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

                            <div class="col-md-10 col-sm-8 col-xs-6 padding-left-lg">
                                <div>
                                    <img ng-src="/context/avatars/<%a.user_id%>.small.jpg" class="img-circle img-responsive inner" />
                                    <a href="/profile/<%a.user_id%>" target="_blank"
                                       class="title" ng-bind="a.username">
                                    </a>
                                    &nbsp;
                                    <span class="text-muted small" ng-bind="a.created_at|limitTo:16"></span>
                                    <span class="margin-left-md" >
                                        <span ng-if="!a.quit" translate="notification.InvitationSubject" translate-values='{project:a.title}'></span>
                                        <span ng-if="a.quit">
                                            <span translate="notification.QuitSubject" translate-values='{project:a.title}'></span>
                                            <span ng-if="a.name" translate="notification.Member"></span>
                                        </span>
                                        <strong class="text-info" ng-bind="a.name"></strong>
                                    </span>
                                </div>
                                <div ng-if="message.id == a.id" class="padding-md">
                                    <div class="letter">
                                        <div class="message" ng-bind-html="message.letter"></div>
                                        <div class="flex-rows margin-top-sm">
                                            <span class="btn text-danger" translate="project.BUTTONS.delete" ng-click="deleteInvitation(a)"></span>
                                            <div ng-if="a.accepted == null">
                                                <div class="btn text-warning" translate="notification.refuse" ng-click="updateInvitation(a, 0)"></div>
                                                <div class="btn text-success" translate="notification.accept" ng-click="updateInvitation(a, 1)">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="loader-content" ng-if="a.deleting"><div class="loader"></div></div>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-2 col-xs-3">
                                <div ng-click="read(a)" class="btn btn-link">
                                    <span ng-if='message.id != a.id' translate="notification.Read"></span>
                                    <span ng-if="message.id == a.id" translate="notification.Envelop"></span>
                                </div>
                            </div>
                        </div>

                        <hr ng-if="!$last">
                    </div>
                </div>
            </uib-tab>
            <uib-tab select="selectTopTab('messages', 'in')">
                <uib-tab-heading>
                    <span>{{trans("notification.LABELS.received_message")}}</span>
                    <sup id="in_messages">{{$messages_cnt > 0 ? $messages_cnt : ''}}</sup>
                </uib-tab-heading>
                <div ng-if="messages.length == 0">
                    @include('templates.empty');
                </div>
                <br/>
                <div class="content">
                    <div ng-repeat="m in messages">
                        <div class="message-container row" ng-class="{'unchecked': !m.checked}">
                            <div class="col-md-1 col-sm-2 col-xs-3">
                                <span ng-if="!m.replied" class="btn-sm btn-warning">
                                     {{trans("notification.LABELS.wait")}}
                                </span>
                                <span ng-if="m.replied" class="btn-sm btn-primary">
                                     {{trans("notification.LABELS.replied")}}
                                </span>
                            </div>
                            <div class="col-md-10 col-sm-8 col-xs-6">
                                <div>
                                    <img ng-src="/context/avatars/<%m.sender_id%>.small.jpg" class="img-circle img-responsive inner" />&nbsp;
                                    <a href="/profile/<%m.sender_id%>" target="_blank"
                                       class="title" ng-bind="m.username">
                                    </a>
                                    &nbsp;
                                    <span class="text-muted small" ng-bind="m.created_at|limitTo:16"></span>
                                    <span class="margin-left-md">
                                        <span ng-if="m.parent_id" translate="notification.Re"></span>
                                        <span class="text-chocolate" ng-bind="m.subject"></span>
                                    </span>
                                </div>
                                <div ng-if="message.id == m.id" class="padding-md">
                                    <div ng-if="message.parent_id">
                                        <div class="retro padding-sm flex-rows">
                                            <div translate="notification.Replies" translate-values="{cnt:message.replies.length}"></div>
                                            <div class="btn btn-link">
                                                <span ng-if='!message.shown' ng-click="message.shown=true;" translate="notification.Read"></span>
                                                <span ng-if="message.shown" ng-click="message.shown=false;" translate="notification.Envelop"></span>
                                            </div>
                                        </div>
                                        <div class="retro padding-sm small" ng-show="message.shown">
                                            <div ng-repeat="e in message.replies">
                                                <div ng-if="!e.sender">
                                                    <div >
                                                        <img ng-src="/context/avatars/<%e.user_id%>.small.jpg" class="img-circle img-responsive inner" />&nbsp;
                                                        <a href="/profile/<%e.user_id%>" target="_blank"
                                                           class="title" ng-bind="e.username">
                                                        </a>
                                                        &nbsp;
                                                        <span class="text-muted small" ng-bind="e.created_at|limitTo:16"></span>
                                                    </div>
                                                    <div ng-bind="e.body">
                                                    </div>
                                                    <hr class="text-chocolate">
                                                </div>
                                                <div ng-if="e.sender" class="blockquote-reverse">
                                                    <div >
                                                        <span translate="notification.You"></span>
                                                        &nbsp;
                                                        <span class="text-muted small" ng-bind="e.created_at|limitTo:16"></span>
                                                    </div>
                                                    <div ng-bind="e.body">
                                                    </div>
                                                    <hr class="text-chocolate">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="letter">
                                        <div class="message" ng-bind-html="message.letter"></div>
                                        <div class="text-right margin-top-sm">
                                            <div ng-if="m.parent_id" class="btn text-warning" translate="project.BUTTONS.deleteAll" ng-click="deleteMessage(m, 1)"></div>
                                            <span class="btn text-danger" translate="project.BUTTONS.delete" ng-click="deleteMessage(m)"></span>
                                        </div>
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
                                    <div class="loader-content" ng-if="m.deleting"><div class="loader"></div></div>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-2 col-xs-3">
                                <div ng-click="read(m)" class="btn btn-link">
                                    <span ng-if='message.id != a.id' translate="notification.Read"></span>
                                    <span ng-if="message.id == a.id" translate="notification.Envelop"></span>
                                </div>
                            </div>
                        </div>
                        <hr ng-if="!$last">
                    </div>
                </div>
            </uib-tab>
            <uib-tab select="selectTopTab('messages', 'out')">
                <uib-tab-heading>
                    <span> {{trans("notification.LABELS.sent_message")}}</span>
                </uib-tab-heading>
                <div ng-if="messages.length == 0">
                    @include('templates.empty');
                </div>
                <br>
                <div class="content">
                    <div ng-repeat="m in messages">
                        <div class="message-container row">
                            <div class="col-md-11 col-sm-10 col-xs-9">
                                <div class="flex-rows">
                                    <div>
                                        <span class="text-muted small" ng-bind="m.created_at|limitTo:16"></span>
                                        <span class="text-chocolate" ng-bind="m.subject"></span>
                                    </div>
                                    <div class="btn btn-danger" translate="project.BUTTONS.delete" ng-click="deleteMessage(m)"></div>
                                </div>
                                <div class="small">
                                    <span translate="notification.Tos"></span>&nbsp;<span ng-repeat="u in m.receivers">
                                        <a class="text-primary" href="/profile/<%u.user_id%>" target="_blank" ng-bind="u.username"></a>
                                        <span ng-if="!$last">,&nbsp;</span>
                                    </span>
                                </div>
                                <div ng-if="message.id == m.id" class="padding-md">
                                    <div ng-if="message.parent_id">
                                        <div class="retro padding-sm flex-rows">
                                            <div translate="notification.Replies" translate-values="{cnt:message.replies.length}"></div>
                                            <div class="btn btn-link">
                                                <span ng-if='!message.shown' ng-click="message.shown=true;" translate="notification.Read"></span>
                                                <span ng-if="message.shown" ng-click="message.shown=false;" translate="notification.Envelop"></span>
                                            </div>
                                        </div>
                                        <div class="retro padding-sm small" ng-show="message.shown">
                                            <div ng-repeat="e in message.replies">
                                                <div ng-if="!e.sender">
                                                    <div >
                                                        <img ng-src="/context/avatars/<%e.user_id%>.small.jpg" class="img-circle img-responsive inner" />&nbsp;
                                                        <a href="/profile/<%e.user_id%>" target="_blank"
                                                           class="title" ng-bind="e.username">
                                                        </a>
                                                        &nbsp;
                                                        <span class="text-muted small" ng-bind="e.created_at|limitTo:16"></span>
                                                    </div>
                                                    <div class="padding-left-md" ng-bind="e.body">
                                                    </div>
                                                    <hr class="text-chocolate">
                                                </div>
                                                <div ng-if="e.sender" class="blockquote-reverse">
                                                    <div >
                                                        <span translate="notification.You"></span>
                                                        &nbsp;
                                                        <span class="text-muted small" ng-bind="e.created_at|limitTo:16"></span>
                                                    </div>
                                                    <div ng-bind="e.body">
                                                    </div>
                                                    <hr class="text-chocolate">
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="letter">
                                        <div class="message" ng-bind-html="message.letter"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1 col-sm-2 col-xs-3">
                                <div ng-click="read(m, 1)" class="btn btn-link">
                                    <span ng-if='message.id != m.id' translate="notification.Read"></span>
                                    <span ng-if="message.id == m.id" translate="notification.Envelop"></span>
                                </div>
                            </div>
                            <div class="loader-content" ng-if="m.deleting"><div class="loader"></div></div>
                        </div>

                        <hr ng-if="!$last">
                    </div>
                </div>
            </uib-tab>
            <uib-tab select="selectTopTab('invitations', 'out')">
                <uib-tab-heading>
                    <span>{{trans("notification.LABELS.sent_invitation")}}</span>
                </uib-tab-heading>
                <div ng-if="messages.length == 0">
                    @include('templates.empty');
                </div>
                <div class="content">
                    <div ng-repeat="a in messages">
                    <div class="message-container row" ng-class="{'unchecked': !a.checked}">
                        <div ng-switch="a.accepted" class="col-md-1 col-sm-2 col-xs-3">
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

                        <div class="col-md-10 col-sm-8 col-xs-6 padding-left-lg">
                            <div class="flex-rows">
                                <div>
                                    <span class="text-muted small" ng-bind="a.created_at|limitTo:16"></span>

                                    <span translate="notification.You"></span>
                                    <img ng-src="/context/avatars/<%a.receiver_id%>.small.jpg" class="img-circle img-responsive inner" />
                                    <a class="title" href="/profile/<%a.receiver_id%>" target="_blank" ng-bind="a.username"></a>
                                    &nbsp;
                                    <span ng-if="!a.quit" translate="notification.Receiver" translate-values='{project:a.title}'></span>
                                    <span ng-if="a.quit">
                                        <span translate="notification.Quit" translate-values='{project:a.title}'></span>
                                        <span ng-if="a.name" translate="notification.Member"></span>
                                    </span>
                                    <strong class="text-info" ng-bind="a.name"></strong>
                                </div>
                                <div ng-if="a.accepted == null" class="btn text-danger" translate="project.BUTTONS.delete" ng-click="deleteInvitation(a)"></div>
                            </div>
                            <div ng-if="message.id == a.id" class="padding-md">
                                <div class="letter">
                                    <div class="message" ng-bind-html="message.letter"></div>
                                    <div class="text-right margin-top-sm" ng-if="a.accepted == null">
                                        <span class="btn text-danger" ng-click="deleteInvitation(a)">
                                            {{trans("project.BUTTONS.delete")}}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-1 col-sm-2 col-xs-3">
                            <div ng-click="read(a, 1)" class="btn btn-link">
                                <span ng-if='message.id != a.id'>
                                     {{trans("notification.LABELS.read")}}
                                </span>
                                <span ng-if="message.id == a.id">
                                    {{trans("notification.LABELS.envelop")}}
                                </span>
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
@endsection
@section('script')
    <script src="/js/directives/message.js"></script>
    <script src="/js/controllers/admin/message.js"></script>
@endsection