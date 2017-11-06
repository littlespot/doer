@extends('auth.cover')
@section('background')
    <div id="layers">
        <div class="backlayer" style="background-image: url(/images/layers/BG_A03.svg);">
            &nbsp;
        </div>
        <div class="backlayer" style="background-image: url(/images/layers/BG_A02.svg);">
            &nbsp;
        </div>
        <div class="backlayer" style="background-image: url(/images/layers/BG_A01.svg);">
            &nbsp;
        </div>
    </div>
@endsection
@section('content')
    <div class="col-lg-offset-4 col-lg-4 col-md-offset-2 col-md-8 col-sm-offset-1 col-sm-10 col-xs-12"
         ng-controller="invitationCtrl" ng-init="init('{{App::getLocale()}}')">
        <script type="text/ng-template" id="result.html">
            <div class="modal-body" id="modal-body">
                <h3 translate="qualification.message"></h3>
            </div>
        </script>
        <script type="text/ng-template" id="error.html">
            <div class="modal-body" id="modal-body">
                <h3 translate="failed"></h3>
            </div>
        </script>
        <form name="workform"  ng-show="wform.show" nvalidate>
            <div>
                <input type="text" class="form-text" translate translate-attr-placeholder="qualification.works"
                       name="title"
                       ng-model="newWork.title" ng-minlength="2" ng-maxlength="100"
                       ng-required />
            </div>
            <div class="error" role="alert" ng-class="{'visible':workform.title.$touched || wform.submit}">
            <span ng-show="workform.title.$error.required"
                  translate="qualification.ERRORS.require.title"></span>
                <span ng-show="workform.title.$error.minlength"
                      translate="qualification.ERRORS.minlength.title" translate-values="{min:2}"></span>
                <span ng-show="workform.title.$error.maxlength"
                      translate="qualification.ERRORS.maxlength.title" translate-values="{min:100}"></span>
            </div>
            <br/>
            <div>
                <input type="text" class="form-text" translate translate-attr-placeholder="qualification.link"
                       name="url" ng-change="error.url = false"
                       ng-model="newWork.url" ng-minlength="10" ng-maxlength="200" />
            </div>
            <div class="error" role="alert" ng-class="{'visible':workform.url.$touched || wform.submit}">
                <span ng-show="error.url" translate="qualification.ERRORS.repeat" translate-values="{min:10}"></span>
                <span ng-show="workform.url.$error.maxlength"
                      translate="qualification.ERRORS.maxlength.url" translate-values="{max:200}"></span>
                <span ng-show="workform.url.$error.minlength"
                      translate="qualification.ERRORS.minlength.url" translate-values="{min:10}"></span>
                <span ng-show="workform.url.$error.maxlength"
                      translate="qualification.ERRORS.maxlength.url" translate-values="{max:200}"></span>
            </div>
            <br/>
            <h5 translate="qualification.talent"></h5>
            <div class="flex-left form-group">
                <div ng-repeat="o in occupations|filter:{uid: ''}">
                    <span class="badge-rectangle" ng-bind="o.name"></span>
                    <span class="btn text-danger fa fa-times"
                          ng-click="removeTalent(o)"></span>
                </div>&nbsp;
                <select id="newOccupation" ng-model="newTalent" style="color: #999">
                    <option value="" disabled translate="personal.PLACES.occupation"></option>
                    <option class="text-primary" ng-repeat="o in occupations|filter:{uid: '!'}|orderBy:name" ng-value="o.id" ng-bind="o.name"></option>
                </select>
                <div class="btn btn-link text-danger fa fa-plus"
                     ng-class="hide"
                     ng-show="newTalent.length>0"
                     ng-click="addTalent(newTalent)"></div>
            </div>
            <div class="error pull-right" role="alert" ng-class="{'visible':error.occupation}">
                <span translate="personal.ERRORS.require.Occupation"></span>
            </div>
            <div class="form-group">
            <textarea class="form-control"
                      translate translate-attr-placeholder="personal.PLACES.description"
                      name="description"
                      ng-model="newWork.description"
                      ng-minlength="20" ng-maxlength="200" ng-required></textarea>
                <div class="error" role="alert" ng-class="{'visible':wform.description.$touched || wform.submit}">
                    <span ng-show="workform.description.$error.required"
                          translate="qualification.ERRORS.require.description"></span>
                    <span ng-show="workform.description.$error.minlength"
                          translate="qualification.ERRORS.require.description" translate-values="{min:20}"></span>
                    <span ng-show="workform.description.$error.maxlength"
                          translate="qualification.ERRORS.maxlength.description" translate-values="{max :200}"></span>
                </div>
            </div>
            <div class="text-right">
                <div class="btn btn-default" ng-click="wform.show=false"><span class="fa fa-undo"></span> </div>
                <div class="btn btn-primary"  ng-click="addWork(workform.$invalid)"><span class="fa fa-check"></span> </div>
            </div>
        </form>

        <form name="usrform" ng-show="!wform.show"  novalidate>
            <div>
                <input type="email" class="form-text"
                       translate translate-attr-placeholder="login.PLACES.email"
                       name="uEmail" ng-model="user.email"
                       ng-model-options="{ updateOn: 'blur' }"
                       required email/>
                <div class="error" role="alert" ng-class="{'visible':usrform.uEmail.$touched||usrform.$submitted}">
                <span ng-show="usrform.uEmail.$error.required"
                      translate="login.ERRORS.require.Email"></span>
                    <span ng-show="usrform.uEmail.$error.email"
                          translate="login.ERRORS.invalid.Email"></span>
                    <span ng-show="usrform.uEmail.$error.unique"
                          translate="login.ERRORS.unique.Email"></span>
                    <span ng-show="usrform.uEmail.$pending.unique"
                          translate="login.ERRORS.pending.Email"></span>
                </div>
            </div>
            <div class="form-group">
                <input type="email" class="form-text"
                       translate translate-attr-placeholder="login.PLACES.email2" name="uEmail2" ng-model="user.email2" ng-match="user.email"
                       required/>
                <div class="error" role="alert" ng-class="{'visible':usrform.uEmail2.$touched||usrform.$submitted}">
                <span ng-show="usrform.uEmail2.$error.match || user.email != user.email2"
                      translate="login.ERRORS.invalid.Email2"></span>
                </div>
            </div>

            <div>
            <textarea class="form-control" style="width: 100%"
                      translate translate-attr-placeholder="personal.PLACES.description"
                      name="presentation"
                      ng-model="user.presentation"
                      ng-minlength="20" ng-maxlength="200" required></textarea>
                <div class="error" role="alert" ng-class="{'visible':usrform.presentation.$touched||usrform.$submitted}">
                     <span ng-show="usrform.presentation.$error.required"
                           translate="login.ERRORS.require.Presentation"></span>
                    <span ng-show="usrform.presentation.$error.minlength"
                          translate="login.ERRORS.minlength.Presentation"></span>
                    <span ng-show="usrform.presentation.$error.maxlength"
                          translate="login.ERRORS.maxlength.Presentation"></span>
                </div>
            </div>
            <h5 ng-if="works.length > 0">
                <span translate="qualification.experience"></span>
            </h5>

            <div ng-repeat="w in works" ng-init="w.show = false;">
                <div ng-show="$index == currentPage - 1">
                    <div class="row" >
                        <h6 class="col-xs-11">
                            <span ng-bind="w.title" class="btn btn-link" ng-click="w.show = !w.show" ></span>&nbsp;
                            <span ng-if="w.url.length > 0">
                                <a ng-href="w.url" class="fa fa-paperclip"></span></a>
                            </span>
                        </h6>
                        <div class="col-xs-1">
                            <div class="btn text-danger" ng-click="removeWork($index)">
                                <span class="fa fa-times"></span>
                            </div>
                        </div>
                    </div>
                    <div class="flex-left form-group">
                        <div ng-repeat="o in w.occupations">
                            <span class="badge-rectangle" ng-bind="o.name"></span>
                            &nbsp;
                        </div>&nbsp;
                    </div>
                    <div style="margin-left: 15px">
                        <blockquote ng-show="w.show" ng-bind="w.description"></blockquote>
                    </div>
                </div>
            </div>

            <div class="text-center" ng-show="works.length > 1">
                <div class="btn-group" role="group">
                    <div class="btn btn-default btn-sm" ng-click="setPage(-1)">&lsaquo;</div>
                    <div class="btn btn-info btn-sm"><%currentPage%>&nbsp;/&nbsp; <%works.length%></div>
                    <div class="btn btn-default btn-sm" ng-click="setPage(1)">&rsaquo;</div>
                </div>
            </div>
            <br>
            <div class="btn btn-primary btn-block" ng-click="wform = {show:true, submit:false}"><span translate="qualification.button"></span> </div>
            <div class="error" role="alert" ng-class="{'visible':error.work}">
                <span translate="qualification.ERRORS.require.work"></span>
            </div>
            <p class="text-right">
                <a class="btn btn-default" ng-href="#/new"><span class="fa fa-undo"></span> </a>
                <span class="btn btn-primary" ng-disabled="usrform.$invalid"
                      ng-click="save(usrform.$valid)"><span class="fa fa-check"></span> </span>
            </p>
        </form>
    </div>
@endsection
@section('script')
    <script src="/js/controllers/auth/invitation.js"></script>
    <script lang="javascript">

        function init() {
            if( $(window).height() < 640){
                $("#layer").hide();
                return;
            }
            var xScale = ($(window).width() / 1600).toFixed(2);
            var ratio = $(window).width() / $(window).height();

            if (ratio >= 1.6)
                $('#layers .backlayer').css('background-size', 'cover');
            else
                $('#layers .backlayer').css('background-size', 'contain');

            $("#layers").show();

            var margin = $(window).height() - $('.header').height() - 330 * xScale;
            $('#content').height(margin);
        }

        $(document).ready(function () {
            init();
        })
    </script>
@endsection