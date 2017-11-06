<link href="/css/message.css" rel="stylesheet" />
<form id="mycomment" name="mycomment" class="paragraph" novalidate>
    <div style="position: relative">
        <textarea ng-model="newComment.message" name="message"
              rows="3"
              placeholder="<%'comment.placeholder' | translate:'{ min: 15, max:800 }' %>"
              style="width:100%"
              ng-minlength="15" ng-maxlength="800" required></textarea>
        <div class="error visible" role="alert">
            <span ng-show="mycomment.message.$error.required && mycomment.$submitted"
              translate="project.ERRORS.require.comment"></span>
            <span ng-show="mycomment.message.$error.minlength"
                  translate="project.ERRORS.minlength.comment"
                  translate-values="{val:'15'}"></span>
            <span ng-show="mycomment.message.$error.maxlength"
                  translate="project.ERRORS.maxlength.comment"
                  translate-values="{val:'800'}"></span>
        </div>
        <div class="paragraph text-right">
            <div class="btn btn-default" ng-click="cancelResponse(false)">
                <span class="fa fa-undo"></span>
            </div>
            <div class="btn btn-primary" ng-disabled="mycomment.$invalid || !newComment.message" ng-click="sendResponse(false)">
                <span class="fa fa-paper-plane-o"></span>
            </div>
        </div>
    </div>
    <div class="loader-content" ng-if="selectedComment.sending"><div class="loader"></div></div>
</form>
<br/>
<div style="position: relative" ng-repeat="c in comments">
    <div class="row">
        <div class="col-md-1 flex-top text-center">
            <a class="text-center margin-top-sm" href="/profile/<%c.user_id%>">
                <img class="center img-circle img-responsive" src="/context/avatars/<%c.user_id%>.small.jpg" />
            </a>
            <div ng-if="c.mine" class="font-xl">
                <span class="fa fa-caret-up" class="text-default"></span>
            </div>
            <div  ng-if="!c.mine" class="font-xl"  id="comment_info_<%c.id%>" ng-click="supportComment(c)">
                <a href="javascript:void(0)" ng-if="c.supported" ng-disabled="c.supporting" class="fa fa-caret-up text-info"></a>
                <a href="javascript:void(0)"  ng-if="!c.supported" ng-disabled="c.supporting" class="fa fa-caret-up text-primary"></a>
            </div>
            <div class="counter" ng-if="c.supports_cnt > 0" ng-bind="c.supports_cnt"></div>
            <div ng-if="c.newest"><aside class="sheer" translate="NEW"></aside></div>
        </div>
        <div class="col-md-11 comment-container">
            <div class="flex-rows">
                <span class="font-sm text-info" ng-if="c.parent">
                   <span class="text-muted small" translate="comment.subject"></span>&nbsp;
                    <a href="/profile/<%c.parent.user_id%>" ng-bind="c.parent.username"></a>
                </span>
            </div>
            <div id="comment_message_<%c.id%>" class="comment_message">
                <div ng-if="c.parent">
                    <blockquote ng-if="!c.parent.id" class="text-danger">
                        <span  translate="project.ERRORS.deleted"></span>
                    </blockquote>
                    <blockquote class="text-default small" ng-if="c.parent.id" ng-init="c.parent_showall = c.parent_message.length <= 40">
                        <span ng-bind="c.parent.message|limitTo:40"></span>
                        <span ng-if="c.parent.message.length > 40" ng-switch="c.parent_showall">
                            <span class="btn btn-link" ng-switch-when="false" ng-click="c.parent.showall=true">......</span>
                            <span ng-switch-when="true" >
                                <span ng-bind="c.parent.message|limitTo:c.parent.message.length:40"></span>
                                <span class="btn btn-link pull-right fa fa-caret-up" ng-click="c.parent_showall=false"></span>
                            </span>
                        </span>
                    </blockquote>
                </div>
                <div ng-bind="c.message" class="body"></div>
            </div>
            <form name="response" class="margin-top-sm" novalidate ng-if="selectedComment.parent && selectedComment.parent.id == c.id">
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
                        <span ng-show="response.message.$error.maxlength" translate="project.ERRORS.maxlength.comment" translate-values="{value:'1000'}">
                        </span>
                     </div>
                    <div class="paragraph text-right">
                        <span class="btn" ng-click="cancelResponse(true);">
                            <span class="fa fa-undo"></span>
                        </span>
                                   &nbsp;&nbsp;
                         <span class="btn btn-primary" ng-disabled="response.$invalid" ng-click="sendResponse(true)">
                             <span class="fa fa-paper-plane-o"></span>
                        </span>
                    </div>
                    <div class="loader-content" ng-if="selectedComment.sending"><div class="loader"></div></div>
                </div>
            </form>
            <div class="text-right text-muted small" ng-bind="c.created_at|limitTo:16"></div>
            <div class="hidden-bar flex-rows" ng-class="{'br': !$last}">
                <div>
                    <a href="/profile/<%c.user_id%>" class="title" ng-bind="c.username"></a>
                </div>
                <div class=" btn" ng-if="c.mine" ng-click="deleteComment(c)">
                    <span class="text-danger fa fa-trash"></span>
                </div>
                <div ng-if="!c.mine" class="btn btn-link" ng-disabled="selectedComment.parent && selectedComment.parent.id == c.id" ng-click="openResponse(c)">
                    <sapn translate="comment.reply"></sapn>
                </div>
            </div>
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