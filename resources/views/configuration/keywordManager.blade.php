<?php
/**
 * Created by PhpStorm.
 * User: harhsa
 * Date: 3/3/17
 * Time: 3:13 PM
 */
?>

@extends('layout.dashbord')

@section('content')
    <div class="content" ng-controller="KeywordListCtrl" ng-cloak="">

        <div class="row" ng-init="mjbEntityType=<?php echo $MJB_entity_type?>;">
            <div class="col-md-12">
                <div class="hpanel">
                    <div class="panel-body">

                        <div class="row" ng-show="keywords.length == 0">
                            <div class="col-md-12">
                                No Keywords found.
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <div class="search-field">
                                    <div class="col-md-2" style="padding-left: 0px"><input placeholder="Search by Keywords" ng-model="search.name" class="input-sm form-control ng-valid ng-dirty ng-valid-parse ng-empty ng-touched" ng-model-options="{ debounce: 500 }" style="" type="text"></div>
                                </div>
                            </div>
                        </div>


                        <div class="row" ng-show="keywords.length > 0">

                            <div class="col-lg-12">
                                <br>
                                <div class="dataTables_length">
                                    <label>Show <select name="question-detail-table_length" id="entries" aria-controls="question-detail-table" ng-model="pageSize" ng-change="getQuestions()" ng-options="e for e in entryOptions">
                                        </select> entries</label>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-condensed city-table" id="coupon-table" style="border-bottom: 1px solid #111;">
                                        <thead>
                                        <tr>
                                            <th>
                                                <a href="#" ng-click="sortType = 'name'; sortReverse = !sortReverse">
                                                   <span class="tb-sort-header-name">
                                                       Keyword
                                                   </span>
                                                    <span class="tb-sort-icons">
                                                       <i ng-show="sortType == 'name' && !sortReverse" class="fa fa-caret-down"></i>
                                                       <i ng-show="sortType == 'name' && sortReverse" class="fa fa-caret-up"></i>
                                                       <i ng-show="sortType != 'name'" class="fa fa-sort"></i>
                                                   </span>
                                                </a>
                                            </th>

                                            <th>
                                                <a href="#" ng-click="sortType = 'used'; sortReverse = !sortReverse">
                                                   <span class="tb-sort-header-name">
                                                       Actions
                                                   </span>
                                                </a>
                                            </th>

                                        </tr>
                                        </thead>
                                        <tbody>

                                        <tr dir-paginate="keyword in keywords | orderBy:sortType:sortReverse |filter:search | itemsPerPage: pageSize">

                                            <td>
                                                <span title="Edit" ng-init="keyword.tempName=keyword.name"
                                                      inline-edit="keyword.tempName"
                                                      inline-edit-textarea=""
                                                      inline-edit-title="Edit"
                                                      inline-edit-btn-edit="Edit Keyword"
                                                      inline-edit-btn-save="Save"
                                                      inline-edit-btn-cancel="Cancel"
                                                      inline-edit-on-blur="cancel"
                                                      inline-edit-on-click=""
                                                      inline-edit-placeholder="Edit"
                                                      inline-edit-callback="saveKeywordListener(keyword)"
                                                ></span>
                                            </td>
                                            <td><a class="btn btn-danger btn-circle btn-xm" data-toggle="tooltip" title="Delete"  ng-click="keywordDelete(keyword.id)"><i class="fa fa-trash-o"></i></a>
                                            </td>

                                        </tr>
                                        </tbody>
                                    </table>
                                    <dir-pagination-controls
                                            max-size="5"
                                            direction-links="true"
                                            boundary-links="true"   template-url="pagination.html">
                                    </dir-pagination-controls>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {!! Html::script('js/angular/keywords.js') !!}

    <script type="text/ng-template" id="pagination.html">
        <div class="focus-inner">
            <div class="range-label pagination ng-scope" style="float: left">Showing @{{ range.lower }} to @{{ range.upper }} of @{{ range.total }} entries</div>
            <ul class="pagination" ng-if="1 < pages.length" style="float: right">
                <li ng-if="boundaryLinks" ng-class="{ disabled : pagination.current == 1 }">
                    <a href="" ng-click="setCurrent(1)">&laquo;</a>
                </li>
                <li ng-if="directionLinks" ng-class="{ disabled : pagination.current == 1 }" class="ng-scope">
                    <a href="" ng-click="setCurrent(pagination.current - 1)" class="ng-binding">‹</a>
                </li>
                <li ng-repeat="pageNumber in pages track by $index" ng-class="{ active : pagination.current == pageNumber, disabled : pageNumber == '...' }">
                    <a href="" ng-click="setCurrent(pageNumber)">@{{ pageNumber }}</a>
                </li>

                <li ng-if="directionLinks" ng-class="{ disabled : pagination.current == pagination.last }" class="ng-scope">
                    <a href="" ng-click="setCurrent(pagination.current + 1)" class="ng-binding">›</a>
                </li>
                <li ng-if="boundaryLinks"  ng-class="{ disabled : pagination.current == pagination.last }">
                    <a href="" ng-click="setCurrent(pagination.last)">&raquo;</a>
                </li>
            </ul>
        </div>
    </script>
@stop
