
<div class="panel-body">

    <?php $questionId = 0; $questionAnswerId = 0; $parentQuestionId = 0; $status = false; $isMandatory = false; $isDraft= 1; $isArchive = 0;?>
    @if(isset($question))
        <?php
            $questionId = $question->id;
            $status = $question->status;
            $isMandatory = $question->is_mandatory;

        ?>
    @endif

    @if(isset($answer))
        <?php $questionAnswerId = $answer->id?>
        <?php $parentQuestionId = $answer->question_id?>
    @endif

    @if(isset($supperParentQuestion))
        <?php $isDraft = $supperParentQuestion->is_draft;?>
        <?php $isArchive = $supperParentQuestion->is_archive;?>
    @endif


    <form role="form"  class="form-horizontal" id="form_{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}}">
        <div class="form-group">
            <label class="col-sm-2 control-label">Visibility</label>
            <div class="col-sm-6">
                @if($questionId == 0 || $status)
                    <div class="radio col-sm-3"><label> <input type="radio" name="visibility" class="i-checks" value="1" checked disabled> Active </label></div>
                    <div class="radio col-sm-3"><label> <input type="radio" name="visibility" class="i-checks" value="0" disabled> Inactive </label></div>
                @else
                    <div class="radio col-sm-3"><label> <input type="radio" name="visibility" class="i-checks" value="1" disabled> Active </label></div>
                    <div class="radio col-sm-3"><label> <input type="radio" name="visibility" class="i-checks" value="0" checked disabled> Inactive </label></div>
                @endif

            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label">Mandatory</label>
            <div class="col-sm-6">
                @if($questionId == 0 || $isMandatory)
                    <div class="radio col-sm-3"><label> <input type="radio" name="mandatory" class="i-checks" value="1" checked disabled> Yes </label></div>
                    <div class="radio col-sm-3"><label> <input type="radio" name="mandatory" class="i-checks" value="0" disabled> No </label></div>
                @else
                    <div class="radio col-sm-3"><label> <input type="radio" name="mandatory" class="i-checks" value="1" checked disabled> Yes </label></div>
                    <div class="radio col-sm-3"><label> <input type="radio" name="mandatory" class="i-checks" value="0" disabled> No </label></div>
                @endif

            </div>
        </div>



        <div class="form-group">
            <label class="col-sm-2 control-label required-field">Question* </label>
            <div class="col-sm-10">
                <textarea readonly="true" class="form-control" rows="1" placeholder="Question"  name="question" id="question" required>{{isset($question) ? $question->question : ''}}</textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Question Topic*</label>
            <div class="col-sm-10">
                <textarea readonly="true" class="form-control" rows="1" placeholder="Explanation" name="explanation" id="explanation" required>{{isset($question) ? $question->explanation : ''}}</textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Action Items*</label>
            <input type="hidden" name="actionItemValidation">
            <div class="col-sm-10">
                <table id="addActionItemTable_{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}}" class="col-sm-11 pull-left">
                    <tbody>
                    @foreach($actionItems as $index => $item)
                        <tr>
                            <td class="questionTableTd" style="display: none">{{$index +1}}</td>
                            <td class="questionTableTdInput"><input disabled class="action_item_data marginButtom1 col-sm-11 form-control" type="text" value="{{$item->name}}"></td>
                        </tr>
                    @endforeach

                    @if(count($actionItems) == 0)
                        <tr>
                            <td class="questionTableTd" style="display: none">1</td>
                            <td class="questionTableTdInput"><input disabled class="action_item_data marginButtom1 col-sm-11 form-control" type="text"></td>
                        </tr>
                    @endif

                    </tbody>
                </table>
            </div>
        </div>

        {{--<div class="form-group">--}}
            {{--<div class="col-sm-6 pull-right">--}}
                {{--<input type="button" class="btn w-xs btn-warning2 btnAddNewActionItem" value="Add New Action Item" table-id="addActionItemTable_{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}}">--}}
            {{--</div>--}}
        {{--</div>--}}


        <div class="form-group">
            {{--@include('includes.question.ParentAnswer')--}}
            @include('includes.question.ChildAnswerView')

        </div>


    </form>

    <br />

</div>

<div>
    <br/>
</div>