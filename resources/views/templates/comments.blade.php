<link href="/css/message.css" rel="stylesheet" />
<!-- Modal -->
<div class="modal fade" id="deleteCommentModal" tabindex="-1" role="dialog" aria-labelledby="deleteCommentModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center">
                {{trans('messages.alert.delete_comment')}}
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" data-dismiss="modal">{{trans("project.BUTTONS.cancel")}}</button>
                <button class="btn btn-danger" type="button" ng-click="deleteComment()">{{trans("project.BUTTONS.confirm")}}</button>
            </div>
        </div>
    </div>
</div>
<form id="mycomment" name="mycomment" class="paragraph" novalidate>
    <div style="position: relative">
        <textarea ng-model="newComment.message" name="message"
              rows="3"
              placeholder="{{trans('project.PLACES.comment', ['cnt'=>400])}}"
              class="form-control"
              ng-maxlength="400" required></textarea>
        <div class="py-1 d-flex justify-content-between">
            <div class="text-danger" role="alert">
                <span ng-show="mycomment.message.$error.maxlength">{{trans('project.ERRORS.maxlength.comment', ['cnt'=>400])}}</span>
            </div>
            <div>
                <div class="btn btn-outline-danger mr-3" ng-click="cancelResponse(false)">
                    <span class="fa fa-undo"></span>
                </div>
                <button class="btn btn-primary" ng-disabled="mycomment.$invalid || !newComment.message" ng-click="sendResponse(false)">
                    <span class="fa fa-paper-plane-o"></span>
                </button>
            </div>

        </div>
    </div>
    <div class="loader-content" ng-if="selectedComment.sending"><div class="loader"></div></div>
</form>
<br/>
<div style="position: relative" ng-repeat="c in comments">
    <div class="row pt-3">
        <div class="col-md-1 card">
            <a class="mt-1" href="/profile/<%c.user_id%>">
                <img class="card-img-top rounded-circle img-fluid" src="/storage/avatars/<%c.user_id%>.small.jpg" />
            </a>
            <div class="text-center">
                <div ng-if="c.mine" class="icon-large">
                    <span class="fa fa-caret-up text-default"></span>
                </div>
                <div ng-if="!c.mine" class="btn text-primary"  id="comment_info_<%c.id%>" ng-click="supportComment(c)" ng-class="{'text-success':c.supported}">
                    <span class="fa fa-caret-up" ></span>
                    <div class="counter" ng-if="c.supports_cnt > 0" ng-bind="c.supports_cnt"></div>
                </div>
            </div>
            <div ng-if="c.newest"><aside class="sheer" translate="NEW"></aside></div>
        </div>
        <div class="col-md-11 comment-container pb-3 border-bottom">
             <div class="small" ng-if="c.parent">
               <span class="text-muted small" translate="comment.subject"></span>&nbsp;
                <a class="text-secondary small" href="/profile/<%c.parent.user_id%>" ng-bind="c.parent.username"></a>
            </div>
            <div id="comment_message_<%c.id%>" class="comment_message">
                <div ng-if="c.parent">
                    <blockquote ng-if="!c.parent.id" class="blockquote text-danger">
                        <span  translate="project.ERRORS.deleted"></span>
                    </blockquote>
                    <blockquote class="small" ng-if="c.parent.id" ng-init="c.parent_showall = c.parent_message.length <= 40">
                        <span ng-bind="c.parent.message|limitTo:40"></span>
                        <span ng-if="c.parent.message.length > 40" ng-switch="c.parent_showall">
                            <span class="btn btn-link" ng-if="!c.parent_showall" ng-click="c.parent_showall=true">......</span>
                            <span ng-if="c.parent_showall" ng-bind="c.parent.message|limitTo:c.parent.message.length:40"></span>
                            <span ng-if="c.parent_showall" class="btn btn-link pull-right fa fa-caret-up" ng-click="c.parent_showall=false"></span>
                        </span>
                    </blockquote>
                </div>
                <div ng-bind="c.message" class="body"></div>
            </div>
            <div class="text-right text-muted small" ng-bind="c.created_at|limitTo:16"></div>
            <div class="hidden-bar d-flex justify-content-between" ng-class="{'br': !$last}">
                <div>
                    <a href="/profile/<%c.user_id%>" class="small text-pink" ng-bind="c.username"></a>
                </div>
                <div class="btn text-danger" ng-if="c.mine" ng-click="confirmDeleteComment(c)">
                    <span class="fa fa-trash"></span>
                </div>
                <div ng-if="!c.mine" class="align-self-end" ng-hide="selectedComment.parent && selectedComment.parent.id == c.id" ng-click="openResponse(c)">
                    <sapn class="btn badge" translate="comment.reply"></sapn>
                </div>
            </div>
            <form name="response" class="my-3" novalidate ng-if="selectedComment.parent && selectedComment.parent.id == c.id">
                <div style="position: relative">
                     <textarea ng-model="selectedComment.message" name="message"
                               rows="3" class="form-controller"
                               placeholder="<%'comment.placeholder' | translate:'{ min: 15, max:800 }' %>"
                               style="width:100%"
                               ng-minlength="15" ng-maxlength="800" required></textarea>
                    <div role="alert" class="error visible">
                       <span ng-show="response.message.$error.required && response.$submitted" translate="project.ERRORS.require.comment">
                       </span>
                        <span ng-show="response.message.$error.minlength" translate="project.ERRORS.minlength.comment" translate-values="{value:'15'}">
                        </span>
                        <span ng-show="response.message.$error.maxlength" translate="project.ERRORS.maxlength.comment" translate-values="{value:'800'}">
                        </span>
                    </div>
                    <div class="pt-1 text-right">
                        <span class="btn btn-outline-danger mr-3" ng-click="cancelResponse(true);">
                            <span class="fa fa-undo"></span>
                        </span>
                        <span class="btn btn-primary" ng-disabled="response.$invalid" ng-click="sendResponse(true)">
                             <span class="fa fa-paper-plane-o"></span>
                        </span>
                    </div>
                    <div class="loader-content" ng-if="selectedComment.sending"><div class="loader"></div></div>
                </div>
            </form>

        </div>

    </div>
    <div class="loader-content" ng-if="c.deleting"><div class="loader"></div></div>
</div>
<div class="text-center" ng-show="pagination.show">
    <ul uib-pagination ng-change="pageChanged()"
        max-size="5"
        boundary-links="true"
        total-items="pagination.total"
        ng-model="pagination.currentPage"
        class="pagination-sm"
        previous-text="&lsaquo;"
        next-text="&rsaquo;"
        first-text="&laquo;"
        last-text="&raquo;"></ul>
</div>
<br>