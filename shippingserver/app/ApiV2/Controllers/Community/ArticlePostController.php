<?php
namespace ApiV2\Controllers\Community;

use DB;
use ApiV2\Services\UserSettingsService;
use ApiV2\Model\Community\CommunityPost;
use ApiV2\Model\Community\CommentModel;
use ApiV2\Model\Community\ReplyModel;
use ApiV2\Model\Community\LikeModel;
use ApiV2\Controllers\BaseController;
use ApiV2\Services\LogistiksCommonServices\EncrptionTokenService;
use ApiV2\Controllers\UserServices;
use ApiV2\Controllers\AbstractUserServices;
use ApiV2\Services\LogistiksCommonServices\DocumentServices;
use Exception;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use ApiV2\Services\LogistiksCommonServices\NumberGeneratorServices;

class ArticlePostController extends BaseController
{
    

    public function addArticle(Request $request)
    {
       //return $request->all();
        try {
                $userid = JWTAuth::parseToken()->getPayload()->get('id');
                $data = json_decode($request->data);
                $input=array();
                $file['name']='';
                $input['name']=self::has($data, 'image')?$data->image->name:'';
                $input['doc']=self::has($data, 'image')?$data->image->doc:'';
                if(!empty($input['name']))
                {
                    $file=DocumentServices::storearticleImage($input, 'community/article/');
                }
                $article=new CommunityPost;
                $article->title=self::has($data, 'title');
                $article->heading=self::has($data, 'heading');
                $article->description=self::has($data, 'discription');
                $article->price=self::has($data, 'price')?$data->price:'';
                $article->user_id=$userid;
                $article->post_type=$data->post_type; //for article and event
                // for event 
                if($data->post_type == 2) {
                    $article->event_start_date = self::has($data, 'startDate');
                    $article->event_end_date = self::has($data, 'endDate');
                    $article->event_start_time = self::has($data, 'startTime');
                    $article->event_end_time = self::has($data, 'endTime');
                    
                    $article->event_location = self::has($data, 'location');
                    $article->seats_available = self::has($data, 'seatsAvailable');
                    $article->workshop_agenda = self::has($data, 'workShopAgenda');
                    $article->why_attend = self::has($data, 'whyAttend');                    
                }

                $article->articletype=self::has($data, 'articletype')?$data->articletype:'';
                $article->status=self::has($data, 'status')?$data->status:'';
                if($file['name']!='')
                {
                 $article->file=$file['name']? $file['name']:'';
                 $article->file_path=$file['path']? $file['path']:'';
                }
                $article->save();
               return response()->json([
                'status' => 'success',
                'payload' => $article->save() ? $article : false
            ], 200);

        } catch (Exception $e) {
           
            return $this->errorResponse($e);

        }

    }
    
    public function editArticles(Request $request)
    {
        try {
              $userid = JWTAuth::parseToken()->getPayload()->get('id');
              $data = json_decode($request->data);
              $input=array();
              $file['name']='';
              $input['name']=self::has($data, 'image')?$data->image->name:'';
              $input['doc']=self::has($data, 'image')?$data->image->doc:'';
              if(!empty($input['name']))
              {
                  $file=DocumentServices::storearticleImage($input, 'community/article/');
              }
              $article = CommunityPost::find($data->id);
              $article->title=self::has($data, 'title');
              $article->heading=self::has($data, 'heading');
              $article->description=self::has($data, 'discription');
              $article->price=self::has($data, 'price')?$data->price:'';
              $article->user_id=$userid;
              $article->post_type=$data->post_type; //for article and event
              // for event 
              if($data->post_type == 2) {
                  $article->event_start_date = self::has($data, 'startDate');
                  $article->event_end_date = self::has($data, 'endDate');
                  $article->event_start_time = self::has($data, 'startTime');
                  $article->event_end_time = self::has($data, 'endTime');
                  
                  $article->event_location = self::has($data, 'location');
                  $article->seats_available = self::has($data, 'seatsAvailable');
                  $article->workshop_agenda = self::has($data, 'workShopAgenda');
                  $article->why_attend = self::has($data, 'whyAttend');                    
              }

              $article->articletype=self::has($data, 'articletype')?$data->articletype:'';
              $article->status=self::has($data, 'status')?$data->status:'';
              if($file['name']!='')
              {
               $article->file=$file['name']? $file['name']:'';
               $article->file_path=$file['path']? $file['path']:'';
              }
              $article->save();
             return response()->json([
              'status' => 'success',
              'payload' => $article->save() ? $article : false
          ], 200);
        } catch (Exception $e) {
             return $this->errorResponse($e);

        }

    }

