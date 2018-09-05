$('#StartDate,#EndDate').datepicker({
    format: "yyyy-mm",
    startView: 1,
    minViewMode: 1,
    language: "zh-CN",
    orientation: "bottom auto",
    autoclose: true,
    todayHighlight: true
});
function generateReport(StartDate,EndDate,MachineId,SearchName){
    let wb = XLSX.utils.book_new();
    let elt_weekTable = document.getElementById('weekTable');
    let elt_monthTable = document.getElementById('monthTable');
    let ws_weekTable = XLSX.utils.table_to_sheet(elt_weekTable);
    let ws_monthTable = XLSX.utils.table_to_sheet(elt_monthTable);
    XLSX.utils.book_append_sheet(wb, ws_weekTable, "周统计");
    XLSX.utils.book_append_sheet(wb, ws_monthTable, "月统计");
    XLSX.writeFile(wb,MachineId+SearchName+'报告.xlsx');
}
function createWeekTable(BiggerThan,LesserThan,SearchName,data){
    let weekTable = document.getElementById('weekTable');//获得表
    $('table#weekTable').find('thead').detach();
    $('table#weekTable').find('tbody').detach();
    $('div#compareDataChartsDiv').show();
    let BiggerThanInt = parseInt(BiggerThan);//默认的BiggerThan和LesserThan都是string型
    let LesserThanInt = parseInt(LesserThan);
    let ConditionTitle = "";//查询结果的conditionResult为单值时使用的conditionName
    let ConditionTitle1 = "";//查询结果的conditionResult为双值时使用的conditionName
    let ConditionTitle2 = "";
    let tableHead = "";//表头
    let tbody = document.createElement('tbody');
    let thead = document.createElement('thead');
    let tr = document.createElement('tr');
    if (BiggerThan === "" && LesserThan !== "") {
        ConditionTitle = SearchName + "小于" + LesserThanInt + "的车次数";
        tableHead = ["时间", "生产车次（万）", "单车最低"+SearchName, "单车最高"+SearchName, "平均"+SearchName, ConditionTitle];
    }
    else if (LesserThan === "" && BiggerThan !== "") {
        ConditionTitle = SearchName + "大于" + BiggerThanInt + "的车次数";
        tableHead = ["时间", "生产车次（万）", "单车最低"+SearchName, "单车最高"+SearchName, "平均"+SearchName, ConditionTitle];
    }
    else if (BiggerThanInt < LesserThanInt) {
        ConditionTitle = SearchName + "大于" + BiggerThanInt + "小于" + LesserThanInt + "的车次数";
        tableHead = ["时间", "生产车次（万）", "单车最低"+SearchName, "单车最高"+SearchName, "平均"+SearchName, ConditionTitle];
    }
    else if (BiggerThanInt > LesserThanInt) {
        ConditionTitle1 = SearchName + "小于" + LesserThanInt + "的车次数";
        ConditionTitle2 = SearchName + "大于" + BiggerThanInt + "的车次数";
        tableHead = ["时间", "生产车次（万）", "单车最低"+SearchName, "单车最高"+SearchName, "平均"+SearchName, ConditionTitle1, ConditionTitle2];
    }
    else if (BiggerThanInt === LesserThanInt) {
        ConditionTitle = SearchName + "等于" + BiggerThanInt + "的车次数";
        tableHead = ["时间", "生产车次（万）", "单车最低"+SearchName, "单车最高"+SearchName, "平均"+SearchName, ConditionTitle];
    }
    for (let i = 0; i < tableHead.length; i++) {
        headerTxt = document.createTextNode(tableHead[i]);
        th = document.createElement('th');
        th.setAttribute("class", "text-center");
        th.appendChild(headerTxt);
        tr.appendChild(th);
        thead.appendChild(tr);
    }
    weekTable.appendChild(thead);
    for (let i = 0; i < data.GeneralResult.length; i++) {
        let row_year = data.GeneralResult[i].Date.substr(0, 4);
        let row_month = data.GeneralResult[i].Date.substr(5, 2);
        let row_week = data.GeneralResult[i].Date.substr(9, 2);
        let tr = document.createElement('tr');
        tr.appendChild(document.createElement('td'));
        tr.appendChild(document.createElement('td'));
        tr.appendChild(document.createElement('td'));
        tr.appendChild(document.createElement('td'));
        tr.appendChild(document.createElement('td'));
        tr.cells[0].appendChild(document.createTextNode(row_year + "年" + row_month + "月" + "第" + row_week + "周"));
        tr.cells[1].appendChild(document.createTextNode(data.GeneralResult[i].TotalNum));
        tr.cells[2].appendChild(document.createTextNode(data.GeneralResult[i].MinValue));
        tr.cells[3].appendChild(document.createTextNode(data.GeneralResult[i].MaxValue));
        tr.cells[4].appendChild(document.createTextNode(data.GeneralResult[i].AvgValue));
        if (BiggerThanInt > LesserThanInt) {
            tr.appendChild(document.createElement('td'));
            tr.appendChild(document.createElement('td'));
            tr.cells[5].appendChild(document.createTextNode(data.ConditionResult[i].ConditionNum1));
            tr.cells[6].appendChild(document.createTextNode(data.ConditionResult[i].ConditionNum2));
        }
        else {
            tr.appendChild(document.createElement('td'));
            tr.cells[5].appendChild(document.createTextNode(data.ConditionResult[i].ConditionNum));
        }
        tbody.appendChild(tr);
    }
    weekTable.appendChild(tbody);
}
function createMonthTable(BiggerThan,LesserThan,SearchName,data){
    let monthTable = document.getElementById('monthTable');//获得表
    $('table#monthTable').find('thead').detach();
    $('table#monthTable').find('tbody').detach();
    $('div#compareDataChartsDiv').show();
    let tbody = document.createElement('tbody');
    let thead = document.createElement('thead');
    let tr = document.createElement('tr');
    let tableHead = ["时间", "生产车次（万）", "单车最低"+SearchName, "单车最高"+SearchName, "平均"+SearchName];
    for (let i = 0; i < tableHead.length; i++) {
        headerTxt = document.createTextNode(tableHead[i]);
        th = document.createElement('th');
        th.setAttribute("class", "text-center");
        th.appendChild(headerTxt);
        tr.appendChild(th);
        thead.appendChild(tr);
    }
    monthTable.appendChild(thead);
    for (let i = 0; i < data.MonthResult.length; i++) {
        let row_year = data.MonthResult[i].Date.substr(0, 4);
        let row_month = data.MonthResult[i].Date.substr(5, 2);
        let tr = document.createElement('tr');
        tr.appendChild(document.createElement('td'));
        tr.appendChild(document.createElement('td'));
        tr.appendChild(document.createElement('td'));
        tr.appendChild(document.createElement('td'));
        tr.appendChild(document.createElement('td'));
        tr.cells[0].appendChild(document.createTextNode(row_year + "年" + row_month + "月"));
        tr.cells[1].appendChild(document.createTextNode(data.MonthResult[i].TotalNum));
        tr.cells[2].appendChild(document.createTextNode(data.MonthResult[i].MinValue));
        tr.cells[3].appendChild(document.createTextNode(data.MonthResult[i].MaxValue));
        tr.cells[4].appendChild(document.createTextNode(data.MonthResult[i].AvgValue));
        tbody.appendChild(tr);
    }
    monthTable.appendChild(tbody);
}
function displayReport(StartDate,EndDate,MachineId,BiggerThan,LesserThan,SearchName){
    let SearchTerm = "";
    switch(SearchName){//把选择条件转为sql对应的列名
        case "作废总数": SearchTerm="Totalfail";break;
        case "严废总数": SearchTerm="Serfail";break;
        case "三仓数": SearchTerm="Psnnum";break;
    }
    $.ajax({
        url: '../php/Report.php',
        type: 'GET',
        dataType: 'JSON',
        data: {"StartDate":StartDate,"EndDate":EndDate,"MachineId":MachineId,"BiggerThan":BiggerThan,"LesserThan":LesserThan,"SearchTerm":SearchTerm},
        success: function(data) {
            if (data.GeneralResult == false) {
                $('div#compareDataChartsDiv').hide();
                alert("请重新输入查询条件");
            }
            else {
                createWeekTable(BiggerThan,LesserThan,SearchName,data);
                createMonthTable(BiggerThan,LesserThan,SearchName,data);
            }
        },
        error: function(data){
            console.log(data);
        }
    });
}
