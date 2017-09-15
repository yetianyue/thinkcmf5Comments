<?php

/**
 * Author: yetianyue
 * CreateTime: 2017/8/24 12:00
 * Description: write something
 */
namespace plugins\comments\controller;
use cmf\controller\PluginBaseController;
use think\Db;
use think\Validate;

class IndexController extends PluginBaseController
{
    private  $user ;

    public function _initialize()
    {
        $this->user = cmf_get_current_user();
        if(empty($this->user))
            $this->error('请先登录!',cmf_url('user/Login/index'));
    }

    /**
     * 评论
     */
    public function ajaxComment()
    {
        if(!$this->request->isAjax())
            return ;
        $post = $this->request->post();
        $rules = [
            'content'=>'require'
        ];
        $message = [
            'content.require'=>'评论内容不能为空!'
        ];
        $va = new Validate($rules,$message);
        if(!$va->check($post))
            $this->error($va->getError());
        $user = $this->user;
        $admin = Db::name("comment")->where('user_id', $user['id'])->field("create_time")->order(["create_time" => "DESC"])->find();
        if(time()-$admin['create_time']<60)
            $this->error('1分钟内只能评论一次！');
        $post ['user_id']=$user['id'];
        $post ['full_name']= $user['user_nickname']?$user['user_nickname']:($user['user_login']?$user['user_login']:$user['mobile']);
        $post ['email']=$user['user_email'];
        $post ['create_time']=time();
        $post ['url']=$_SERVER['HTTP_REFERER'];
        $result = Db::name('comment')->insertGetId($post);
        if ($result)
        {
            $return = [
                'full_name' => $post ['full_name'],
                'avatarUrl'=> cmf_get_user_avatar_url($this->user['avatar']),
                'content'=> $post['content'],
                'create_time'=>'刚刚',
                'id'=> $result,
                'click_up'=>0
            ];
            $count = Db::name("comment")->where('object_id', $post['object_id'])->count();
            Db::name('portal_post')->where('id','eq',$post['object_id'])->update(['comment_count'=>$count]);
            $this->success('评论成功!',null,$return);
        }
        else
        {
            $this->error('评论失败！');
        }
    }

    /**
     * 点赞
     */
    public function ajaxDoClick()
    {
        if(!$this->request->isAjax())
            return ;
        $post = $this->request->post();
        $result = Db::name('comment')->where('id','eq',$post['id'])->setInc('click_up',1);
        if ($result)
            $this->success('点赞成功!');
        else
            $this->error('点赞失败！');

    }
}