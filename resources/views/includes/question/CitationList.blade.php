<label class="col-sm-2 control-label" name="addLicenceTable">Citations</label>


<div class="col-sm-10">
    <div id="main_div" class="col-sm-12 m-b citation-group">
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
                <div class="col-sm-12 table_row padder-v">
                    <div class="col-sm-4 no-padding">
                        <div class="col-sm-12">
                            @if(!isset($view_only_citations))
                                <input class="form-control citation" type="text" name="citation" value="{{$saved_citation->citation}}" autocomplete="off">
                                <input class="citation_id" type="hidden" name="citation_id" value="{{$saved_citation->id}}" autocomplete="off">
                            @else
                                <input class="form-control citation" disabled type="text" name="citation" value="{{$saved_citation->citation}}" autocomplete="off">
                            @endif
                        </div>
                    </div>

                    <div class="col-sm-4 no-padding">
                        <div class="col-sm-12">
                            @if(!isset($view_only_citations))
                                <input class="form-control link" type="text" name="link" value="{{$saved_citation->link}}" autocomplete="off">
                            @else
                                <input class="form-control link" disabled type="text" name="link" value="{{$saved_citation->link}}" autocomplete="off">
                            @endif
                        </div>
                    </div>
                    <div class="col-sm-3 no-padding">
                        <div class="col-sm-12">
                            @if(!isset($view_only_citations))
                                <input class="form-control des" type="text" name="des" value="{{$saved_citation->description}}" autocomplete="off">

                            @else
                                <input class="form-control des" disabled type="text" name="des" value="{{$saved_citation->description}}" autocomplete="off">
                            @endif
                        </div>
                    </div>
                    @if(!isset($view_only_citations))
                        <div class="col-sm-1 no-padding text-center">

                            <button class="btn btn-danger btn-circle delete" type="button"><i class="fa fa-times"></i></button>
                        </div>
                    @endif


                </div>
            @endforeach

        @else
            <div class="col-sm-12 table_row padder-v">
                <div class="col-sm-4 no-padding">
                    <div class="col-sm-12">
                        <input class="form-control citation" type="text" name="citation" autocomplete="off">
                    </div>
                </div>

                <div class="col-sm-4 no-padding">
                    <div class="col-sm-12">
                        <input class="form-control link" type="text" name="link" autocomplete="off">
                    </div>
                </div>
                <div class="col-sm-3 no-padding">
                    <div class="col-sm-12">
                        <input class="form-control des" type="text" name="des" autocomplete="off">
                    </div>
                </div>
                <div class="col-sm-1 no-padding text-center">
                    <button class="btn btn-danger btn-circle delete" type="button"><i class="fa fa-times"></i></button>
                </div>
            </div>
        @endif


    </div>
</div>

@if(!isset($view_only_citations))
    <div class="col-sm-12">
            <div class="col-sm-4 pull-right text-right no-padding">
                <input class="btn btn-success" type="button" value="Add New Citation" id="add_citation">
            </div>
    </div>
@endif
