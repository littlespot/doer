<div class="modal fade" id="deleteInvitationModal" tabindex="-1" role="dialog" aria-labelledby="deleteInvitationModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                {{trans('notification.HEADER.delete_invitation')}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="modal-body">
                <div>{{trans('notification.MESSAGES.delete')}}</div>
                <div class="alert alert-danger">{{trans('notification.ALERTS.delete_invitation')}}</div>
            </div>
            <div class="modal-footer d-flex px-5">
                <button class="btn btn-danger mr-auto" type="button" data-dismiss="modal" >
                    {{trans("project.BUTTONS.cancel")}}
                </button>
                <button class="btn btn-primary" type="button" ng-click="invitationDeleted()" >
                    {{trans("project.BUTTONS.confirm")}}
                </button>
            </div>
        </div>
    </div>
</div>
<div invite-content class="modal fade" id="invitationConfirmModal" tabindex="-1" role="dialog" aria-labelledby="invitationConfirmModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 translate="notification.message.i"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        </div>
    </div>
</div>

<div invite-content class="modal fade bd-example-modal-lg" id="invitationModal" tabindex="-1" role="dialog" aria-labelledby="invitationModalTitle" aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                {{trans('notification.LABELS.invitations')}}
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-5"  ng-if="invitation.projects && invitation.projects.length > 0">
                <h5 class="d-flex justify-content-center" id="invitationModalTitle">
                    <div class="text-uppercase">{{trans('notification.LABELS.invitations')}}&nbsp;</div>
                    <div class="dropdown bg-white"  id="receiver" ng-if="invitation.receivers">
                        <a class="text-primary dropdown-toggle px-2" id="dropdownReceiver" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <span ng-bind="invitation.receiver.username"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownReceiver">
                            <a class="dropdown-item" href="javascript:void(0)" ng-click="invitation.receiver = o" ng-repeat="o in invitation.receivers"  ng-show="invitation.receiver.id != o.id">
                                <span ng-bind="o.username"></span>
                            </a>
                        </div>
                    </div>
                    <div ng-if="!invitation.receivers"><span class="text-danger" ng-bind="invitation.receiver.username"></span></div>
                </h5>
                <h6 class="d-flex">
                    <div class="text-uppercase">&nbsp;{{trans('notification.LABELS.as')}}</div>
                    <div class="dropdown bg-white" id="occupation">
                        <a class="text-info dropdown-toggle px-2" id="dropdownOccupation" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" ng-disabled="invitation.sending">
                            <span ng-bind="invitation.occupation.name"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownOccupation">
                            <a class="dropdown-item" href="javascript:void(0)" ng-repeat="o in invitation.occupations" ng-show="invitation.occupation.id != o.id" ng-click="invitation.occupation = o">
                                <span ng-bind="o.name"></span>
                            </a>
                        </div>
                    </div>&nbsp;
                    <div class="text-uppercase">{{trans('notification.LABELS.participate')}}&nbsp;</div>

                    <div class="dropdown bg-white" id="project" ng-if="invitation.projects.length > 1">
                        <a class="text-info dropdown-toggle px-2" id="dropdownProject" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                            <span ng-bind="invitation.project.title"></span>
                        </a>
                        <div class="dropdown-menu" aria-labelledby="dropdownProject">
                            <a class="dropdown-item" href="javascript:void(0)" ng-repeat="p in invitation.projects" ng-show="invitation.project.id != p.id" ng-click="invitation.project = p">
                                <span ng-bind="o.name"></span>
                            </a>
                        </div>
                    </div>
                    <div class="text-info px-2"  ng-if="invitation.projects.length == 1" ng-bind="invitation.project.title"></div>
                </h6>
                <form class="pt-3" name="inviteForm" ng-if="invitation.projects && invitation.projects.length >0">
                    <textarea class="form-control" ng-model="invitation.message" name="mailBody" placeholder="{{trans('project.PLACES.invitation')}}"
                              rows="20" ng-minlength="10" ng-maxlength="2000" required
                              ng-readonly="invitation.sending">
                    </textarea>
                    <div class="error" role="alert" ng-class="{'visible':inviteForm.mailBody.$touched}">
                        <span ng-show="!invitation.message">{{trans('project.ERRORS.require.invitation')}}</span>
                        <span ng-show="invitation.message && invitation.message.length <10">{{trans('project.ERRORS.require.minlength', ['cnt'=>10])}}</span>
                        <span ng-show="invitation.message && invitation.message>2000">{{trans('project.ERRORS.require.maxlength', ['cnt'=>2000])}}</span>
                    </div>
                </form>
            </div>
            <h1 class="modal-body p-5 text-center text-danger" ng-if="!invitation.projects || invitation.projects.length == 0">
                {{trans('notification.ERRORS.projects')}}
            </h1>
            <div class="modal-footer d-flex px-5" ng-if="invitation.projects && invitation.projects.length > 0">
                <button class="btn btn-outline-danger" data-dismiss="modal">
                    {{trans("project.BUTTONS.cancel")}}
                </button>
                <button class="btn btn-primary" ng-disabled="inviteForm.$invalid || invitation.sending" ng-click="sendInvite()">
                    {{trans("project.BUTTONS.confirm")}}
                </button>
            </div>
        </div>
    </div>
</div>

