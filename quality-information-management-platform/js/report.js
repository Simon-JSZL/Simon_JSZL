$('#StartDate,#EndDate').datepicker({
    format: "yyyy-mm",
    startView: 1,
    minViewMode: 1,
    language: "zh-CN",
    orientation: "bottom auto",
    autoclose: true,
    todayHighlight: true
});
function generateReport(StartDate,EndDate,MachineId,BiggerThan,LesserThan,SearchTerm){
    $.ajax({
        url: '../php/Report.php',
        type: 'GET',
        dataType: 'JSON',
        data: {"StartDate":StartDate,"EndDate":EndDate,"MachineId":MachineId,"BiggerThan":BiggerThan,"LesserThan":LesserThan,"SearchTerm":SearchTerm},
        success: function(data){

        },
        error: function(data){
            console.log(data);
        }
    });
}
function displayReport(StartDate,EndDate,MachineId,BiggerThan,LesserThan,SearchTerm){
    $.ajax({
        url: '../php/Report.php',
        type: 'GET',
        dataType: 'JSON',
        data: {"StartDate":StartDate,"EndDate":EndDate,"MachineId":MachineId,"BiggerThan":BiggerThan,"LesserThan":LesserThan,"SearchTerm":SearchTerm},
        success: function(data){

        },
        error: function(data){
            console.log(data);
        }
    });
}
