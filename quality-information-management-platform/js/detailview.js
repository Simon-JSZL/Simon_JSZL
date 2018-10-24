$(document).ready(function() {
    let WagonName=sessionStorage.WagonName;
    let Procedure=sessionStorage.Procedure;
    let Title=document.getElementById('DetailViewTitle');
    Title.innerText="缺陷明细-"+WagonName.toUpperCase( );
    $('#WangonTable').DataTable( {
        "scrollY":"501px",
        "scrollCollapse":true,
        "info":false,
        "paging":false,
        "searching":false,
        "bScrollCollapse" : true,
        "ordering":true,
        "language":{
            lengthMenu:"显示_MENU_条记录",
            loadingRecords:"载入中...",
            processing:"载入中..."
        },
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": "../php/DetailView.php",
            "type": "GET",
            "dataType": "JSON",
            "data": {"WagonName":WagonName,"Procedure":Procedure},
        },
        "columns": [
            {"data":"ID"},
            {"data":"FormatPos"},
            {"data":"Grade"},
            {"data":"Dim"}
        ]
    } );
    let table=$('#WangonTable').DataTable();
    $('#WangonTable tbody').on('click','tr',function(){
        table.$('tr.selected').removeClass('selected');
        $(this).addClass('selected');
        let data = table.row( this ).data();
        $('image#DetailViewImage').empty();
        showImage(data['IpAddress'],WagonName,data['ID']);
    });
});
function showImage(IpAddress,WagonName,ID){
    $.ajax({
        url: '../php/DetailView_Image.php',
        type: 'GET',
        dataType: 'JSON',
        data: {"IpAddress":IpAddress,"WagonName":WagonName,"ID":ID},
        success: function(data){
            image = document.getElementById('DetailViewImage');
            image.src = "data:image/bmp;base64," + data.Image;
        },
        error: function(data){
            console.log(data);
        }
    });
}