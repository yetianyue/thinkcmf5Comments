<?php

/**
 * Author: yetianyue
 * CreateTime: 2017/8/23 16:26
 * Description: write something
 */
namespace plugins\comments;
use cmf\lib\Plugin;
use plugins\comments\lib\Comments;
use think\Db;



class CommentsPlugin extends  Plugin
{

    public $info = [
        'name'        => 'comments',
        'title'       => '评论管理',
        'description' => '评论管理',
        'status'      => 1,
        'author'      => 'yetianyue',
        'version'     => '1.0',
        'demo_url'    => 'http://www.iamfoodie.cn',
        'author_url'  => 'http://www.iamfoodie.cn'
    ];

    public $hasAdmin = 0;

    public function install()
    {
        $prefix = config('database.prefix');
        //增加点赞数据
        $sql = "ALTER TABLE {$prefix}comment ADD click_up INT NOT NULL DEFAULT 0 COMMENT '点赞数据'";
        Db::execute($sql);
        return true;
    }

    public function uninstall()
    {
        $prefix = config('database.prefix');
        //增加点赞数据
        $sql = "ALTER TABLE {$prefix}comment DROP COLUMN click_up";
        Db::execute($sql);
        return true;
    }

    public function comment($params){
        $join   = [
            ['__USER__ u', 'a.user_id = u.id']
        ];
        $where = [];
        $where['status'] = 1;
        $where['object_id'] = $params['post_id'];
        $where['delete_time'] = 0;
        $commentCount = Db::name('comment')->where($where)->count();
        $comments = Db::name('comment')
            ->field('a.content,a.create_time,a.id,a.full_name,a.parent_id,a.click_up,u.avatar')
            ->alias('a')->join($join)
            ->where($where)
            ->order("click_up DESC , id DESC")
            ->select();
        $this->assign('comment_count',$commentCount);
        $this->assign('post_id',$params['post_id']);
        $this->assign('post_table',$params['post_table']);
        $this->assign('user',cmf_get_current_user());
        $this->assign("comments", Comments::FormartComments($comments));
        return $this->fetch('comment');
    }
}