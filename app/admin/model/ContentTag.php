<?php

namespace app\admin\model;

use think\Model;

class ContentTag extends Model
{
    protected $autoWriteTimestamp = true;
    
    public function articles()
    {
        return $this->belongsToMany(ContentArticle::class, 'content_article_tag', 'article_id', 'tag_id');
    }
}