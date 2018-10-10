$(document).ready(function() {
    $('#WangonTable').DataTable( {
        dom: "Bfrtip",
        "scrollY":"500px",
        "scrollCollapse":true,
        "info":false,
        "ordering":true,
        "paging":false,
        "searching":false,
        "language":{
            lengthMenu:"显示_MENU_条记录",
            loadingRecords:"载入中...",
            processing:"载入中..."
        },
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "../php/DetailView.php",
            "type": "GET"
        },
        "columns": [
            { "data": "ID" },
            { "data": "Psn" },
            { "data": "FormatPos" },
            { "data": "Grade"},
            { "data": "Dim"}
        ]
    } );
    var table=$('#WangonTable').DataTable();
    $('#WangonTable tbody').on('click','tr',function(){
        if ( $(this).hasClass('selected') ) {
            $(this).removeClass('selected');
        }
        else {
            table.$('tr.selected').removeClass('selected');
            $(this).addClass('selected');
        }
    });
});
function alert(){

}