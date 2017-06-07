<div class="panel-body">

    <?php $questionId = 0; $questionAnswerId = 0; $parentQuestionId = 0; $status = false; $isMandatory = false; $isDraft = 1; $isArchive = 0;?>
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


    <form role="form" class="form-horizontal" id="form_{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}}">
        <div class="form-group">
            <label class="col-sm-2 control-label">Visibility</label>
            <div class="col-sm-6">
                @if($questionId == 0 || $status)
                    <div class="radio col-sm-3"><label> <input type="radio" name="visibility" class="i-checks" value="1"
                                                               checked> Active </label></div>
                    <div class="radio col-sm-3"><label> <input type="radio" name="visibility" class="i-checks"
                                                               value="0"> Inactive </label></div>
                @else
                    <div class="radio col-sm-3"><label> <input type="radio" name="visibility" class="i-checks"
                                                               value="1"> Active </label></div>
                    <div class="radio col-sm-3"><label> <input type="radio" name="visibility" class="i-checks" value="0"
                                                               checked> Inactive </label></div>
                @endif

            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label">Mandatory</label>
            <div class="col-sm-6">
                @if($questionId == 0 || $isMandatory)
                    <div class="radio col-sm-3"><label> <input type="radio" name="mandatory" class="i-checks" value="1"
                                                               checked> Yes </label></div>
                    <div class="radio col-sm-3"><label> <input type="radio" name="mandatory" class="i-checks" value="0">
                            No </label></div>
                @else
                    <div class="radio col-sm-3"><label> <input type="radio" name="mandatory" class="i-checks" value="1"
                                                               checked> Yes </label></div>
                    <div class="radio col-sm-3"><label> <input type="radio" name="mandatory" class="i-checks" value="0">
                            No </label></div>
                @endif

            </div>
        </div>

        <div class="form-group law-section-child">
            @include('includes.question.LawListEditChild')
        </div>
        <div class="form-group country-section-child">
            @include('includes.question.CountryListEdit')
        </div>

        <?php
        $stateEdit = 'hidden';
        if ($supperParentQuestion->law == 2) {
            $stateEdit = '';
        }
        ?>

        <div class="form-group state-section-child {{$stateEdit}}">
            @include('includes.question.StateListEdit')
        </div>

        <?php
        $cityEdit = 'hidden';

        if ($parentLaw == 3 || (isset($question->law) && $question->law == 3)) {
            $cityEdit = '';
        }
        ?>

        <div class="form-group city-section-child {{$cityEdit }}">
            @include('includes.question.CityListEditChild')
        </div>

        <?php
        $licenseEdit = 'hidden';

        if (($parentLaw == 3 || (isset($question->law) && $question->law == 3)) || ($parentLaw == 2 || (isset($question->law) && $question->law == 2))) {
            $licenseEdit = '';
        }
        ?>
        <div class="form-group license-section-child {{$licenseEdit}}">
            @include('includes.question.LicenceListEditChild')
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label required-field">Question* </label>
            <div class="col-sm-10">
                <input class="form-control" type="text" placeholder="Question" name="question" id="question"
                       value="{{isset($question) ? $question->question : ''}}" required>
                <input type="hidden" name="law" id="sub_question_law" value="{{$supperParentQuestion->law}}">
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Question Topic*</label>
            <div class="col-sm-10">
                <textarea class="form-control" rows="1" placeholder="Explanation" name="explanation" id="explanation"
                          required>{{isset($question) ? $question->explanation : ''}}</textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label">Action Items*</label>
            <input type="hidden" name="actionItemValidation">
            <div class="col-sm-10">
                <table id="addActionItemTable_{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}}"
                       class="col-sm-11 pull-left">
                    <tbody>
                    @foreach($actionItems as $index => $item)
                        <tr>
                            <td class="questionTableTd" style="display: none">{{$index +1}}</td>
                            <td class="questionTableTdInput"><input
                                        class="action_item_data marginButtom1 col-sm-11 form-control" type="text"
                                        value="{{$item->name}}"></td>

                            @if($index == 0)
                                <td class="questionTableTd">
                                    <button class="btn btn-danger btn-circle marginLeft5 marginTopMin6 btn_delete_item pull-left"
                                            type="button" item-number="1"><i class="fa fa-times"></i></button>
                                    <button class="btn btn-success btn-circle marginLeft5 marginTopMin6 btnAddNewActionItem"
                                            table-id="addActionItemTable_{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}}"
                                            type="button"><i class="fa fa-plus"></i></button>
                                </td>
                            @else
                                <td class="questionTableTd">
                                    <button class="btn btn-danger btn-circle marginLeft5 marginTopMin6 btn_delete_item pull-left"
                                            type="button"><i class="fa fa-times"></i></button>
                                </td>
                            @endif
                        </tr>
                    @endforeach

                    @if(count($actionItems) == 0)
                        <tr>
                            <td class="questionTableTd" style="display: none">1</td>
                            <td class="questionTableTdInput"><input
                                        class="action_item_data marginButtom1 col-sm-11 form-control" type="text"></td>
                            <td class="questionTableTd">
                                <button class="btn btn-danger btn-circle marginLeft5 marginTopMin6 btn_delete_item pull-left"
                                        type="button" item-number="1"><i class="fa fa-times"></i></button>
                                <button class="btn btn-success btn-circle marginLeft5 marginTopMin6 btnAddNewActionItem"
                                        table-id="addActionItemTable_{{$questionAnswerId}}_{{$parentQuestionId}}_{{$questionId}}"
                                        type="button"><i class="fa fa-plus"></i></button>
                            </td>
                        </tr>
                    @endif

                    </tbody>
                </table>
            </div>
        </div>

        <?php
        if ($supperParentQuestion->law == 1) {
            $auditEdit = 'hidden';
        } else {
            $auditEdit = '';
        }
        ?>

        <div class="form-group {{$auditEdit}}">
            @include('includes.question.AuditTypesListEdit')
        </div>

        <?php
        if ($supperParentQuestion->law == 1) {
            $classificationEdit = 'hidden';
        } else {
            $classificationEdit = '';
        }
        ?>
        <div class="form-group {{$classificationEdit}}">
            @include('includes.question.ClassificationListEdit')
        </div>

        <div class="form-group {{$classificationEdit}}">
            @include('includes.question.ClassificationListNotReqListEdit')
        </div>

        <div class="form-group">
            @include('includes.question.CitationListChild')
        </div>

        <div class="form-group">
            @include('includes.question.ChildAnswer')
        </div>


        @if($isDraft == 1 && $isArchive == 0)
            <div>
                @if($questionId == 0)
                    <button class="btn w-xs btn-success save_child_question pull-right" type="button"
                            is-child-saved="false"><strong>Save</strong></button>
                @else
                    <button class="btn w-xs btn-info save_child_question pull-right" type="button"
                            is-child-saved="false"><strong>Update</strong></button>
                @endif
            </div>
        @endif

    </form>

    <br/>

</div>

<div>
    <br/>
</div>