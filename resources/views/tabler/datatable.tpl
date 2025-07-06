<link href="//cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.css" rel="stylesheet"/>
<script src="//cdn.datatables.net/v/bs5/dt-2.0.8/datatables.min.js"></script>

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
            $('div.dt-length').parent().parent().removeClass('mt-2').addClass('row px-3 py-3')
            $('div.dt-scroll').parent().parent().removeClass('mt-2')
            $('div.dt-info').parent().parent().removeClass('mt-2').addClass('row card-footer')
            $('div.dt-length').parent().removeClass('col-md-auto me-auto').addClass('col-auto')
            $('div.dt-search').parent().removeClass('col-md-auto me-auto ms-auto').addClass('col-auto')
            $('div.dt-info').parent().removeClass('col-md-auto me-auto').addClass('col')
            $('div.dt-paging').parent().removeClass('col-md-auto me-auto ms-auto').addClass('col-auto')
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
