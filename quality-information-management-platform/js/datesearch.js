$(document).ready(function() {
    let StartDate=sessionStorage.CurrentDate//sql server无法识别斜杠的日期格式，改位横杠
    let EndDate=StartDate;
    let MachineId=$.trim(sessionStorage.CompareMachineId);//jquery自带的trim方法
    if(StartDate!==undefined)
        StartDate=StartDate.replace(/\//g,"-");
    if(StartDate!==""&&EndDate!==""&&MachineId!=="") {
       dateSearch(StartDate,EndDate,MachineId);
    }
    sessionStorage.CurrentDate="";
    sessionStorage.CompareMachineId="";
});
function dateSearch(StartDate,EndDate,MachineId) {
    if(MachineId===""){
        $('div#dataSearchTablesDiv').hide();
        alert("请选择机台");
    }
    else{
        $.ajax({
            url: '../php/DateSearch_Counting.php',
            type: 'GET',
            dataType: 'JSON',
            data: {"StartDate":StartDate,"EndDate":EndDate,"MachineId":MachineId},
            success: function(data){
                if(data===0) {
                    $('div#dataSearchTablesDiv').hide();
                    alert("未查询到该时间段内有印刷车次");
                }
                else {
                    sessionStorage.Procedure = data.MachineInfo.Procedure;
                    $('div#dataSearchTablesDiv').show();
                    document.getElementById("tablehead_totaldate_start").innerText=data.SingleResult[0].CurrentDate;
                    document.getElementById("tablehead_totaldate_end").innerText=data.SingleResult[data.SingleResult.length-1].CurrentDate;
                    document.getElementById("total_AVGFail").innerText=data.TotalResult.AVGTotal_total;
                    document.getElementById("total_AVGSer").innerText=data.TotalResult.AVGSer_total;
                    document.getElementById("total_AVGPsn").innerText=data.TotalResult.AVGPsn_total;
                    document.getElementById("total_MaxM").innerText=data.TotalResult.maxM_total;
                    document.getElementById("total_MaxK").innerText=data.TotalResult.maxk_total;
                    let dailyFailTable = document.getElementById('dailyFailTable');
                    $('table#dailyFailTable').find('tbody').detach();
                    let tbody = document.createElement('tbody');
                    for(let i = 0; i < data.SingleResult.length; i++){
                        let tr = document.createElement('tr');
                        tr.appendChild(document.createElement('td'));
                        tr.appendChild(document.createElement('td'));
                        tr.appendChild(document.createElement('td'));
                        tr.appendChild(document.createElement('td'));
                        tr.appendChild(document.createElement('td'));
                        tr.appendChild(document.createElement('td'));
                        tr.cells[0].appendChild(document.createTextNode(data.SingleResult[i].CurrentDate));
                        tr.cells[1].appendChild(document.createTextNode(data.SingleResult[i].AVGTotal));
                        tr.cells[2].appendChild(document.createTextNode(data.SingleResult[i].AVGSer));
                        tr.cells[3].appendChild(document.createTextNode(data.SingleResult[i].AVGPsn));
                        tr.cells[4].appendChild(document.createTextNode(data.SingleResult[i].maxM));
                        tr.cells[5].appendChild(document.createTextNode(data.SingleResult[i].maxk));
                        tbody.appendChild(tr);
                    }
                    dailyFailTable .appendChild(tbody);
                    let chartdetail_wagon = [];//建立echarts的数据保存数组
                    $.each(data.EachWangonResult, function(i, obj){
                        chartdetail_wagon.push([obj.crtime_wangon, obj.tablename, obj.totalfail_wangon, obj.serfail_wangon, obj.psnnum_wangon]);
                    });
                    let myChart=echarts.getInstanceByDom(document.getElementById('wagonFail_chart'));//如果存在实例则dispose
                    if (myChart != null && myChart !== "" && myChart !== undefined) {
                        myChart.dispose();
                    }
                    let wagonChart = echarts.init(document.getElementById('wagonFail_chart'), 'light');
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
                                '印刷时间',
                                '车号',
                                '作废总数',
                                '严重废数',
                                '三仓数'
                            ],
                            source: chartdetail_wagon
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
                    wagonChart.setOption(option);
                    wagonChart.on('click', function (params) {
                        sessionStorage.WagonName=params.value[1];
                        window.location.href = "WangonSearch.html";
                    });
                }
            },
            error: function(data){
                console.log(data);
            }
        });
    }
}