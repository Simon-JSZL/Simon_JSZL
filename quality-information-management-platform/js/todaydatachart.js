function gotoWagonSearch(WagonName, Procedure){
    sessionStorage.WagonName = WagonName;
    sessionStorage.Procedure = Procedure;
    window.location.href = "WangonSearch.html";
}
$(document).ready(function() {
    let machineId=sessionStorage.MachineId;
    $.ajax({
        url: '../php/TodayDataChart.php',
        type: 'GET',
        dataType: 'JSON',
        data: {"machineId":machineId},
        success: function(data) {
            if (data !== 0) {
                $('div#TodayDataChart_Div').show();
                var myChart = echarts.init(document.getElementById('TodayDataChart'), 'light');
                var chartdetail = [];
                $.each(data, function (i, obj) {
                    chartdetail.push([obj.WagonName, obj.CreateTime, obj.TotalFail, obj.SerFail, obj.PsnNum]);
                });
                option = {
                    title: {
                        text: '今日作废',
                        left: 'center'
                    },
                    tooltip: {
                        trigger: 'axis'
                    },
                    grid: {
                        left: '0%',
                        right: '4%',
                        bottom: '3%',
                        containLabel: true
                    },
                    xAxis: {
                        type: 'category',

                    },
                    yAxis: {
                        type: 'value',
                        name: '作废数量'
                    },
                    dataset: {
                        dimensions: [
                            '车号',
                            '印刷时间',
                            '作废总数',
                            '严重废数',
                            '三仓数'
                        ],
                        source: chartdetail
                    },
                    series: [
                        {
                            name: '详细信息',
                            type: 'bar',
                            encode: {
                                x: '车号',
                                y: '作废总数',
                                tooltip: [1, 2]
                            },
                        },
                        {
                            name: '严重废数',
                            type: 'bar',
                            encode: {
                                x: '车号',
                                y: '严重废数'
                            }

                        },
                        {
                            name: '三仓数',
                            type: 'bar',
                            encode: {
                                x: '车号',
                                y: '三仓数'
                            }
                        }
                    ]
                };
                myChart.setOption(option);
                myChart.on('click', function (params) {
                    let WagonName = params.value[0];
                    let Procedure = data[0].Procedure;
                    gotoWagonSearch(WagonName, Procedure);
                });
            }
        },
        error: function(data){
            console.log(data);
        }

    });
});