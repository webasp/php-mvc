var header = new Headroom(document.getElementById("header"), {
    tolerance: 10,
    offset : 80,
    classes: {
        initial: "animated",
        pinned: "slideDown",
        unpinned: "slideUp"
    }
});
header.init();

$('#search-inp').keypress(function (e) {
    var key = e.which;
    if (key == 13) {
        var q = $(this).val();
        if (q && q != '') {
            window.location.href = '/search/' + q;
        }
    }
});

InstantClick.on('change', function (isInitialLoad) {
    var blocks = document.querySelectorAll('pre code');
    for (var i = 0; i < blocks.length; i++) {
        hljs.highlightBlock(blocks[i]);
    }
    if (isInitialLoad === false) {
        if (typeof ga !== 'undefined') ga('send', 'pageview', location.pathname + location.search);
    }
});
InstantClick.init();

/* 提交留言 */
$("#comment-form").submit(function () {
    $(this).ajaxSubmit({
        dataType:  'json',
        success: function(data) {
            if(data.success){
                $("#reply_id").val('');
                $("#name").val('');
                $("#content").val('');
                alert(data.message);
                return false;
            }else{
                alert(data.message);
                return false;
            }
        },
        error:function(xhr){
            alert('发生错误了啊');
            return false;
        }
    });
    return false;
});


