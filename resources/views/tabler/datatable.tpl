<link href="//cdn.datatables.net/v/bs5/dt-2.0.4/datatables.min.css" rel="stylesheet"/>
<script src="//cdn.datatables.net/v/bs5/dt-2.0.4/datatables.min.js"></script>

<script>
    let tableConfig = {
        autoWidth: false,
        iDisplayLength: 10,
        scrollX: true,
        columns: [
            {foreach $details['field'] as $key => $value}
            {
                data: '{$key}'
            },
            {/foreach}
        ],
        initComplete: function () {
            let tableHeader = $('div.dt-length').parent().parent()
            let tableBody = $('div.dt-scroll').parent().parent()
            let tableFooter = $('div.dt-info').parent().parent()
            let length = $('div.dt-length').parent()
            let search = $('div.dt-search').parent()
            let info = $('div.dt-info').parent()
            let paging = $('div.dt-paging').parent()

            tableHeader.removeClass('mt-2').addClass('row px-3 py-3')
            tableBody.removeClass('mt-2')
            tableFooter.removeClass('mt-2').addClass('row card-footer')
            length.removeClass('col-md-auto me-auto').addClass('col-auto')
            search.removeClass('col-md-auto me-auto ms-auto').addClass('col-auto')
            info.removeClass('col-md-auto me-auto').addClass('col')
            paging.removeClass('col-md-auto me-auto ms-auto').addClass('col-auto')

            $('div.dt-scroll-body').css('border-bottom-style', 'none')
        },
        language: {
            "sProcessing": "处理中...",
            "sLengthMenu": "显示 _MENU_ 条",
            "sZeroRecords": "没有匹配结果",
            "sInfo": "第 _START_ 至 _END_ 项结果，共 _TOTAL_ 项",
            "sInfoEmpty": "第 0 至 0 项结果，共 0 项",
            "sInfoFiltered": "(在 _MAX_ 项中查找)",
            "sInfoPostFix": "",
            "sSearch": "<i class=\"ti ti-search\"></i> ",
            "sUrl": "",
            "sEmptyTable": "表中数据为空",
            "sLoadingRecords": "载入中...",
            "sInfoThousands": ",",
            "oPaginate": {
                "sFirst": "首页",
                "sPrevious": "<i class=\"ti ti-arrow-left\"></i>",
                "sNext": "<i class=\"ti ti-arrow-right\"></i>",
                "sLast": "末页"
            },
            "oAria": {
                "sSortAscending": ": 以升序排列此列",
                "sSortDescending": ": 以降序排列此列"
            }
        }
    };
</script>
