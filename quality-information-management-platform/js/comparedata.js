function compareData(StartDate, EndDate, ProductId, SideId) {
    if(SideId==='凹印正面')
        SideId=1;
    else if(SideId==='凹印反面')
        SideId=0;
    $.ajax({
        url: '../php/CompareData.php',
        type: 'GET',
        dataType: 'JSON',
        data: {"StartDate":StartDate,"EndDate":EndDate,"ProductId":ProductId,"SideId":SideId},
        success: function(data){
            let myChart_total=echarts.getInstanceByDom(document.getElementById('totalFail_chart'));
            if (myChart_total != null && myChart_total !== "" && myChart_total !== undefined) {
                myChart_total.dispose();
            }
            let myChart_ser=echarts.getInstanceByDom(document.getElementById('serFail_chart'));
            if (myChart_ser != null && myChart_ser !== "" && myChart_ser !== undefined) {
                myChart_ser.dispose();
            }
            let myChart_psn=echarts.getInstanceByDom(document.getElementById('psnNum_chart'));
            if (myChart_psn != null && myChart_psn !== "" && myChart_psn !== undefined) {
                myChart_psn.dispose();
            }//检查已经有实例就销毁之前的实例，防止warning
            let Container_AvgTotal = document.getElementById('totalFail_chart');
            let Container_AvgSer = document.getElementById('serFail_chart');
            let Container_AvgPsn = document.getElementById('psnNum_chart');
            let resizeMainContainer = function (Container) {
                Container.style.width = window.innerWidth*0.845+'px';
                Container.style.height = window.innerHeight*0.8+'px';
            };
            resizeMainContainer(Container_AvgTotal);
            resizeMainContainer(Container_AvgSer);
            resizeMainContainer(Container_AvgPsn);//获取窗体的大小动态设置div的宽高
            let totalFailChart = echarts.init(document.getElementById('totalFail_chart'), 'light');
            let serFailChart = echarts.init(document.getElementById('serFail_chart'), 'light');
            let psnNumChart = echarts.init(document.getElementById('psnNum_chart'), 'light');
            if(data.CompareData===0){//查询的时间段内没有生产车次就重置实例并alert
                totalFailChart.clear();
                serFailChart.clear();
                psnNumChart.clear();
                $('div#compareDataChartsDiv').hide();
                alert("未查询到该时间段内有印刷车次");
            }
            else if(data.CompareData===1){
                totalFailChart.clear();
                serFailChart.clear();
                psnNumChart.clear();
                $('div#compareDataChartsDiv').hide();
                alert("未查询到相关的生产机台");
            }
            else{
                $('div#compareDataChartsDiv').show();
                let AvgTotalData=new Array(2);//{0}=>{'2014-01-01',100}二维数组，存放一个机台一段时间内的对应作废，用于显示
                let AvgSerData=new Array(2);
                let AvgPsnData=new Array(2);
                let MachineId=[];
                option_Total = {
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
                        name: '作废总数'
                    },
                    series:function(){
                        var serie_Total=[];
                        for(let i=0;i<data.CompareData.length;i++)//这层循环用于切换机台
                        {
                            MachineId.push(data.CompareData[i][0]['MachineId']);
                            for(let j=1;j<data.CompareData[i].length;j++){//这层循环用于切换日期，每一天往数组里push一次
                                let CurrentDate=data.CompareData[i][j]['CurrentDate'];
                                let AvgTotal=data.CompareData[i][j]['AvgTotal'];
                                AvgTotalData.push([CurrentDate,AvgTotal]);
                            }
                            let item={
                                name:MachineId[i],//用于显示的提示
                                type:'line',
                                data:AvgTotalData
                            };
                            serie_Total.push(item);
                            AvgTotalData=[];//一条折线的item保存完毕，清空数组用于保存下一个机台的
                        }
                        return serie_Total;
                    }()
                };
                option_Ser = {
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
                        name: '严废总数'
                    },
                    series:function(){
                        var serie_Ser=[];
                        for(let i=0;i<data.CompareData.length;i++)
                        {
                            MachineId.push(data.CompareData[i][0]['MachineId']);
                            for(let j=1;j<data.CompareData[i].length;j++){
                                let CurrentDate=data.CompareData[i][j]['CurrentDate'];
                                let AvgSer=data.CompareData[i][j]['AvgSer'];
                                AvgSerData.push([CurrentDate,AvgSer]);
                            }
                            let item={
                                name:MachineId[i],
                                type:'line',
                                data:AvgSerData
                            };
                            serie_Ser.push(item);
                            AvgSerData=[];
                        }
                        return serie_Ser;
                    }()
                };
                option_Psn = {
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
                        name: '三仓总数'
                    },
                    series:function(){
                        var serie_Psn=[];
                        for(let i=0;i<data.CompareData.length;i++)
                        {
                            MachineId.push(data.CompareData[i][0]['MachineId']);
                            for(let j=1;j<data.CompareData[i].length;j++){
                                let CurrentDate=data.CompareData[i][j]['CurrentDate'];
                                let AvgPsn=data.CompareData[i][j]['AvgPsn'];
                                AvgPsnData.push([CurrentDate,AvgPsn]);
                            }
                            let item={
                                name:MachineId[i],
                                type:'line',
                                data:AvgPsnData
                            };
                            serie_Psn.push(item);
                            AvgPsnData=[];
                        }
                        return serie_Psn;
                    }()
                };
                totalFailChart.setOption(option_Total);
                serFailChart.setOption(option_Ser);
                psnNumChart.setOption(option_Psn);
                totalFailChart.on('click', function (params) {
                    sessionStorage.CompareMachineId=params.seriesName;
                    sessionStorage.CurrentDate=params.value[0];
                    window.location.href = "DateSearch.html";
                });
                serFailChart.on('click', function (params) {
                    sessionStorage.CompareMachineId=params.seriesName;
                    sessionStorage.CurrentDate=params.value[0];
                    window.location.href = "DateSearch.html";
                });
                psnNumChart.on('click', function (params) {
                    sessionStorage.CompareMachineId=params.seriesName;
                    sessionStorage.CurrentDate=params.value[0];
                    window.location.href = "DateSearch.html";
                });
            }
        },
        error: function(data){
            console.log(data);
        }
    });
}