<script>
   $(document).ready(function() {
       var machineId=document.cookie;
       $.ajax({
           url: '../php/MainView_Counting.php',
           type: 'GET',
           dataType: 'JSON',
           data: {"machineId":machineId},
           success: function(data){
               document.getElementById("tablehead_totaldate_start").innerText=data.SingleResult[0].CurrentDate;
               document.getElementById("tablehead_totaldate_end").innerText=data.SingleResult[4].CurrentDate;
               document.getElementById("total_AVGFail").innerText=data.TotalResult.AVGTotal_total;
               document.getElementById("total_AVGSer").innerText=data.TotalResult.AVGSer_total;
               document.getElementById("total_AVGPsn").innerText=data.TotalResult.AVGPsn_total;
               document.getElementById("total_MaxM").innerText=data.TotalResult.maxM_total;
               document.getElementById("total_MaxK").innerText=data.TotalResult.maxk_total;
               for(let i=1;i<=5;i++)
               {
                   document.getElementById("single_date_day"+i).innerText=data.SingleResult[i-1].CurrentDate;
                   document.getElementById("single_AVGFail_day"+i).innerText=data.SingleResult[i-1].AVGTotal;
                   document.getElementById("single_AVGSer_day"+i).innerText=data.SingleResult[i-1].AVGSer;
                   document.getElementById("single_AVGPsn_day"+i).innerText=data.SingleResult[i-1].AVGPsn;
                   document.getElementById("single_MaxM_day"+i).innerText=data.SingleResult[i-1].maxM;
                   document.getElementById("single_MaxK_day"+i).innerText=data.SingleResult[i-1].maxk;
               }
               var chartdetail = [];
               $.each(data.EachWangonResult, function(i, obj){
                   chartdetail.push([obj.crtime_wangon, obj.tablename, obj.totalfail_wangon, obj.serfail_wangon, obj.psnnum_wangon]);
               });
               var myChart = echarts.init(document.getElementById('fivedaydetail_chart'), 'light');
               option = {
                   title: {
                       text: ''
                   },
                   tooltip: {
                       trigger: 'axis'
                   },
                   legend: {
                       data:['作废总数',
                           '严重废数',
                           '三仓数']
                   },
                   grid: {
                       left: '0%',
                       right: '4%',
                       bottom: '3%',
                       containLabel: true
                   },
                   toolbox: {
                       left: 'right',
                       feature: {
                           dataZoom: {}
                       }
                   },
                   xAxis: {
                       type: 'time',
                       boundaryGap: false
                   },
                   yAxis: {
                       type: 'value',
                       name: '作废数量'
                   },
                   dataset: {
                       dimensions: [
                           '印刷时间',
                           '车号',
                           '作废总数',
                           '严重废数',
                           '三仓数'
                       ],
                       source: chartdetail
                   },
                   series: [
                       {
                           name:'详细信息',
                           type: 'line',
                           encode: {
                               x: '印刷时间',
                               y: '作废总数',
                               tooltip:[1,1]
                           }
                       },
                       {
                           name:'作废总数',
                           type: 'line',
                           encode: {
                               x: '印刷时间',
                               y: '作废总数'
                           }
                       },
                       {
                           name:'严重废数',
                           type: 'line',
                           encode: {
                               x: '印刷时间',
                               y: '严重废数'
                           }

                       },
                       {
                           name:'三仓数',
                           type: 'line',
                           encode: {
                               x: '印刷时间',
                               y: '三仓数'
                           }
                       }
                   ]
               };
               // 使用刚指定的配置项和数据显示图表。
               myChart.setOption(option);
           },
           error: function(data){
               console.log(data);
           }
       });
   });
</script>


