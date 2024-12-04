function loadData() {
    var search = $('#search-form input[name="search"]').val();
    var pageSize = $('#pageSize-form select[name="pageSize"]').val();
    var page = $('#goPage').val();
    $.pjax({
        url: loadPageUrl,
        container: '#data-grid-' + pageId,
        type: 'GET',
        data: {
            pageId,
            page,
            search,
            pageSize,
        },
        push: false,
        headers: {
            'X-CSRF-Token': $('meta[name="csrf-token"]').attr('content')
        },
        timeout: 5000,
    });
}
