$(document).ready(function() {
    let machineId=sessionStorage.MachineId;
    $.ajax({
        url: '../php/TodayData.php',
        type: 'GET',
        dataType: 'JSON',
        data: {"machineId":machineId},
        success: function(data){
            let conFailTable = document.getElementById('conFailTable');
            let typFailTable = document.getElementById('typFailTable');
            document.getElementById("wagonName").innerText = data.WagonName;
            document.getElementById("TotalFail").innerText = data.LastWagonGenFail['TotalFail'];
            document.getElementById("SerFail").innerText = data.LastWagonGenFail['SerFail'];
            document.getElementById("PsnNum").innerText = data.LastWagonGenFail['PsnNum'];
            document.getElementById("MaxM").innerText = data.LastWagonGenFail['MaxK'];
            document.getElementById("MaxK").innerText = data.LastWagonGenFail['MaxM'];
            //添加generalfail表内容
            if(data.LastWagonConFail===0) {
                $('table#conFailTable').find('thead').detach();
                $('table#conFailTable').find('tbody').empty();
                let thead = document.createElement('thead');
                let tr = document.createElement('tr');
                let th = document.createElement('th');
                th.setAttribute("class","text-center");
                th.appendChild(document.createTextNode("本车无连续废"));
                tr.appendChild(th);
                thead.appendChild(tr);
                conFailTable .appendChild(thead);
            }
            //当上车没有连续废时清除掉表头和表内所有内容
            else {
                $('table#conFailTable').find('thead').detach();
                $('table#conFailTable').find('tbody').detach();
                let tableHead = ["编号","连续废张数","起始大张编号","末尾大张编号","连续废区域","连续废所在列","起始图像","末尾图像"];
                let tbody = document.createElement('tbody');
                let thead = document.createElement('thead');
                let tr = document.createElement('tr');
                for (let i = 0; i < tableHead.length; i++) {
                    headerTxt = document.createTextNode(tableHead[i]);
                    th = document.createElement('th');
                    th.setAttribute("class","text-center");
                    if(i===6||i===7){   //第7和第8列用来显示图片，单独指定列宽
                        th.setAttribute("width","180px");
                    }
                    th.appendChild(headerTxt);  //添加内容
                    tr.appendChild(th); //添加单独的列单元
                    thead.appendChild(tr);  //添加整行
                }
                conFailTable .appendChild(thead);   //添加表头
                for (let i = 0; i < data.LastWagonConFail.length; i++) {
                    let tr = document.createElement('tr');
                    for (let temp =0; temp<8;temp++){
                        tr.appendChild(document.createElement('td'));
                    }
                    tr.cells[0].appendChild(document.createTextNode(i + 1));
                    tr.cells[1].appendChild(document.createTextNode(data.LastWagonConFail[i].ConNum));
                    tr.cells[2].appendChild(document.createTextNode(data.LastWagonConFail[i].StartPsn));
                    tr.cells[3].appendChild(document.createTextNode(data.LastWagonConFail[i].EndPsn));
                    tr.cells[4].appendChild(document.createTextNode(data.LastWagonConFail[i].ConArea));
                    tr.cells[5].appendChild(document.createTextNode(data.LastWagonConFail[i].ConCol));
                    let image1 = document.createElement('img');
                    let image2 = document.createElement('img');
                    image1.src = "data:image/bmp;base64," + data.LastWagonConFail[i].Image1;
                    image2.src = "data:image/bmp;base64," + data.LastWagonConFail[i].Image2;
                    tr.cells[6].appendChild(image1);
                    tr.cells[7].appendChild(image2);
                    tr.cells[6].setAttribute("width","180px");
                    tr.cells[7].setAttribute("width","180px");
                    tbody.appendChild(tr);
                }
                conFailTable .appendChild(tbody);//添加表体内容
            }
            if(data.LastWagonTypFail!==0){
                $('table#typFailTable').find('tbody').detach();
                let tbody = document.createElement('tbody');
                for (let i = 0; i < data.LastWagonTypFail.length; i++) {
                    let tr = document.createElement('tr');
                    for (let temp =0; temp<5;temp++){
                        tr.appendChild(document.createElement('td'));
                    }
                    tr.cells[0].appendChild(document.createTextNode(data.LastWagonTypFail[i].Max_Pos));
                    tr.cells[1].appendChild(document.createTextNode(data.LastWagonTypFail[i].Max_Area));
                    tr.cells[2].appendChild(document.createTextNode(data.LastWagonTypFail[i].Max_Num));
                    tr.cells[3].appendChild(document.createTextNode(data.LastWagonTypFail[i].Avg_Dim));
                    let image = document.createElement('img');
                    image.src = "data:image/bmp;base64," + data.LastWagonTypFail[i].Image;
                    tr.cells[4].appendChild(image);
                    tr.cells[4].setAttribute("width","180px");
                    tbody.appendChild(tr);
                }
                typFailTable.appendChild(tbody);
            }
            },
        error: function(data){
        console.log(data);
    }
});
});