<label class="col-sm-2 control-label">Answers*</label>
<div class="col-sm-10">
    <div class="p-xs panel panel-default answer-box-panel" ng-repeat="masterAnswer in form.masterAnswers ">
        <div class="checkbox">
            <label>
                <input type="checkbox" ng-model="form.masterAnswers[$index].checked" ng-true-value="true"
                       ng-false-value="false" ng-change="validateAnswersCount(form.masterAnswers[$index])" ng-disabled="!isOnEdit"> @{{masterAnswer.name}}
            </label>
            <span>
                <select name="selectedPlan" ng-model="form.masterAnswers[$index].answerOptionId"
                        ng-options="item.id as item.name for item in form.masterAnswers[$index].questionAnswerValues"  ng-disabled="!isOnEdit">
                </select>
            </span>
        </div>
    </div>
</div>