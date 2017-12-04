<link rel="stylesheet" href="/css/angular-timeline-bootstrap.css" />
<link rel="stylesheet" href="/css/angular-timeline-animations.css" />
<link rel="stylesheet" href="/css/timeline.css">
@if($project->admin || $role)
    <div class="flex-rows">
        @if($project->admin)
            <div class="timeline-filter">
                <div class="timeline-btn" id="timeline-all" ng-click="setTimelineFilter('all')">
                    <span class="fa fa-bars" aria-hidden="true"></span>
                </div>
                <div class="timeline-btn" id="timeline-budget" ng-click="setTimelineFilter('budget', 'b')">
                    <span class="fa fa-jpy" aria-hidden="true"></span>
                </div>
                <div class="timeline-btn" id="timeline-team" ng-click="setTimelineFilter('team', 't', 'child', 'q', 'user-secret')">
                    <span class="fa fa-child" aria-hidden="true"></span>
                </div>
                <div class="timeline-btn" id="timeline-report" ng-click="setTimelineFilter('report', 'r', 'book', 's', 'bookmark')">
                    <span class="fa fa-book" aria-hidden="true"></span>
                </div>
                <div class="timeline-btn" id="timeline-other" ng-click="setTimelineFilter('other', 'o')">
                    <span class="fa fa-pencil" aria-hidden="true"></span>
                </div>
            </div>
            @else
            <div>&nbsp;</div>
        @endif
        @if($project->active)
        <div>
            <a href="/report/{{$project->id}}" class="btn btn-text-info">
                <span translate="report.add"></span>
            </a>
        </div>
            @endif
    </div>
@endif
<div class="timeline" ng-if="events">
    <div ng-repeat="mevent in events" id="timeline-<%$index%>">
        <div class="timeline-header" ng-click="slideUp($index)">
            <div>
                <div class="text-success" translate="month.<%mevent.month | split:'-':1%>"></div>
                <div><%mevent.month | split:'-':0%></div>
            </div>
        </div>
        <div ng-repeat="devent in mevent.events">
            <ul class="timeline-events">
                <li ng-repeat="event in devent.events| filter:{type:timelineFilter} | orderBy:'created_at':true"
                    ng-class="{'text-through':event.deleted, 'team':event.type=='t' || event.type=='q', 'report':event.type=='r', 'right':$index%2==1}">
                    <div ng-if="event.type != 'r' && event.type != 't'" ng-class="{'quit':event.type=='q'}">
                        <div class="delete-btn" ng-click="deleteEvent(event, month)" ng-if="event.type != 'q'">
                            <span class="fa fa-times"></span>
                        </div>
                        <div class="timeline-title">
                            <div class="small" ng-if="$index%2">
                                <span class="text-chocolate fa" ng-class="{'fa-jpy':event.type=='m' || event.type=='b', 'fa-bookmark':event.type=='s'}"></span>
                                <span ng-bind="event.created_date"></span>&nbsp;<span ng-bind="event.created_time"></span>
                            </div>
                            <div class="small" ng-if="!$index%2">
                                <span ng-bind="event.created_date"></span>&nbsp;<span ng-bind="event.created_time"></span>
                                <span class="text-chocolate fa" ng-class="{'fa-jpy':event.type=='m' || event.type=='b', 'fa-bookmark':event.type=='s'}"></span>
                            </div>
                            <div  ng-bind-html="event.title"></div>
                        </div>
                        <div class="timeline-conent" ng-bind-html="event.content"></div>
                        <div ng-if="event.type=='m'" class="text-info"><span ng-bind="event.username"></span></div>
                        <ul class="list-unstyled">
                            <li ng-repeat="c in event.changements" ng-class="{'text-through':c.deleted}">
                                <div><label ng-bind="c.title"></label></div>
                                <div ng-bind-html="c.content"></div>
                            </li>
                        </ul>
                        <div class="loader-content" ng-if="event.deleting"><div class="loader"></div> </div>
                    </div>
                    <div ng-if="event.type == 't'">
                        <div class="timeline-title">
                            <div><span ng-bind="mevent.month"></span>-<span ng-bind="devent.day"></span></div>
                            <div ng-repeat="p in event.events">
                                <img src="/context/avatars/<%p.user_id%>.small.jpg" />
                                <a class="title" href="/profile/<%p.user_id%>" ng-bind="p.username" target="_blank"></a>
                                <span translate="event.team-add"></span>
                                <span ng-repeat="r in p.roles"><label ng-bind="r"></label><span ng-if="!$last">,&nbsp;</span></span>
                            </div>
                        </div>
                        <div class="timeline-conent" ng-bind-html="event.content"></div>
                    </div>
                    <div ng-if="event.type == 'r'" class="letter">
                        <div ng-repeat="report in event.events">
                            <div class="timeline-title">
                                <div class="h5 text-info">
                                    <a class="header" href="/reports/<%report.id%>" ng-bind="report.title" target="_blank"></a>
                                    <div ng-if="report.user_id == user.id">
                                        <a href="/reports/<%report.id%>" target="_blank"><span class="fa fa-edit"></span></a>
                                    </div>
                                </div>
                                <div>
                                    <img src="/context/avatars/<%report.user_id%>.small.jpg" />
                                    <a class="link active" href="/profile/<%report.user_id%>" target="_blank" ng-bind="report.username"></a>
                                    <div class="tags">
                                        &nbsp;<aside ng-repeat="role in report.roles" class="diamond text-center"><span ng-bind="role"></span></aside>
                                    </div>
                                </div>
                                <small class="text-muted" ng-bind="report.created_at | limitTo:16"></small>
                            </div>
                            <div class="timeline-conent">
                                <div ng-bind-html="report.synopsis"></div>
                            </div>
                            <div class="flex-rows">
                                <div class="timeline-tags">
                            <span class="text-info">
                                <?php echo file_get_contents(public_path("/images/icons/comments.svg")); ?>
                                <span ng-if="report.comments_cnt" ng-bind="report.comments_cnt"></span>
                            </span>&nbsp;&nbsp;&nbsp;
                                    <span class="text-success">
                                <?php echo file_get_contents(public_path("/images/icons/favorite.svg")); ?>
                                        <span ng-if="report.lovers_cnt" ng-bind="report.lovers_cnt"></span>
                            </span>
                                </div>
                                <div>
                                    <a href="/reports/<%report.id%>" target="_blank" translate="project.TAGS.more"></a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
            </ul>
        </div>

    </div>
    <div class="timeline-footer flex-horizontal">
        <div>
            <div>{{str_limit($project->start_at, 10, "")}}</div>
            <div translate="event.start"></div>
        </div>
    </div>
</div>