function dateSearch(StartDate,EndDate) {
    let machineId=document.cookie;
    $.ajax({
        url: '../php/DateSearch_Counting.php',
        type: 'GET',
        dataType: 'JSON',
        data: {"StartDate":StartDate,"EndDate":EndDate,"MachineId":machineId},
        success: function(data){
            if(data===0) {
                $('div#dataSearchTablesDiv').hide();
                alert("未查询到该时间段内有印刷车次");
            }
            else {
                console.log(data);
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
                var chartdetail_wagon = [];
                $.each(data.EachWangonResult, function(i, obj){
                    chartdetail_wagon.push([obj.crtime_wangon, obj.tablename, obj.totalfail_wangon, obj.serfail_wangon, obj.psnnum_wangon]);
                });
                var wagonChart = echarts.init(document.getElementById('wagonFail_chart'), 'light');
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
            }
        },
        error: function(data){
            console.log(data);
        }
    });
}