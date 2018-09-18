function gotoWagonSearch(WagonName){
    sessionStorage.WagonName=WagonName;
    window.location.href = "WangonSearch.html";
}
$(document).ready(function() {
    let machineId=sessionStorage.MachineId;
    $.ajax({
        url: '../php/MainView.php',
        type: 'GET',
        dataType: 'JSON',
        data: {"machineId":machineId},
        success: function(data){
            sessionStorage.Procedure = data.MachineInfo.Procedure;//sessionStroage中存放procedure，提供给wagonSearch
            let conFailTable = document.getElementById('conFailTable');
            if(data.LastDayCon===0){
                $('table#conFailTable').find('thead').detach();
                $('table#conFailTable').find('tbody').empty();
                let thead = document.createElement('thead');
                let tr = document.createElement('tr');
                let th = document.createElement('th');
                th.setAttribute("class","text-center");
                th.appendChild(document.createTextNode("上个工作日无连续废"));
                tr.appendChild(th);
                thead.appendChild(tr);
                conFailTable .appendChild(thead);
            }
            else {
                $('table#conFailTable').find('thead').detach();
                $('table#conFailTable').find('tbody').detach();
                let tableHead = ["车号","连续废次数","合计张数","明细"];
                let tbody = document.createElement('tbody');
                let thead = document.createElement('thead');
                let tr = document.createElement('tr');
                for (let i = 0; i < tableHead.length; i++) {
                    headerTxt = document.createTextNode(tableHead[i]);
                    th = document.createElement('th');
                    th.setAttribute("class","text-center");
                    th.appendChild(headerTxt);
                    tr.appendChild(th);
                    thead.appendChild(tr);
                }
                conFailTable .appendChild(thead);
                for (let i = 0; i < data.LastDayCon.length; i++) {
                    let tr = document.createElement('tr');
                    let WagonName = data.LastDayCon[i].WagonName;
                    tr.appendChild(document.createElement('td'));
                    tr.appendChild(document.createElement('td'));
                    tr.appendChild(document.createElement('td'));
                    tr.appendChild(document.createElement('td'));
                    tr.cells[0].appendChild(document.createTextNode(WagonName));
                    tr.cells[1].appendChild(document.createTextNode(data.LastDayCon[i].ConFailCount));
                    tr.cells[2].appendChild(document.createTextNode(data.LastDayCon[i].ConFailNum));
                    let goto=document.createElement('input');
                    goto.value="明细";
                    goto.type="button";
                    goto.setAttribute("onclick","gotoWagonSearch('"+WagonName+"')");
                    goto.setAttribute("class","btn btn-large btn-primary");
                    tr.cells[3].appendChild(goto);
                    tbody.appendChild(tr);
                }
                conFailTable .appendChild(tbody);
            }
            document.getElementById("tablehead_lastday").innerText=data.SingleResult[4].CurrentDate;
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
            myChart.on('click', function (params) {
                let WagonName=params.value[1];
                gotoWagonSearch(WagonName)
            });
        },
        error: function(data){
            console.log(data);
        }
    });
});