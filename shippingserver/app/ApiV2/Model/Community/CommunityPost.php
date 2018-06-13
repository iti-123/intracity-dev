<?php

namespace ApiV2\Model\Community;
use Illuminate\Database\Eloquent\Model;
use ApiV2\Model\Community\CommentModel;
use ApiV2\Model\Community\LikeModel;
use App\User;
use DB;
use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\ApiV2\Events\BuyerPostCreatedEvent;
use Illuminate\Support\Facades\Crypt;

class CommunityPost extends Model 
{   

  protected $table = 'community_posts';
  protected $appends = ['encryptedId'];

    public function comments() {       
        return $this->hasMany(CommentModel::class,'article_id')->orderBy('id','decs');
    }
  
    public function ArticleLikes() {
     	  return $this->hasMany(LikeModel::class,'article_comment_reply_id')->where('type','=',1);
    }

     
    public function postedby() {
       	return $this->belongsTo(User::class,'user_id')->select('id','username','logo','user_pic');
    }

    public function getEncryptedIdAttribute()
    {
        return Crypt::encrypt($this->id);
    }


}