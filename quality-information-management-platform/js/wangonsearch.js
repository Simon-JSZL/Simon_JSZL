$(document).ready(function() {
    let WagonName=sessionStorage.WagonName;
    let Procedure=sessionStorage.Procedure;
    if(WagonName!=="") {
        wangonSearch(WagonName,Procedure);
    }
});
function detailView(WagonName,Procedure){
    if(WagonName===""&&sessionStorage.WagonName===""){
        $('div#wangonSearchTablesDiv').hide();
        alert("请输入车号");
    }
    else if(Procedure===""&&sessionStorage.Procedure===""){
        $('div#wangonSearchTablesDiv').hide();
        alert("请选择面次");
    }
    else{
        if(Procedure!==""&&WagonName!==""){
            if(Procedure==='凹印正面')
                Procedure=1;
            else if(Procedure==='凹印背面')
                Procedure=0;
            sessionStorage.WagonName=WagonName;
            sessionStorage.Procedure=Procedure;
        }
            let height=window.screen.height*0.1;
            let width=window.screen.width*0.15;
            window.open('DetailView.html', 'DetailView','height=560,width=1000,top='+height+',left='+width+',location=no,resizable=no,toolbar=no');
    }
}
function wangonSearch(WagonName,Procedure) {
    let wangonName=WagonName;
    let procedure=Procedure;
    if(procedure===""){
        $('div#wangonSearchTablesDiv').hide();
        alert("请选择面次");
    }
    else if(wangonName===""){
        $('div#wangonSearchTablesDiv').hide();
        alert("请输入车号");
    }
    else if(procedure!==""&&wangonName!==""){
    if(procedure==='凹印正面')
         procedure=1;
    else if(procedure==='凹印背面')
         procedure=0;
        sessionStorage.WagonName=wangonName;
        sessionStorage.Procedure=procedure;
    $.ajax({
        url: '../php/WangonSearch.php',
        type: 'GET',
        dataType: 'JSON',
        data: {"wangonName":wangonName,"procedure":procedure},
        success: function(data){
            if(data===0) {
                $('div#wangonSearchTablesDiv').hide();
                alert("未查询到车次:"+wangonName+"请重新录入信息");
            }
            else {
                $('div#wangonSearchTablesDiv').show();
                document.getElementById("WagonName").innerText = wangonName;
                document.getElementById("MachineId").innerText = data.WangonInfo.MachineId;
                document.getElementById("CreateTime").innerText = data.WangonInfo.CreateTime;
                document.getElementById("SideId").innerText = (data.WangonInfo.SideId===1)?'正面':'背面';
                document.getElementById("ProductId").innerText = data.WangonInfo.ProductId;
                document.getElementById("TotalFail").innerText = data.GeneralFail.TotalFail;
                document.getElementById("SerFail").innerText = data.GeneralFail.SerFail;
                document.getElementById("PsnNum").innerText = data.GeneralFail.PsnNum;
                document.getElementById("MaxM").innerText = data.GeneralFail.MaxM;
                document.getElementById("MaxK").innerText = data.GeneralFail.MaxK;
                let conFailTable = document.getElementById('conFailTable');
                let typFailTable = document.getElementById('typFailTable');
                if(data.TypFail!==0){
                    $('table#typFailTable').find('tbody').detach();
                    let tbody = document.createElement('tbody');
                    for (let i = 0; i < data.TypFail.length; i++) {
                        let tr = document.createElement('tr');
                        tr.appendChild(document.createElement('td'));
                        tr.appendChild(document.createElement('td'));
                        tr.appendChild(document.createElement('td'));
                        tr.appendChild(document.createElement('td'));
                        tr.appendChild(document.createElement('td'));
                        tr.cells[0].appendChild(document.createTextNode(data.TypFail[i].Max_Pos));
                        tr.cells[1].appendChild(document.createTextNode(data.TypFail[i].Max_Area));
                        tr.cells[2].appendChild(document.createTextNode(data.TypFail[i].Max_Num));
                        tr.cells[3].appendChild(document.createTextNode(data.TypFail[i].Avg_Dim));
                        let image = document.createElement('img');
                        image.src = "data:image/bmp;base64," + data.TypFail[i].Image;
                        tr.cells[4].appendChild(image);
                        tr.cells[4].setAttribute("width","180px");
                        tbody.appendChild(tr);
                    }
                    typFailTable.appendChild(tbody);
                }
                if(data.ConFail===0) {
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
                        if(i===6||i===7){
                            th.setAttribute("width","180px");
                        }
                        th.appendChild(headerTxt);
                        tr.appendChild(th);
                        thead.appendChild(tr);
                    }
                    conFailTable .appendChild(thead);
                    for (let i = 0; i < data.ConFail.length; i++) {
                        let tr = document.createElement('tr');
                        tr.appendChild(document.createElement('td'));
                        tr.appendChild(document.createElement('td'));
                        tr.appendChild(document.createElement('td'));
                        tr.appendChild(document.createElement('td'));
                        tr.appendChild(document.createElement('td'));
                        tr.appendChild(document.createElement('td'));
                        tr.appendChild(document.createElement('td'));
                        tr.appendChild(document.createElement('td'));
                        tr.cells[0].appendChild(document.createTextNode(i + 1));
                        tr.cells[1].appendChild(document.createTextNode(data.ConFail[i].ConNum));
                        tr.cells[2].appendChild(document.createTextNode(data.ConFail[i].StartPsn));
                        tr.cells[3].appendChild(document.createTextNode(data.ConFail[i].EndPsn));
                        tr.cells[4].appendChild(document.createTextNode(data.ConFail[i].ConArea));
                        tr.cells[5].appendChild(document.createTextNode(data.ConFail[i].ConCol));
                        let image1 = document.createElement('img');
                        let image2 = document.createElement('img');
                        image1.src = "data:image/bmp;base64," + data.ConFail[i].Image1;
                        image2.src = "data:image/bmp;base64," + data.ConFail[i].Image2;
                        tr.cells[6].appendChild(image1);
                        tr.cells[7].appendChild(image2);
                        tr.cells[6].setAttribute("width","180px");
                        tr.cells[7].setAttribute("width","180px");
                        tbody.appendChild(tr);
                    }
                    conFailTable .appendChild(tbody);
                }
            }},
        error: function(data){
            console.log(data);
        }
    });
}}