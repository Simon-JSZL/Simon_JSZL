function offLineSearch(WagonName) {
    if (WagonName === "") {
        $('div#offLineSearchTablesDiv').hide();
        alert("请输入车号");
    }
    else {
        $.ajax({
            url: '../php/OffLineSearch.php',
            type: 'GET',
            dataType: 'JSON',
            data: {"WagonName": WagonName},
            success: function (data) {
                if (data === 0) {
                    $('div#offLineSearchTablesDiv').hide();
                    alert("未查询到相关车次信息,请重新输入车号");
                }
                else{
                    let offLineSearchTable = document.getElementById('offLineSearchTable');
                    let tableHead = [];
                    let tbody = document.createElement('tbody');
                    let thead = document.createElement('thead');
                    let tr = document.createElement('tr');
                    $('table#offLineSearchTable').find('thead').detach();
                    $('table#offLineSearchTable').find('tbody').detach();
                    $('div#offLineSearchTablesDiv').show();

                    document.getElementById('MachineId').innerText = data.MachineId;
                    document.getElementById('AverageScore').innerText = data.AverageScore;


                    tableHead.push('编号');   //表头第一行为编号，不在返回的json数组中，需要在之前单独定义
                    for (let i = 1; i <= (Object.keys(data[1]).length) / 2; i++) {//js获取数组元素为整形的数组长度时不要用.length
                        tableHead.push(data[1]['item' + i])                         //使用Object.keys(data).length
                    }
                    tableHead.push('总分');   //表头最后一行是总分，不在返回的json数组中，需要在最后单独定义


                    let col_num = tableHead.length;


                    for (let i = 0; i < col_num; i++) {//将生成的表头内容填充至表头中
                        headerTxt = document.createTextNode(tableHead[i]);
                        th = document.createElement('th');
                        th.setAttribute("class", "text-center");
                        th.appendChild(headerTxt);
                        tr.appendChild(th);
                        thead.appendChild(tr);
                    }
                    offLineSearchTable.appendChild(thead);//附加表头

                    for (let i = 1; i < Object.keys(data).length - 1; i++) {
                        let tr = document.createElement('tr');
                        tr.appendChild(document.createElement('td'));
                        tr.cells[0].appendChild(document.createTextNode(i));    //第一行为编号，即为i


                        for (let j = 1; j < col_num - 1; j++) {    //colnum-1表示除去了第一行和最后一行
                            tr.appendChild(document.createElement('td'));
                            tr.cells[j].appendChild(document.createTextNode(data[i]['score' + j]));
                        }

                        tr.appendChild(document.createElement('td'));
                        tr.cells[col_num - 1].appendChild(document.createTextNode(data[i]['totalscore']));//最后一行插入总分


                        tbody.appendChild(tr);
                    }
                    offLineSearchTable.appendChild(tbody);
                }
            },
            error: function (data) {
                console.log(data);
            }

        });
    }
}