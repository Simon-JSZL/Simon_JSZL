function createImage(MachineId,WagonName){
    $.ajax({
        url: '../php/SaveImage.php',
        type: 'GET',
        dataType: 'JSON',
        data: {"MachineId":MachineId,"WagonName":WagonName},
        success: function(data){
            for(let i=0;i<data.length;i++){
                saveBase64AsFile(data[i].Image,data[i].Id);
            }
        },
        error: function(data){
            console.log(data);
        }
    });
}
function saveBase64AsFile(base64, fileName){
    let image = "data:image/bmp;base64," + base64;
    download(image,fileName,"image/bmp");
}