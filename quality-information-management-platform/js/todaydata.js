$(document).ready(function() {
    let machineId=sessionStorage.MachineId;
    $.ajax({
        url: '../php/MainView.php',
        type: 'GET',
        dataType: 'JSON',
        data: {"machineId":machineId},
        success: function(data){

        },
        error: function(data){
        console.log(data);
    }
});
});