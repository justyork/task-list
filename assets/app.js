$('.checkbox-status').change(function () {
    $.post('/index/check', {id:$(this).attr('data-id')})
})

$('.update-text').click(function(){
    let row = $(this).parents('tr');
    let textRow = row.find('.task-text');
    if (textRow.is('[data-text]')) return;

    let text = textRow.text();
    textRow.attr('data-text', text)
    let field = '<input type="text" value="'+text+'" class="input-task-form" /> <a class="fa fa-check save-form-text"></a>'
    textRow.html(field);
});

$('.form-table').on('click', '.save-form-text', function () {
    let textRow = $(this).parent();
    let id = textRow.parents('[data-id]').attr('data-id');
    let text = textRow.find('.input-task-form').val()
    $.post('/index/edit', {id:id, text:text}, function (){
        textRow.text(text);
    });
})
