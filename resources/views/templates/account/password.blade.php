<div class="modal fade" id="pwdChangedModal" tabindex="-1" role="dialog" aria-labelledby="pwdChangedModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body text-center" id="modal-body">
                <div>{{trans('personal.ALERTS.pwd_changed')}}</div>
                <div class="alert alert-warning">{{trans('personal.ALERTS.page_jump')}}</div>
                <div>{{trans('personal.MESSAGES.page_jump')}}</div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-outline-secondary" type="button" ng-click="cancelPageJump()" >
                    {{trans("project.BUTTONS.cancel")}}
                </button>
                <button class="btn btn-success" type="button" ng-click="confirmPageJump()" >
                    {{trans("project.BUTTONS.confirm")}}
                </button>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-3"></div>
    <form name="pwdform" class="col-md-6 my-3">
        {{csrf_field()}}
        <div class="form-group input input--isao">
            <input type="password" class="input__field input__field--isao" id="oldPwd"
                   name="old"
                   ng-model="password.old"
                   required />
            <label class="input__label input__label--isao" for="oldPwd" data-content="{{trans('passwords.old_password')}}">
                <span class="input__label-content input__label-content--isao">{{trans('passwords.old_password')}}</span>
            </label>
            <div class="error" role="alert" ng-class="{'visible':pwdform.old.$touched || pwdform.$submitted}">
                <span ng-show="pwdform.old.$error.required || error.password">{{trans('passwords.old_password')}}</span>
            </div>
        </div>
        <div class="form-group input input--isao">
            <input type="password" class="input__field input__field--isao" id="newpwd"
                   name="password"
                   ng-model="password.password" ng-pattern="regex"
                   ng-minlength="6" ng-maxlength="16"
                   required />
            <label class="input__label input__label--isao" for="newpwd" data-content="{{trans('passwords.new_password')}}">
                <span class="input__label-content input__label-content--isao">
                    <span ng-show="!pwdform.password.$error.required && !pwdform.password.$error.minlength && !pwdform.password.$error.maxlength && !pwdform.password.$error.pattern">
                        {{trans('passwords.new_password')}}
                    </span>
                    <span class="text-danger"  ng-show="pwdform.password.$error.required || pwdform.password.$error.minlength || pwdform.password.$error.maxlength || pwdform.password.$error.pattern">
                        {{trans("passwords.password")}}
                    </span>
                    <span class="text-danger" ng-show="error.newpwd">{{trans("passwords.password_same")}}</span>
                </span>
            </label>
        </div>
        <div class="form-group input input--isao">
            <input type="password" class="input__field input__field--isao" id="password_confirmation"
                   name="password_confirmation"
                   ng-model="password.password_confirmation" pw-check="newpwd"
                   required />
            <label class="input__label input__label--isao" for="password_confirmation" data-content="{{trans('passwords.password_confirmation')}}">
                <span class="input__label-content input__label-content--isao">
                    <span ng-show="!pwdform.password_confirmation.$error.required && !pwdform.password_confirmation.$error.pwmatch">{{trans('passwords.password_confirmation')}}</span>
                     <span class="text-danger" ng-show="pwdform.password_confirmation.$error.required">{{trans("passwords.password")}}</span>
                    <span class="text-danger" ng-show="pwdform.password_confirmation.$error.pwmatch">{{trans("passwords.password_different")}}</span>
                </span>
            </label>
        </div>
        <div class="text-right">
            <a href="{{url()->previous()}}" class="btn btn-outline-danger">{{trans('layout.BUTTONS.cancel')}}</a>
            <div class="btn btn-primary" ng-disabled="pwdform.$invalid" ng-click="changePwd(password)">{{trans('layout.BUTTONS.submit')}}</div>
        </div>
    </form>
</div>
