@extends('layout.dashbord')

@section('content')
        <div class="normalheader transition animated fadeIn" ng-controller="questionIndex">
        <div class="hpanel">
            <div class="panel-body">
                <a class="small-header-action" href="">
                    <div class="clip-header">
                        <i class="fa fa-arrow-up"></i>
                    </div>
                </a>
                <input id="sort" type="hidden" data-sort="@{{questionSearch.sort}}" data-sortType="@{{questionSearch.sortType}}">

                {{--<div class="pull-right">--}}
                    {{--<a href="/question/create" class="btn btn-success btn-block">New Question </a>--}}
                {{--</div>--}}
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group text-left">
                            <a href="/question/create" class="btn btn-info" id="new-user-model">Add New Question</a>
                            <a href="/question/export" class="btn btn-info" id="export-questions" target="_blank">Export</a>
                            <a href="/question/export_csv" class="btn btn-info" id="export-questions-csv" target="_blank">Export to CSV</a>
                        </div>
                    </div>
                </div>



                <div class="row">
                    <form id="eventForm" class="form-horizontal">
                        <div class="col-lg-12">
                            <div class="col-md-4">
                                <div class="form-group" style="margin-right: 0 !important;">
                                    <input type="text"class="form-control" id="questionName" placeholder="Search by Questions" ng-model="questionSearch.questionName">
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <select class="form-control padding0" name="keywords" id="questionKeywords" multiple="multiple" placeholder="Search by Keywords" style="height: auto;" ng-model="questionSearch.keywords">
                                        @foreach($masterKeywords as $option)
                                            <option value="{{$option->id}}"> {{$option->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <select class="form-control" name="status" id="status" ng-model="questionSearch.status">
                                            <option value="">Status</option>
                                            <option value="1">Active</option>
                                            <option value="0">Inactive</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-2">
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <select class="form-control" name="display" id="display" ng-model="questionSearch.display">
                                            <option value="">Display</option>
                                            <option value="0">Published</option>
                                            <option value="1">Draft</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <div class="col-xs-12">
                                        <a name="searchQuestions" id="searchQuestions" class="btn btn-default" ng-click="getSearchedPosts()"><i class="fa fa-search"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>


                <div class="row">
                    <div class="col-lg-12">
                        <input id="currentPage" type="hidden" data-currentPage="@{{currentPage}}">
                        <div class="dataTables_length">
                            {{--<label>Show <select name="question-detail-table_length" aria-controls="question-detail-table" class=""  ng-model="entries" ng-change="getQuestions()">--}}
                                {{--<option ng-selected="entries==10" value="10">10</option>--}}
                                {{--<option ng-selected="entries==25" value="25">25</option>--}}
                                {{--<option ng-selected="entries==50" value="50">50</option>--}}
                                {{--<option ng-selected="entries==100" value="100">100</option>--}}
                            {{--</select> entries</label>--}}
                            <label>Show <select name="question-detail-table_length" id="entries" aria-controls="question-detail-table" ng-model="entries" ng-change="getQuestions()" ng-options="e for e in entryOptions">
                                </select> entries</label>
                        </div>

                        <div class="row">
                            <div class="col-lg-12 table-responsive" ng-if="questionListLoaded">

                                <script type="text/ng-template" id="tree_node">
                                    <tr id='@{{node.id}}' tt-node is-branch="node.count!=0">
                                        <td><span ng-bind="node.level"></span></td>
                                        <td><span ng-bind="node.name"></span></td>
                                        <td ng-bind="node.createdBy"></td>
                                        <td ng-bind="node.updatedBy"></td>
                                        <td ng-bind="node.createdAt"></td>
                                        <td><span class="publish" ng-bind-html="node.display | html"></span></td>
                                        <td> <span ng-bind-html="node.action | html"></span></td>
                                    </tr>
                                </script>

                                <table class="table table-condensed table-hover no-footer dataTable" tt-table tt-params="expanded_params" id="question-table">
                                    <thead>
                                    <tr>
                                        <th class="width-id"><a href="" ng-click="sortReverseId?sortQuestions('id','asc'):sortQuestions('id','desc')">#
                                                <span class="tb-sort-icons">
                                                       <i ng-show="sortType == 'id' && !sortReverseId" class="fa fa-caret-up"></i>
                                                       <i ng-show="sortType == 'id' && sortReverseId" class="fa fa-caret-down"></i>
                                                       <i ng-show="sortType != 'id'" class="fa fa-sort"></i>
                                        </span></a>
                                        </th>
                                        <th class="width-1"><a href="" ng-click="sortReverseName?sortQuestions('question','desc'):sortQuestions('question','asc')">Question
                                                <span class="tb-sort-icons">
                                                       <i ng-show="sortType == 'question' && !sortReverseName" class="fa fa-caret-up"></i>
                                                       <i ng-show="sortType == 'question' && sortReverseName" class="fa fa-caret-down"></i>
                                                       <i ng-show="sortType != 'question'" class="fa fa-sort"></i>
                                        </span></a>
                                        </th>
                                        <th  class="width-2"><a href="" ng-click="sortReverseCreated?sortQuestions('created_by','asc'):sortQuestions('created_by','desc')">Created By
                                                <span class="tb-sort-icons">
                                                       <i ng-show="sortType == 'created_by' && !sortReverseCreated" class="fa fa-caret-down"></i>
                                                       <i ng-show="sortType == 'created_by' && sortReverseCreated" class="fa fa-caret-up"></i>
                                                       <i ng-show="sortType != 'created_by'" class="fa fa-sort"></i>
                                        </span></a></th>
                                        <th  class="width-3"><a href="" ng-click="sortReverseUpdated?sortQuestions('updated_by','asc'):sortQuestions('updated_by','desc')">Updated By
                                                <span class="tb-sort-icons">
                                                       <i ng-show="sortType == 'updated_by' && !sortReverseUpdated" class="fa fa-caret-down"></i>
                                                       <i ng-show="sortType == 'updated_by' && sortReverseUpdated" class="fa fa-caret-up"></i>
                                                       <i ng-show="sortType != 'updated_by'" class="fa fa-sort"></i>
                                        </span></a></th>
                                        <th  class="width-4"><a href="" ng-click="sortReverseCreatedAt?sortQuestions('created_at','asc'):sortQuestions('created_at','desc')">Created Date & Time
                                                <span class="tb-sort-icons">
                                                       <i ng-show="sortType == 'created_at' && !sortReverseCreatedAt" class="fa fa-caret-down"></i>
                                                       <i ng-show="sortType == 'created_at' && sortReverseCreatedAt" class="fa fa-caret-up"></i>
                                                       <i ng-show="sortType != 'created_at'" class="fa fa-sort"></i>
                                        </span></a></th>
                                        <th class="width-6"><a href="" ng-click="sortReverseIsDraft?sortQuestions('is_draft','asc'):sortQuestions('is_draft','desc')">Display
                                                <span class="tb-sort-icons">
                                                       <i ng-show="sortType == 'is_draft' && !sortReverseIsDraft" class="fa fa-caret-down"></i>
                                                       <i ng-show="sortType == 'is_draft' && sortReverseIsDraft" class="fa fa-caret-up"></i>
                                                       <i ng-show="sortType != 'is_draft'" class="fa fa-sort"></i>
                                        </span></a></th>
                                        <th  class="width-5">Actions</th>

                                    </tr>
                                    </thead>
                                    <tbody></tbody>
                                </table>

                            </div>
                        </div>
                        <div>
                            <div class="dataTables_info" style="margin:20px 0;display: inline-block" id="example_info" role="status" aria-live="polite">Showing @{{from}} to @{{to}} of @{{total}} entries</div>
                            <posts-pagination ng-hide="total < entries"></posts-pagination>
                        </div>
                    </div>
                </div>

            </div>





        </div>
    </div>


    <div class="content animate-panel">

    </div>

    <div class="modal fade" id="questionLogModel" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="color-line"></div>
                <div class="modal-header text-center">
                    <h4 class="version-modal-title"></h4>
                </div>
                <div class="modal-body" id="questionVersionBody">
                    <ul id="questionVersionList">

                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    {!! Html::script('/js/question/question.js') !!}
@stop