const hostName = window.location.hostname;
const hostNameServerUrl = '/task/';

$(document).ready(function() {
    let edit = false;
    console.log('jquery is working!');
    fetchTasks();
    $('#task-result').hide();
    var url = window.location.href;
    var searchValue = new URL(url).searchParams.get("search");
    $(function() {
        if (searchValue) {
            $('#search').val(searchValue).trigger("keyup");
        }
    });
    // 검색 기능
    $('#search').keyup(function() {
        if ($('#search').val()) {
            let search = $('#search').val();
            $.ajax({
                url: hostNameServerUrl + 'task-search.php',
                data: { search },
                type: 'POST',
                success: function(response) {
                    if (!response.error) {
                        let tasks = JSON.parse(response);
                        let template = '';
                        tasks.forEach(task => {
                            template += `
                                <li taskId="${task.idx}">
                                    <a href="#" class="task-item">${task.task_name}</a>
                                </li>
                            `;
                        });
                        $('#task-result').show();
                        $('#container').html(template);
                    }
                }
            });
        }
    });
    // 등록 및 수정 처리
    $('#task-form').submit(e => {
        e.preventDefault();
        const postData = {
            task_name: $('#name').val(),
            task_description: $('#description').val(),
            idx: $('#taskId').val()
        };
        const url = edit === false ? 'task-add.php' : 'task-edit.php';
        console.log(postData, url);
        $.post(hostNameServerUrl + url, postData, (response) => {
            console.log(response);
            $('#task-form').trigger('reset');
            fetchTasks();
        });
    });
    // 목록 불러오기
    function fetchTasks() {
        $.ajax({
            url: hostNameServerUrl + 'tasks-list.php',
            type: 'GET',
            dataType: "json",
            success: function(tasks) {
                if (tasks.result == "ok") {
                    const list = tasks.tasks;
                    let template = '';
                    list.forEach(task => {
                        template += `
                            <tr taskId="${task.idx}">
                                <td>${task.idx}</td>
                                <td><a href="#" class="task-item">${task.task_name}</a></td>
                                <td>${task.task_description}</td>
                                <td>${task.task_datetime}</td>
                                <td>
                                    <button class="task-delete btn btn-danger" data-taskId="${task.idx}">Delete</button>
                                </td>
                            </tr>
                        `;
                    });
                    $('#tasks').html(template);
                    $('.user-name').remove();
                    $(".table-bordered").before("<p class='user-name'>" + tasks.username + "님의 할일</p>");
                } else {
                    alert(tasks.msg);
                    window.location.href = "/login.html";
                }
            }
        });
    }
    // 폼 초기화
    function initForm() {
        edit = false;
        $('#name').val("");
        $('#description').val("");
        $('#taskId').val("");
        $('#task-result').hide();
    }
    // 단일 Task 조회
    $(document).on('click', '.task-item', (e) => {
        e.preventDefault();
        var element = $(this)[0].activeElement.parentElement.parentElement;
        var idx = $(element).attr('taskId');
        if (!idx) {
            element = $(this)[0].activeElement.parentElement;
            idx = $(element).attr('taskId');
        }
        if (idx) {
            $.post(hostNameServerUrl + 'task-single.php', { idx }, (response) => {
                const task = JSON.parse(response);
                $('#name').val(task.task_name);
                $('#description').val(task.task_description);
                $('#taskId').val(task.idx);
                edit = true;
            });
        }
    });
    // Task 삭제
    $(document).on('click', '.task-delete', (e) => {
        if (confirm('Are you sure you want to delete it?')) {
            const element = $(this)[0].activeElement.parentElement.parentElement;
            const idx = $(element).attr('taskId');
            $.post(hostNameServerUrl + 'task-delete.php', { idx }, (response) => {
                if (response == 'Task Deleted Successfully') {
                    initForm();
                    fetchTasks();
                    $("#search").trigger("keyup");
                }
                alert(response);
            });
        }
    });
});