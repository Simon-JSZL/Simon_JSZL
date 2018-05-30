<script>
    function wangonSearch(str) {
        wangonName=str;
        $.ajax({
            url: '../php/WangonSearch_Counting.php',
            type: 'GET',
            dataType: 'JSON',
            data: {"wangonName":wangonName},
            success: function(data){
                if(data===0) {
                    alert('查无本车!');
                }
                else {
                    document.getElementById("MachineId").innerText = data.WangonInfo.MachineId;
                    document.getElementById("CreateTime").innerText = data.WangonInfo.CreateTime;
                    document.getElementById("TotalFail").innerText = data.GeneralFail.TotalFail;
                    document.getElementById("SerFail").innerText = data.GeneralFail.SerFail;
                    document.getElementById("PsnNum").innerText = data.GeneralFail.PsnNum;
                    document.getElementById("MaxM").innerText = data.GeneralFail.MaxM;
                    document.getElementById("MaxK").innerText = data.GeneralFail.MaxK;
                }},
            error: function(data){
                console.log(data);
            }
        });
    }
</script>
