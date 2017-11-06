<script type="text/ng-template" id="feedback.html">
    <div class="modal-body" id="modal-body">
        <h3 translate="notification.message.i"></h3>
    </div>
    <div class="modal-footer">
        <button class="btn btn-default" type="button" ng-click="$close()">OK</button>
    </div>
</script>
<div invite-content>
    <div style="position: relative" ng-if="invitation.projects && invitation.projects.length>0">
        <div class="text-center inline margin-top-sm">
            <div class="text-uppercase">{{trans('notification.LABELS.invitations')}}&nbsp;</div>
            <div class="dropdown" id="receiver" ng-if="invitation.receivers">
                <div class="dropdown-toggle" type="button" id="dropdownReceiver" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <span class="text-important" ng-bind="invitation.receiver.username"></span>
                    <span class="caret"></span>
                </div>
                <ul class="dropdown-menu">
                    <li ng-repeat="o in invitation.receivers" ng-show="invitation.receiver.id != o.id">
                        <a class="btn" href="#" ng-click="invitation.receiver = o">
                            <span ng-bind="o.username"></span>
                        </a>
                    </li>
                </ul>
            </div>
            <div ng-if="!invitation.receivers"><span class="text-important" ng-bind="invitation.receiver.username"></span></div>
            &nbsp;
            <div class="text-uppercase">&nbsp;{{trans('notification.LABELS.as')}}</div>
            <div class="dropdown" id="occupation">
                <div class="dropdown-toggle" type="button" id="dropdownOccupation" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <span class="text-important" ng-bind="invitation.occupation.name"></span>
                    <span class="caret"></span>
                </div>
                <ul class="dropdown-menu">
                    <li ng-repeat="o in invitation.occupations" ng-show="invitation.occupation.id != o.id">
                        <a class="btn" href="#" ng-click="invitation.occupation = o">
                            <span ng-bind="o.name"></span>
                        </a>
                    </li>
                </ul>
            </div>&nbsp;
            <span class="text-uppercase">{{trans('notification.LABELS.participate')}}&nbsp;</span>

            <div class="dropdown" id="project">
                <div class="dropdown-toggle" type="button" id="dropdownProject" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    <span class="text-important" ng-bind="invitation.project.title"></span>
                    <span class="caret" ng-if="invitation.projects.length>1"></span>
                </div>
                <ul class="dropdown-menu">
                    <li ng-repeat="p in invitation.projects" ng-show="invitation.project.id != p.id">
                        <a class="btn" href="#" ng-click="invitation.project = p">
                            <span  ng-bind="p.title"></span>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <form name="inviteForm" class="margin-top-md">
        <textarea class="form-control" ng-model="invitation.message" name="mailBody"
                  rows="20" ng-minlength="10" ng-maxlength="2000" required></textarea>
            <div class="error" role="alert" ng-class="{'visible':inviteForm.mailBody.$touched || inviteForm.$submitted}">
                <span ng-show="inviteForm.mailBody.$error.required" translate="notification.ERRORS.require.Invitation"></span>
                <span ng-show="inviteForm.mailBody.$error.minlength" translate="notification.ERRORS.minlength.Invitation"></span>
                <span ng-show="inviteForm.mailBody.$error.maxlength" translate="notification.ERRORS.maxlength.Invitation"></span>
            </div>
            <div class="text-right">
                <div class="btn btn-default" ng-click="cancelInvite();">
                    <span class="fa fa-undo"></span>
                </div>
                <div class="btn btn-primary" ng-disabled="inviteForm.$invalid" ng-click="sendInvite()">
                    <span class="fa fa-send-o"></span>
                </div>
            </div>
        </form>
        <div class="loader-content" ng-if="invitation.loading"><div class="loader"></div></div>
    </div>
    <div class="h1 text-center text-danger" ng-if="!invitation.projects || invitation.projects.length == 0">
        {{trans('notification.errors.projects')}}
    </div>

</div>

