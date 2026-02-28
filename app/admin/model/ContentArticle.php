<?php

namespace app\admin\model;

use think\Model;

class ContentArticle extends Model
{
    protected $autoWriteTimestamp = true;
    
    protected $append = [
        'category_name',
        'tag_names',
    ];
    
    public function getCategoryNameAttr($value, $row)
    {
        $category = ContentCategory::where('id', $row['category_id'])->value('name');
        return $category ?: '';
    }
    
    public function getTagNamesAttr($value, $row)
    {
        $tags = $this->tags()->column('name');
        return implode(', ', $tags);
    }
    
    public function category()
    {
        return $this->belongsTo(ContentCategory::class, 'category_id', 'id');
    }
    
    public function tags()
    {
        return $this->belongsToMany(ContentTag::class, 'content_article_tag', 'tag_id', 'article_id');
    }
}