    public function Articlelist(Request $request,$id='') {
        try {
            $article=CommunityPost::with([
                'comments',
                'postedby',
                'ArticleLikes',
                'ArticleLikes.LikedUsers',
                'comments.commentReply',
                'comments.CommentLikes',
                'comments.CommentUsers',
                'comments.commentReply.ReplyLikes',
                'comments.commentReply.ReplyedUsers'
            ]);
            
            if($id!='')
            {
                $article->where('id', '=', $id);
            } else if(isset($request['at']) && !empty($request['at'])) {
                $article->where('articletype','=',$request['at']);                    
            }

            if(isset($request['pt']) && !empty($request['pt'])) {
                $article->where('post_type','=',$request['pt']);
            }
                
            $rs=$article->latest()->get();
              
            return response()->json([
                'isSuccessful' => true,
                'data'=>$rs,
            ], 200); 
         }catch(Exception $e)
         {
            return $this->errorResponse($e);
         }
    }
       public static function has($object, $property)
        {
            return property_exists($object, $property) ? $object->$property : '';
        }
      
      public function Comment(Request $request)
      {
            try{
                 $userid = JWTAuth::parseToken()->getPayload()->get('id'); 
                 $Comment=new CommentModel;
                 $Comment->article_id=$request->id;
                 $Comment->comment=$request->comment;
                 $Comment->user_id=$userid;
                 $Comment->save();
                 return response()->json([
                        'isSuccessful' => true,
                        'data'=>$Comment,
                    ], 200); 
            }catch(Exception $e)
            {
              return $this->errorResponse($e);
            }
      }
      public function CommentReply(Request $request)
      {
            //return $request->all();
            try{
                 $userid = JWTAuth::parseToken()->getPayload()->get('id'); 
                 $ReplyModel=new ReplyModel;
                 $ReplyModel->comment_id=$request->id;
                 $ReplyModel->reply_text=$request->reply;
                 $ReplyModel->article_id=$request->article_id;
                 $ReplyModel->user_id=$userid;

                 $ReplyModel->save();
                 return response()->json([
                        'isSuccessful' => true,
                        'data'=>$ReplyModel,
                    ], 200); 
            }catch(Exception $e)
            {
              return $this->errorResponse($e);
            }
      }
     public function Likes(Request $request)
     {   

            try{
                $userid = JWTAuth::parseToken()->getPayload()->get('id'); 
                $type=$request->data['type'];
                $id=$request->data['id'];
                $like=self::getLikes($userid,$type,$id);
                if(count($like)==0)
                {
                   $like=new LikeModel;
                   $like->article_comment_reply_id=$id;
                   $like->type=$type;
                   $like->user_id=$userid;
                   $like->save();
                   $rs=self::getLikes($userid,$type,$id); 
                } else{
                     $like=LikeModel::where([
                        ["user_id",'=',$userid],
                        ["type",'=',$type],
                        ["article_comment_reply_id",'=',$id],
                    ])->delete();
                 $rs=self::getLikes($userid,$type,$id);
                }
                
                 return response()->json([
                                'isSuccessful' => true,
                                'data'=>$rs,
                            ], 200); 

            }catch(Exception $e)
                {
                    return $this->errorResponse($e);
                }
     }
    
    public static function getLikes($userid,$type,$id)
    {
        $like=LikeModel::where([
                    ["user_id",'=',$userid],
                    ["type",'=',$type],
                    ["article_comment_reply_id",'=',$id],
                ])->get();
        return $like;
    }
  
    public function commentDelete(Request $request)
    {
        $del = $request->data;
        try{
            $comm = CommentModel::find($del);
            $comm->delete();
             return response()->json([
                                'isSuccessful' => true,
                                'data'=>$comm,
                            ], 200); 
        }catch(Exception $e)
        {
            return $this->errorResponse($e);
        }
    }

    public function editArticle(Request $request)
    {
      try{
          $comm = CommunityPost::find($request->id);
          return response()->json([
              'isSuccessful' => true,
              'data'=>$comm,
          ], 200); 
      }catch(Exception $e)
      {
        return $this->errorResponse($e);
      }
    }

    public function postDelete(Request $request)
    {
        $del = $request->data;
        try{
            $post = CommunityPost::find($del);
            $post->delete();
             return response()->json([
                                'isSuccessful' => true,
                                'data' => $post,
                            ], 200); 
        }catch(Exception $e)
        {
            return $this->errorResponse($e);
        }
    }

    public function commentUpdate(Request $request)
    {
      $id=$request->data['id'];
      $comment=$request->data['comment'];   
      try{
            $comm=CommentModel::where('id', $id)->update(['comment' =>$comment]);

          return response()->json([
                'isSuccessful' => true,
                'data'=>$comm,
                 ], 200); 

      }catch(Exception $e)
      {
        return $this->errorResponse($e);
      }
    }
    public function replyDelete(Request $request)
    {
        $del=$request->data;
        try{
            $ReplyModel=ReplyModel::find($del);
            $ReplyModel->delete();
             return response()->json([
                                'isSuccessful' => true,
                                'data'=>$ReplyModel,
                            ], 200); 
        }catch(Exception $e)
        {
            return $this->errorResponse($e);
        }
    }

    public function replyUpdate(Request $request)
    {
      $id=$request->data['id'];
      $reply=$request->data['reply'];   
      try{
            $rep=ReplyModel::where('id', $id)->update(['reply_text' =>$reply]);
          return response()->json([
                'isSuccessful' => true,
                'data'=>$rep,
                 ], 200); 

      }catch(Exception $e)
      {
        return $this->errorResponse($e);
      }
    }
    

}