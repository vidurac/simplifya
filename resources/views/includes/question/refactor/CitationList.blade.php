<label class="col-sm-2 control-label" name="addLicenceTable">Citations</label>


<div class="col-sm-10">
    <div id="main_div_child" class="col-sm-12 m-b citation-group">
        <div class="col-sm-12 table_header m-t">
            <div class="col-sm-4 no-padding"><div class="col-sm-12">
                    <label>Citation</label>
                </div></div>
            <div class="col-sm-4 no-padding"><div class="col-sm-12">
                    <label>Link</label>
                </div></div>
            <div class="col-sm-4 no-padding"><div class="col-sm-12">
                    <label>Description</label>
                </div></div>
        </div>
        <div class="col-sm-12 table_row_child padder-v" ng-repeat="citation in form.citations">
            <div class="col-sm-4 no-padding">
                <div class="col-sm-12">
                    <input class="form-control citation_child" type="text" name="citation" ng-model="citation.citation"  ng-disabled="!isOnEdit">
                </div>
            </div>

            <div class="col-sm-4 no-padding">
                <div class="col-sm-12">
                    <input class="form-control link_child" type="text" name="link" ng-model="citation.link"  ng-disabled="!isOnEdit">
                </div>
            </div>
            <div class="col-sm-3 no-padding">
                <div class="col-sm-12">
                    <input class="form-control des_child" type="text" name="des" ng-model="citation.description"  ng-disabled="!isOnEdit">
                </div>
            </div>
            <div class="col-sm-1 no-padding text-center">
                <button class="btn btn-danger btn-circle delete_child" type="button" ng-show="!$first" ng-click="removeCitation($index)" ng-show="isOnEdit"><i class="fa fa-times"></i></button>
            </div>
        </div>


    </div>
</div>
<div class="col-sm-12">
        <div class="col-sm-4 pull-right text-right no-padding">
            <input class="btn btn-success" type="button" value="Add New Citation" ng-click="addNewCitation()" ng-show="isOnEdit">
        </div>
</div>
