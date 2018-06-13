<?php

namespace ApiV2\Model\Community;
use Illuminate\Database\Eloquent\Model;
use ApiV2\Model\Community\ReplyModel;
use DB;
use App\User;
use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\ApiV2\Events\BuyerPostCreatedEvent;

class CommentModel extends Model 
{   

    protected $table = 'article_comments';

    public function commentReply()
    {
    	return $this->hasMany(ReplyModel::class,'comment_id')->orderBy('comment_id');
    }
    
    public function CommentLikes()
    {
    return $this->hasMany(LikeModel::class,'article_comment_reply_id')->where('type','=',2);
    }
    public function CommentUsers()
    {
    return $this->belongsTo(User::class,'user_id')->select('id','username','logo','user_pic');
    }

}