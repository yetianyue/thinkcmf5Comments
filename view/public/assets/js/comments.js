
COMMENT_LIST.html(buildCommentHtml(COMMENT_INFO));
   //文章页面评论foucus
$('#comentContent').focus(function (e) {
    $('#inputCommentBox').addClass('textarea-focus')
    $(this).addClass('textarea-focus');
    $('#buttomBoxTop').addClass('button-box-top-focus');
    $('#commentButtom').height(50);
    $('.replay-comment-box').remove();
});
//错误信息输出
function commentLayer(msg){
    $('#commentLayer').html(msg).show();
    setTimeout(function () {
        $('#commentLayer').fadeOut();
    },2000);
}
//评论
$('#buttomBoxTop').click(function (e) {
    var data = $('#inputComment').serialize();
    ajaxComment(data,function (res) {
        COMMENT_LIST.prepend(res);
    });
});

//加载更多
$('#loadMoreComment').click(function(e){
    COMMENT_LIST.removeAttr('style');
    changeLoadMore();
});
function changeLoadMore() {
    $('#loadMoreComment').hide();
    $('#noMoreComment').show();
}

function buildCommentHtml(data){
    var lis = '';
    $.each(data,function(n,m){
        lis += buildCommentliHtml(m);
        if(LI_NUMS > 5 ){
            COMMENT_LIST.height('580');
            COMMENT_LIST.css('overflow','hidden');
            $('#loadMoreComment').show();
        }
    });
    return lis;    
}

//构建评论的HTML
function buildCommentliHtml(data) {
    var li = '<li class="clear">';
    li += '<a href="javascrip:void(0);" class="comment-image pull-left"><img src="'+data.avatarUrl+'"/></a><div class="comment-content"><div class="comment-user-info"><a href="javascrip:void(0);" class="comment-user-name">'+data.full_name+'</a><span class="comment-user-time">'+data.create_time+'</span></div><p class="comment-content-p">'+data.content+'</p><div class="comment-footer clear"><span class="replay-comment" onclick="return mkReComment(this,'+data.id+'); ">回复</span><span class="comment-action pull-right" onclick="return doClick(this,'+data.id+'); "><em>'+data.click_up+'</em><i class="glyphicon glyphicon-thumbs-up"></i></span></div><ul class="comment-list clear">';
    if (data.childs) {
        li += buildCommentHtml(data.childs);
    }
    li += '</ul></div></li>';
    LI_NUMS += 1;
    return li;
}
//构建回复的HTML
function buildReCommentHtml(parent_id) {
    return '<form class="form-recoment clear" id="form-recoment"><div class="input-box pull-left replay-comment-box" id="reCommentBox"><div class="textarea-box"><input type="hidden" name="parent_id"  value="'+parent_id+'"/><input type="hidden" name="table_name" id="tablename" value="{$post_table}" /><input type="hidden" name="object_id"  value="{$post_id}"/><textarea name="content"  class="mytextarea " placeholder="写下您的回复..."onfocus="return recommentFocus(this);"></textarea></div><div class="button-box-top " ><div class="comment-button" style="height: 55px;" id="recommentButton" onclick="return doRecomment(this);">评论</div></div></div></form>';
}
//点赞事件
function doClick(obj,id) {
    var div = $(obj);
    var em = div.find('em');
    var num = parseInt(em.html());
    $.ajax({
        url:PLUGIN_CLICK_URL,
        type:'post',
        data:{id:id},
        dataType:'json',
        beforeSend:function(res){
            em.html(num+1);
            div.css('color','#cacaca');
            div.removeAttr('onclick');
        },
        success:function(res){
            if(res.code != 1){
                em.html(num);
                div.css('color','#ed4040');
                div.attr('onclick','return doClick(this,'+id+')');
                commentLayer(res.msg);
            }
        }
    })
}

//生成回复的输入框
function mkReComment(obj,parent_id) {
    $('#inputCommentBox').removeClass('textarea-focus')
    $('#comentContent').removeClass('textarea-focus');
    $('#buttomBoxTop').removeClass('button-box-top-focus');
    $('#commentButtom').height(55);

    $('#form-recoment').remove();
    $(obj).parent().after(buildReCommentHtml(parent_id));
}

//回复的别人获取焦点事件
function recommentFocus(obj){
    $('#reCommentBox').addClass('textarea-focus');
    $(obj).addClass('textarea-focus');
    $('#recommentButton').parent().addClass('button-box-top-focus');
    $('#recommentButton').height(50);
}

//回复别人
function doRecomment() {
    var data = $('#form-recoment').serialize();
    ajaxComment(data,function (res) {
        $('#form-recoment').parent().parent().find('ul.comment-list').after(res);
        $('#form-recoment').remove();
    });
}

//评论AJAX
function ajaxComment(data,callback){
    $.ajax({
        url:PLUGIN_COMMENT_URL,
        type:'post',
        data:data,
        dataType:'json',
        success:function(res){
            if(res.code != 1){
                commentLayer(res.msg);
            }else{
                data = $.extend(data,USER_INFO,res.data);
                data.time = "刚刚";
                callback(buildCommentliHtml(data));
                var comments = parseInt($('.comment-header em').html())+1;
                $('.comment-header em').html(comments);
                $('a.comment-area-a span').html(comments);
            }
        }
    });
}