@extends('project.top')

@section('tabcontent')
    <script type="text/ng-template" id="team.html">
        <div class="modal-body" id="modal-body">
            <h3 translate="project.MESSAGES.<%confirm%>" translate-values="{user:user}"></h3>
        </div>
        <form name="invitationForm" class="form-group text-center padding-sm">
        <textarea ng-model="invitation" name="invitation" placeholder="<% 'project.PLACES.invitation' | translate%>" class="form-controller" style="width: 100%"
                  ng-minlength="15" ng-maxlength="800" required></textarea>
            <div role="alert" class="error visible">
                <span ng-show="invitationForm.invitation.$error.required" translate="project.ERRORS.minlength.invitation"></span>
                <span ng-show="invitationForm.invitation.$error.minlength" translate="project.ERRORS.minlength.invitation"></span>
                <span ng-show="invitationForm.invitation.$error.maxlength" translate="project.ERRORS.maxlength.invitation"></span>
            </div>
        </form>
        <div class="modal-footer">
            <button class="btn btn-default" type="button" ng-click="$close(null)">
                {{trans("project.BUTTONS.cancel")}}
            </button>
            <button class="btn btn-danger" type="button" ng-click="$close(invitationForm.$valid ? invitation : null)" ng-disabled="invitationForm.$invalid">
                {{trans("project.BUTTONS.confirm")}}
            </button>
        </div>
    </script>
    <div class="content content-margin" ng-controller="preparationCtrl" ng-init="init('{{$project}}','{{$users}}')">
        <div class="alert alert-warning">
            {!! trans("project.ALERTS.team") !!}
        </div>
        <div team-content>
            @include('templates.team')
        </div>
    </div>
    @endsection
@section('tabscript')
    <script src="/js/directives/team.js"></script>
    <script src="/js/controllers/admin/team.js"></script>
@endsection