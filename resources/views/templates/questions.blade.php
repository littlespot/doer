<div ng-repeat="q in questions" class="margin-top-md">
    <div class="row">
        <div class="col-md-1 flex-cols text-center">
            <div class="margin-top-sm ">
                <a class="text-center margin-top-sm" href="/profile/<%q.user_id%>">
                    <img class="center img-circle img-responsive" src="/context/avatars/<%q.user_id%>.small.jpg" />
                </a>
                <div ng-if="q.mine" class="text-info">
                    <span class="fa" ng-class="{'fa-bookmark-o':!q.followers_cnt, 'fa-bookmark':q.followers_cnt}"></span>
                </div>
                <div ng-if="!q.mine" id="favorite_<%q.id%>" class="text-warning" ng-click="followQuestion(q)">
                    <span class="btn btn-sm fa"  ng-disabled="q.following"  ng-class="{'fa-bookmark-o':!q.myfollow, 'fa-bookmark':q.myfollow}"></span>
                </div>
                <div class="counter" ng-class="{'text-info':q.mine, 'text-warning':!q.mine}"
                     ng-if="q.followers_cnt > 0" ng-bind="q.followers_cnt"></div>
            </div>
            <div><aside ng-if="q.newest" class="sheer" translate="NEW"></aside></div>
        </div>
        <div class="col-md-11">
            <div class="comment-container">
                <div class="flex-rows">
                    <a class="text-primary" href="/questions/<%q.id%>" target="_blank">
                        <label ng-bind="q.subject"></label>
                    </a>
                    <div ng-if="q.cnt == 0">
                        <span ng-if="q.mine" class="text-default">{{trans('project.QUESTION.answer_none')}}</span>
                        <span ng-if="!q.mine" class="text-danger">{{trans("project.QUESTION.answer_first")}}</span>
                    </div>
                    <div ng-if="q.cnt > 0" class="text-primary" translate="project.AnswerCnt" translate-values="{cnt:q.cnt}"></div>
                </div>
                <div class="margin-top-sm" ng-bind-html="q.content"></div>
                <div class="text-right">
                    <span class="small text-muted" ng-bind="q.created_at | limitTo:16"></span>
                </div>
                <div class="hidden-bar flex-rows" ng-class="{'br':!$last}">
                    <div>
                        <a class="title" ng-if="q.username" href="/profile/<%q.user_id%>" target="_blank">
                            <span ng-bind="q.username"></span>
                        </a>
                        <span class="text-info" ng-if="!q.username" ng-bind="profile.username"></span>
                    </div>
                    <div class="btn"  ng-if="q.mine && !q.cnt" ng-click="deleteQuestion(q)" >
                        <span class="text-danger fa fa-trash"></span>
                    </div>
                    <div class="btn" disabled  ng-if="q.mine && q.cnt" title="<%'project.MESSAGES.undelable' | translate %>" >
                        <span class="text-danger fa fa-trash"></span>
                    </div>
                    <a class="btn" ng-if="!q.mine" href="/questions/<%q.id%>?answer=1" title="{{trans("project.QUESTION.answer_wait")}}" >
                        <span class="text-danger fa fa-hand-spock-o"></span>
                    </a>
                </div>
                <div class="loader-content" ng-if="q.deleting"><div class="loader"></div> </div>
            </div>
        </div>
    </div>
</div>
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