<script>
    function dateSearch(StartDate,EndDate) {
        let machineId=document.cookie;
        $.ajax({
            url: '../php/DateSearch_Counting.php',
            type: 'GET',
            dataType: 'JSON',
            data: {"StartDate":StartDate,"EndDate":EndDate,"MachineId":machineId},
            success: function(data){
                document.getElementById("tablehead_totaldate_start").innerText=StartDate;
                document.getElementById("tablehead_totaldate_end").innerText=EndDate;
                document.getElementById("total_AVGFail").innerText=data.TotalResult.AVGTotal_total;
                document.getElementById("total_AVGSer").innerText=data.TotalResult.AVGSer_total;
                document.getElementById("total_AVGPsn").innerText=data.TotalResult.AVGPsn_total;
                document.getElementById("total_MaxM").innerText=data.TotalResult.maxM_total;
                document.getElementById("total_MaxK").innerText=data.TotalResult.maxk_total;
            },
            error: function(data){
                console.log(data);
            }
        });
    }
</script>