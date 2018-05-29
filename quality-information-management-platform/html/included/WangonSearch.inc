<script>
    function wangonSearch(str) {
        machineId=document.cookie;
        wangonName=str;
        $.ajax({
            url: '../php/WangonSearch_Counting.php',
            type: 'GET',
            dataType: 'JSON',
            data: {"wangonName":wangonName,"machineId":machineId},
            success: function(data){
                document.getElementById("TotalFail").innerText=data.GeneralFail.TotalFail;
                document.getElementById("SerFail").innerText=data.GeneralFail.SerFail;
                document.getElementById("PsnNum").innerText=data.GeneralFail.PsnNum;
                document.getElementById("MaxK").innerText=data.GeneralFail.MaxK;
                document.getElementById("MaxM").innerText=data.GeneralFail.MaxM;
                if((data.ConFail).length===0) {
                    document.getElementById("ConNum").innerText = '本车无连续废';
                }
                else{
                    document.getElementById("ConNum").innerText='本车连续废数：'+(data.ConFail).length;
                    for(let i=1;i<=(data.ConFail).length;i++)
                    {
                        document.getElementById("testimage"+i).src="data:image/bmp;base64,"+data.ConFail[i-1].Image1;
                    }
                }
            },
            error: function(data){
                console.log(data);
            }
        });
    }
</script>
