$(document).ready(function() {
    $('#WangonTable').DataTable( {
        "scrollY":"500px",
        "scrollCollapse":true,
        "info":false,
        "paging":false,
        "language":{
            //"lengthMenu":"显示_MENU_条记录",
            //"info":"显示_PAGES_页中的第_PAGE_页",
            "loadingRecords":"正在载入中...",
            "search":"查询"
            //"paginate":{
            //"first":"第一页",
            //"last":"最后一页",
            //"next":"下一页",
            //"previous":"前一页"
            //},
        },
        "columnDefs": [
            {
                // targets用于指定操作的列，从第0列开始，-1为最后一列，这里第六列
                // return后边是我们希望在指定列填入的按钮代码
                "targets": -1,
                "render": function ( data, type, full, meta ) {
                    //return "<input type = 'button' id = 'SubButton' value = '确认选择'>"
                    return "<button class='btn btn-info' type='button' id='SubButton'>确认选择</button>"
                }
            }
        ],
        "columns": [
            { "data": "Wangon" },
            { "data": "Product" },
            { "data": "Side" },
            { "data": null}
        ],
        "data": [
            { "Wangon": "J5", "Product": "9607T","Side":"正面"},
            { "Wangon": "J6", "Product": "9607T","Side":"正面"},
            { "Wangon": "W10#1", "Product": "9607T","Side":"反面"},
            { "Wangon": "W10#2", "Product": "9607T","Side":"反面"}
        ]
    } );
    $("#WangonTable tbody").on("click", "#SubButton", function () {
        //获取行
        let row = $("table#WangonTable tr").index($(this).closest("tr"));
        //获取某列（从0列开始计数）的值
        let MachineId = $("table#WangonTable").find("tr").eq(row).find("td").eq(0).text();
        sessionStorage.MachineId=MachineId;
        window.location.href="TodayData.html";
    });
});