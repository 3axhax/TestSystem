window.addEventListener('load', function () {
    $('#generate_table').on('click', generateTable);
    $('#emulate_test').on('click', emulateTest);
});

function generateTable() {
    let sent_data = new FormData();
    min_dif = $('#min_difficulty');
    max_dif = $('#max_difficulty');
    sent_data.append('action', 'generate_table');
    sent_data.append('min_dif', min_dif.val());
    sent_data.append('max_dif', max_dif.val());
    $.ajax({
        url: '/ajax.php',
        type: 'POST',
        processData: false,
        contentType: false,
        data: sent_data,
        success: function (data) {
            try {
                data = JSON.parse(data);
                if (data.success) {
                    max_dif.val(data.max_dif);
                    min_dif.val(data.min_dif);
                    showReport(data.success, 'success');

                }
                if (data.error) {
                    showReport(data.error, 'danger');
                }
            }
            catch (e) {
                console.log(e);
            }
        },
        error: function (data) {
            console.log(data);
        },
    });
}

function emulateTest() {
    let sent_data = new FormData();
    user_int = $('#user_intelligence');
    sent_data.append('action', 'emulate_test');
    sent_data.append('user_int', user_int.val());
    $.ajax({
        url: '/ajax.php',
        type: 'POST',
        processData: false,
        contentType: false,
        data: sent_data,
        success: function (data) {
            try {
                data = JSON.parse(data);
                if (data.success) {
                    fillTable(data.table);
                    $('#test_result').html('Result: ' + data.result);
                    showReport(data.success, 'success');

                }
                if (data.error) {
                    showReport(data.error, 'danger');
                }
            }
            catch (e) {
                console.log(e);
            }
        },
        error: function (data) {
            console.log(data);
        },
    });
}

function showReport (message, style) {
    if (style === undefined) {
        style = 'dark';
    }
    let modal_report = $('#modal_report');
    let modal_body = $('#modal_body');
    modal_body.html('<span class="text-' + style + '">' + message + '</span>');
    modal_report.modal('show');
}

function fillTable(data) {
    result_table_body = $('#test_details > tbody');
    result_table_body.html('');
    temple = '<tr>' +
        '<td>:NUM:</td><td>:QUESTION_ID:</td><td>:TESTS_COUNT:</td><td>:QUESTION_DIFFICULT:</td><td>:ANSWER:</td>' +
        '</tr>';
    for (i = 0; i < data.length; i++) {
        answer = (data[i]['answer'] == 1) ? '<span class="text-success">YES</span>' : '<span class="text-danger">NO</span>'
        result_table_body.append(
            temple.replace(':NUM:', i+1)
                .replace(':QUESTION_ID:', data[i]['question_id'])
                .replace(':TESTS_COUNT:', data[i]['uses_count'])
                .replace(':QUESTION_DIFFICULT:', data[i]['question_difficult'])
                .replace(':ANSWER:', answer)
        );
    }

}