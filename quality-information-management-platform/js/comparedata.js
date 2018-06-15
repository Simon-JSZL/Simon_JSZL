function compareData(StartDate, EndDate, ProductId, SideId) {
    if(SideId==='正面')
        SideId=1;
    else if(SideId==='反面')
        SideId=0;
    $.ajax({
        url: '../php/CompareData.php',
        type: 'GET',
        dataType: 'JSON',
        data: {"StartDate":StartDate,"EndDate":EndDate,"ProductId":ProductId,"SideId":SideId},
        success: function(data){
        },
        error: function(data){
            console.log(data);
        }
    });
}