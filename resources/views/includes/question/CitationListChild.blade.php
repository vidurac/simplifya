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

        @if(isset($saved_citations) && count($saved_citations) > 0)
            @foreach($saved_citations as $saved_citation)
                <div class="col-sm-12 table_row_child padder-v">
                    <div class="col-sm-4 no-padding">
                        <div class="col-sm-12">
                            <input class="form-control citation_child" type="text" name="citation" value="{{$saved_citation->citation}}">
                            <input class="citation_id_child" type="hidden" name="citation_id_child" value="{{$saved_citation->id}}">
                        </div>
                    </div>

                    <div class="col-sm-4 no-padding">
                        <div class="col-sm-12">
                            <input class="form-control link_child" type="text" name="link" value="{{$saved_citation->link}}">
                        </div>
                    </div>
                    <div class="col-sm-3 no-padding">
                        <div class="col-sm-12">
                            <input class="form-control des_child" type="text" name="des" value="{{$saved_citation->description}}">
                        </div>
                    </div>
                    <div class="col-sm-1 no-padding text-center">
                        <button class="btn btn-danger btn-circle delete_child" type="button"><i class="fa fa-times"></i></button>
                    </div>
                </div>
            @endforeach

        @else
            <div class="col-sm-12 table_row_child padder-v">
                <div class="col-sm-4 no-padding">
                    <div class="col-sm-12">
                        <input class="form-control citation_child" type="text" name="citation">
                    </div>
                </div>

                <div class="col-sm-4 no-padding">
                    <div class="col-sm-12">
                        <input class="form-control link_child" type="text" name="link">
                    </div>
                </div>
                <div class="col-sm-3 no-padding">
                    <div class="col-sm-12">
                        <input class="form-control des_child" type="text" name="des">
                    </div>
                </div>
                <div class="col-sm-1 no-padding text-center">
                    <button class="btn btn-danger btn-circle delete_child" type="button"><i class="fa fa-times"></i></button>
                </div>
            </div>
        @endif


    </div>
</div>
<div class="col-sm-12">
        <div class="col-sm-4 pull-right text-right no-padding">
            <input class="btn btn-success" type="button" value="Add New Citation" id="add_citation_child">
        </div>
</div>
