<label class="col-sm-2 control-label" name="addLicenceTable">License*</label>

    <input type="hidden" name="licenceValidation">


<div class="col-sm-10">
    <table id="addLicenceTable" class="col-sm-12">

        <tbody id="addLicenceTableBody">
        <tr>
            <td class="questionTableTd" style="display: none">1</td>
            <td class="questionTableStateTdInput state-list-position-top hidden">
                <div class="row">
                    <div class="col-xs-12">
                        <label>State:</label>
                        <select class="form-control states_fedaral"  id="states_fedaral_1" custom-attr="state_type">
                        </select>
                    </div>
                </div>
            </td>
            <td class="questionTableTdInput">
                <div class="col-xs-12">
                    <label>License Type:</label>
                    <select class="license_data marginButtom1 col-sm-11 form-control"  name="license_type_1" id="license_type_1" disabled multiple="multiple" custom-attr="licence_type">
                    </select>
                </div>

            </td>

            <td class="questionTableTd" valign="bottom">
                <div class="col-xs-12 m-b-xs">
                    <button class="btn btn-danger btn-circle marginLeft5 btn_delete_licence_type" type="button" item-number="1"><i class="fa fa-times"></i></button>
                    <button class="btn btn-success btn-circle marginLeft5 btnAddNewActionItem" id="btnAddLicense" type="button"><i class="fa fa-plus"></i></button>
                </div>
            </td>

        </tr>
        </tbody>
    </table>
</div>