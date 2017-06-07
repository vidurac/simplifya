// Download Report
function downloadCompanyReport(url){
    location.href = url+"?business_name="+$('#business_name').val()+"&entity_type="+$('#entity_type').val()+"&status="+$('#status').val()
}