<style>
    .sns-gallery{
        position: absolute;left:0;top:36px; width: 36px; z-index: 100; overflow-y: hidden;
    }

    .sns-gallery>img{
        width: 36px;
        cursor:pointer;
    }

    .sns-gallery>img:hover{
       opacity: 0.5;
    }

</style>
<div class="modal fade" id="deleteSnsModal" tabindex="-1" role="dialog" aria-labelledby="deleteSnsModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="modal-body">
                <div>{{trans('personal.MESSAGES.delete_sns')}}</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal" >
                    {{trans("project.BUTTONS.cancel")}}
                </button>
                <button class="btn btn-danger" type="button" ng-click="snsDeleted()" >
                    {{trans("project.BUTTONS.confirm")}}
                </button>
            </div>
        </div>
    </div>
</div>
<div id="social" ng-init="selectTab()" class="my-5">
    <div ng-repeat="(key, value) in sns">
        <form name="snsform_<%key%>">
            {{csrf_field()}}
            <h4 translate="personal.SNS.<%key%>"></h4>
            <div class="row py-1">
                <div class="col-lg-2 col-md-4 col-sm-6 col-xs-12" ng-repeat="s in value|filter:{sns_id:'!!'}">
                    <div class="card">
                        <img ng-src="/images/sns/<%s.id%>.png"  class="card-img-top"/>
                        <div class="card-body text-center">
                            <input type="text" ng-readonly="editedSns.sns_id != s.sns_id"
                                   class="form-control" ng-model="s.sns_name" />
                        </div>
                        <div class="card-footer d-flex">
                            <div class="text-danger mr-auto fa" ng-class="{'fa-trash':editedSns.sns_id != s.sns_id, 'fa-undo':editedSns.sns_id == s.sns_id}"
                                 ng-click="deleteSns(s)"></div>
                            <div class="fa" ng-class="{'fa-edit':editedSns.sns_id != s.sns_id, 'text-success fa-check':editedSns.sns_id == s.sns_id}"
                                 ng-click="updateSns(s)"></div>
                        </div>
                    </div>
                </div>
            </div>
            <br/>
            <div class="row">
                <div class="col-lg-6 col-md-4 col-sm-push-0"></div>
                <div class="col-lg-5 col-md-6 col-sm-9 col-xs-8">
                    <div class="media" ng-show="chosenSns.type == key" style="position: relative">
                        <img class="align-self-start " ng-if="chosenSns.id" ng-src="/images/sns/<%chosenSns.id%>.png" ng-attr-title='<%chosenSns.name%>'
                             style="width:36px;" ng-click="value.viewed = true;"/>
                        <span class="btn fa fa-caret-down" ng-click="value.viewed = true;"></span>
                        <div class="sns-gallery" ng-show="value.viewed">
                            <img ng-repeat="s in value|filter:{sns_id:'!'}" ng-if="s.id != chosenSns.id" ng-src="/images/sns/<%s.id%>.png" ng-attr-title='<%s.name%>'
                                class="my-1" ng-click="choseSns(s)" />
                        </div>

                        <div class="media-body pt-2">
                            <div class="input input--isao"  >
                                <input type="text" class="input__field input__field--isao" name="new_sns_<%key%>" id="new_sns_<%key%>"
                                       ng-model="chosenSns.sns_name"/>
                                <label class="input__label input__label--isao" for="new_sns_<%key%>"
                                       data-content="{{trans('personal.PLACES.sns_name')}}">
                            <span class="input__label-content input__label-content--isao">
                                <i class="text-danger pr-1">*</i>
                                <span ng-if="!chosenSns.sns_name || chosenSns.sns_name.length<=40" ng-class="{'text-danger':!chosenSns.sns_name}">{{trans('personal.PLACES.sns_name')}}</span>
                                <span class="text-danger" ng-if="chosenSns.sns_name.length>40">{{trans('personal.ERRORS.maxlength_sns_name', ['cnt'=>40])}}</span>
                            </span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-1 col-md-2 col-sm-3 col-xs-4 btn-group">
                    <div class="btn fa fa-plus" ng-if="chosenSns.type != key" ng-click="addSns(key)"></div>
                    <div class="btn text-success fa fa-check" ng-if="chosenSns.type == key" ng-click="snsSaved(key)"></div>
                    <div class="btn btn-default fa fa-undo" ng-click="cancelSns(key);" ></div>
                </div>
            </div>
            <hr>
        </form>
    </div>
</div>