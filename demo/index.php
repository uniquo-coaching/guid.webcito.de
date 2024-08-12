<?php
$action = filter_input(INPUT_GET, 'action');
if (!empty($action)) {
    require '../vendor/autoload.php';
    if ($action === 'tableGuids') {
        {
            $values = filter_input_array(INPUT_GET, [
                'count' => FILTER_VALIDATE_INT,
                'lower' => FILTER_VALIDATE_BOOL
            ]);
            $count = min(1000, max(1, $values["count"] ?? 1));
            $return = [];
            $ids = GUID::generate(count: $count, lowerCase: $values['lower']);
            if (!is_array($ids)) {
                $ids = [$ids];
            }
            foreach ($ids as $id) {
                $return[] = ["guid" => $id];
            }

            try {
                echo json_encode(["rows" => $return, "total" => $count], JSON_THROW_ON_ERROR);
            } catch (JsonException $e) {
                trigger_error(message: $e->getTraceAsString(), error_level: E_USER_WARNING);
            }
        }
    }
    exit;
}

?>
<!doctype html>
<html lang="en" dir="ltr" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP class GUID</title>
    <link href="../vendor/twbs/bootstrap/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../vendor/twbs/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../vendor/wenzhixin/bootstrap-table/dist/bootstrap-table.min.css" rel="stylesheet">
</head>
<body>
<main class="container py-5">
    <h1 class="text-center">GUID::class</h1>

    <nav class="navbar d-flex justify-content-center">
        <a class="fs-1 btn btn-link" href="https://github.com/ThomasDev-de/php-guid" target="_blank">
            <i class="bi bi-github"></i>
        </a>
        <a class="fs-1 btn btn-link" href="https://getbootstrap.com" target="_blank">
            <i class="bi bi-bootstrap"></i>
        </a>
        <a class="fs-1 btn btn-link" href="https://bootstrap-table.com" target="_blank">
            <i class="bi bi-bootstrap-fill"></i>
        </a>
    </nav>


    <main class="d-flex justify-content-center">
        <div id="toolbar_table_guids" class="d-flex flex-column">
            <div class="form-floating mb-3">
                <select class="form-select" id="selectCountGuids">
                    <option value="1">1</option>
                    <option selected value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="500">500</option>
                    <option value="1000">1000</option>
                </select>
                <label for="selectCountGuids">How many GUIDs should be shown</label>
            </div>

            <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" role="switch" id="switchFilterOnlyLowerCase">
                <label class="form-check-label" for="switchFilterOnlyLowerCase">in lowercase, otherwise in uppercase letters</label>
            </div>
        </div>
        <table id="table_guids" data-url="?action=tableGuids" data-toolbar="#toolbar_table_guids"></table>
    </main>
</main>
<script src="../vendor/components/jquery/jquery.min.js"></script>
<script src="../vendor/twbs/bootstrap/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/tableExport.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/jsPDF/jspdf.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/tableexport.jquery.plugin@1.10.21/libs/jsPDF-AutoTable/jspdf.plugin.autotable.js"></script>
<script src="../vendor/wenzhixin/bootstrap-table/dist/bootstrap-table.min.js"></script>
<script src="../vendor/wenzhixin/bootstrap-table/dist/extensions/export/bootstrap-table-export.min.js"></script>
<script>
    $(function () {
        $.fn.pageIndex = function () {
            const body = $(this),
                vars = {
                    copiedGuids: []
                },
                tables = {
                    guids: '#table_guids'
                },
                switches = {
                    filterLowerCase: '#switchFilterOnlyLowerCase'
                },
                selects = {
                    filterCount: '#selectCountGuids'
                };

            function initTables() {
                $(tables.guids).bootstrapTable({
                    classes: 'table table-bordered table-secondary',
                    pagination: true,
                    sidePagination: 'client',
                    showHeader: false,
                    showRefresh: true,
                    showExport: true,
                    exportDataType: 'all',
                    exportTypes: ['json', 'xml', 'csv', 'txt', 'sql', 'excel', 'pdf'],
                    exportOptions: {
                        fileName: function () {
                            return 'guids_generated'
                        }
                    },
                    queryParams: p => {
                        p.count = 50;
                        p.lower = $(switches.filterLowerCase).is(':checked');
                        p.count = $(selects.filterCount).val();
                        return p;
                    },
                    onPostBody: () => {
                        $('.tooltip.show').remove();
                        $(tables.guids).find('[data-bs-toggle="tooltip"]').tooltip({
                            container: body,
                            trigger: 'hover'
                        });
                    },
                    columns: [{
                        field: 'guid',
                        title: 'guid',
                        class: 'fw-bold',
                        formatter: guid => {
                            if ($.inArray(guid, vars.copiedGuids) > -1) {
                                return `<s class="text-muted">${guid}</s>`;
                            }
                            return guid;
                        }
                    }, {
                        width: 10,
                        field: 'copy',
                        align: 'center',
                        forceHide: true,
                        formatter: (value, row) => {
                            if ($.inArray(row.guid, vars.copiedGuids) === -1) {
                                return `<a href="#" class="btn btn-light btn-sm js-copy" data-bs-toggle="tooltip" title="Copy to clipboard"><i class="bi bi-clipboard"></i></a>`;
                            }
                            return '<i class="bi bi-clipboard-check text-success"></i>';
                        },
                        events: {
                            'click .js-copy': (e, val, row, index) => {
                                e.preventDefault();
                                navigator.clipboard.writeText(row.guid);
                                vars.copiedGuids.push(row.guid);
                                const td = $(tables.guids).find(`tbody tr[data-index="${index}"] td:first`);
                                td.html(`<s class="text-muted">${td.text()}</s>`);
                                $('.tooltip.show').remove();
                                $(e.currentTarget).replaceWith('<i class="bi bi-clipboard-check text-success"></i>');
                                if (!$(tables.guids).find('.js-copy').length) {
                                    $(tables.guids).bootstrapTable('nextPage');
                                }
                            }
                        }
                    }]
                });
            }

            function events() {
                body
                    .on('change', `${switches.filterLowerCase}, ${selects.filterCount}`, function () {
                        $(tables.guids).bootstrapTable('refresh', {silent: true, pageNumber: 1});
                    })
            }

            function init() {
                initTables();
                events();
                return body;
            }

            return init();
        };
        $('body').pageIndex();
    });
</script>
</body>
</html>
