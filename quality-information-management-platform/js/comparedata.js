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
            if(data===0)
                alert("未查询到该时间段内有印刷车次");
            else{
                var chartdetail_AvgTotal = [];
                var chartdetail_MachineId= [];
                var totalFailChart = echarts.init(document.getElementById('totalFail_chart'), 'light');
                var series=[];
                for(let i=0;i<data.CompareData.length;i++)
                {
                    MachineId=data.CompareData[i][0]['MachineId'];
                    for(let j=1;j<data.CompareData[i].length;j++){
                        CurrentDate=data.CompareData[i][j]['CurrentDate'];
                        AvgTotal=data.CompareData[i][j]['AvgTotal'];
                        series.push([MachineId,CurrentDate,AvgTotal]);
                    }
                }
                console.log(chartdetail_AvgTotal);
                option = {
                    title: {
                        text: ''
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    legend: {
                        data:data[0]
                    },
                    grid: {
                        left: '0%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    dataZoom: [
                        {
                            type: 'slider',
                            start: 0,
                            end: 100
                        },
                        {
                            type: 'inside',
                            start: 0,
                            end: 100
                        }
                    ],
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
                            '机台',
                            '印刷时间',
                            '作废总数'
                        ]
                    },
                    series: series
                };
                // 使用刚指定的配置项和数据显示图表。
                totalFailChart.setOption(option);
            }
        },
        error: function(data){
            console.log(data);
        }
    });
}