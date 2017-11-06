
 <div class="project-item" ng-class="{'tri-first-item':!($index%3),'tri-middle-item':!(($index - 1)%3), 'tri-last-item':!(($index + 1)%3), 'duo-last-item':($index%2), 'duo-first-item':!($index%2)}">
        <div class="project-content">
            <a class="project-poster" href="/project/<%p.id%>" style="background-image:url('/context/projects/<%p.id%>.thumb.jpg');">
                <div></div>
            </a>
            <div class="project-info-panel">
                <h6>
                    <a class="text-info"  href="/project/<%p.id%>" ng-bind="p.title"></a>
                </h6>
                <div class="description">
                    <span ng-bind="p.synopsis"></span>
                </div>
                <div class="flex-rows margin-top-sm">
                    <div class="text-default small" style="display: inline-block" translate="project.TAGS.update" translate-values="{date:p.updated_at}"></div>
                    <div class="clip" ng-class="{'text-important':p.recommendation, 'text-chocolate': p.active == 0, 'text-success':p.active == 3}"><span ng-bind="p.genre_name"></span></div>
                </div>
                <div class="progress-table">
                    <div>
                        <div class="margin-bottom-xs">
                            <a class="title" ng-if="p.username" href="/profile/<%p.user_id%>" ng-bind="p.username"></a>
                            <a class="title" ng-if="!p.username" href="/profile/<%user.id%>" ng-bind="user.username"></a>
                        </div>
                        <div class="text-chocolate small margin-bottom-xs">
                            <i class="fa fa-clock-o" aria-hidden="true"></i>
                            <span translate="project.Duration" translate-values="{min:p.duration}"></span>
                        </div>
                        <div class="flex-rows small">
                                <span class="text-default">
                                    <span class="glyphicon"><?php echo file_get_contents(public_path("/images/icons/location.svg")); ?></span>
                                        <span ng-bind="p.city_name"></span> (<span ng-bind="p.sortname"></span>)
                                </span>
                            <span ng-if="p.comments_cnt" class="text-default">
                                    <span class="glyphicon"><?php echo file_get_contents(public_path("/images/icons/comments.svg")); ?></span>
                                    <a href="/project/<%p.id%>?tab=3" ng-bind="p.comments_cnt"></a>
                                </span>
                            <span ng-if="p.followers_cnt" class="text-default">
                                    <span class="glyphicon fa fa-bookmark<%p.myfollow ? '' : '-o' %>" ></span>
                                    <span ng-bind="p.followers_cnt"></span>
                                </span>
                        </div>
                    </div>
                    <div class="progress-content">
                        <div class="progress-text"
                             ng-class="{'text-primary': p.daterest > 7, 'text-warning' : p.daterest > 3 && p.daterest < 8, 'text-danger': p.daterest < 4}"
                             translate="project.TAGS.rest" translate-values="{days:p.daterest}">
                        </div>
                        <round-progress
                                max="max"
                                current="(p.daterest*100/p.datediff)"
                                color="<% (p.daterest < 3) ? '#993e25' : (p.daterest < 7 ? 'ae6892' : '#293a4f') %>"
                                bgcolor="#e6e6e6"
                                radius="100"
                                stroke="9"
                                semi="false"
                                rounded="false"
                                clockwise="true"
                                responsive="true"
                                duration="800"
                                animation="easeOutCubic"
                                animation-delay="0"></round-progress>
                    </div>
                </div>
            </div>
        </div>
    </div>
