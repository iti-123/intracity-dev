app.controller('EventListCtrl', ['$scope', '$http', 'config', 'apiServices', 'type_basis', '$state', '$dataExchanger','apiCommunityServices','apiShareServices', '$sce', function ($scope, $http, config, apiServices, type_basis, $state, $dataExchanger,apiCommunityServices,apiShareServices,$sce) {
    $scope.STATUS=STATUS;
    $scope.ARTICLE=ARTICLE;
    var serverUrl = config.serverUrl;
    var authToken = config.appAuthToken;
    $scope.$sce = $sce;
    
    $scope.storagePath = STORAGE_PATH;
    

    $scope.servicetype = SERVICE_TYPE;
    $scope.data = [];
    $scope.type_basis = type_basis.type_basis;
    $scope.UNIT = ESTIMATED_UNIT;
    $scope.priceTypes = HYPER_PRICE_TYPES;
    var url = serverUrl + 'locations/getCity';
    $scope.userid=Auth.getUserID();
    $scope.getCity = function (url) {
        apiServices.city(url).then(function (response) {
            $scope.cities = response;
        });
    }
    //  Get city of intracity 
    $scope.getCity(url);
    $scope.getLocationByCity = function (url) {
        apiServices.getLocationByCity(url).then(function (response) {
            $scope.locations = response;
        });
    }  
    $scope.articleType = 'Paid Event';
    $scope.getarticleLIst=function(type) {
        if(type == 2) {
            $scope.articleType = 'Paid Event';
        } else {
            $scope.articleType = 'Free Event';
        }
        var url = serverUrl + 'community/article-list?at='+type+'&pt='+2;
        apiCommunityServices.articleList(url).then(function (response) {
            $scope.payload = response;
            $scope.articles = response.data;
            console.log($scope.payload);
        });
    }
    console.log($state.params.type);
    $scope.redirectToAction = function() {
        if($state.params.type == 'free') {
            $scope.getarticleLIst(1);
        } else {
            $scope.getarticleLIst(2); 
        }
    }   
    $scope.redirectToAction();
   
   $scope.comment=function(key,event)
   {
      if(event.keyCode==13)
         {
             $scope.requestdata=$scope.articles[key];
              var url =serverUrl +'community/post-comment';
              apiCommunityServices.postComment(url,$scope.requestdata).then(function (response){
                    
                    if(response.isSuccessful==true)
                    {                    
                        $scope.getarticleLIst($scope.articles[key].articletype);
                    }
              
              });
           
         }
   }
   
  $scope.replycomment=function(k,key,$event)
  {
    if(event.keyCode==13)
         {
            $scope.requestdata=$scope.articles[key].comments[k];
              var url =serverUrl +'community/post-comment-reply';
              apiCommunityServices.postCommentReply(url,$scope.requestdata).then(function (response){
                    // $scope.articles[key].comments[k].comment_reply.push(response.data);
                    // $scope.articles[key].comments[k].reply='';
                    if(response.isSuccessful==true)
                      {
                        $scope.redirectToAction();
                      }
              });
         }
  }
  
  $scope.likes=function(type,id,key,value)
  {
      $scope.requestdata={type:type,id:id};
      var url=serverUrl+"community/article-likes";
      apiCommunityServices.postMethod(url,$scope.requestdata).then(function(response){
           switch(type)
           {
            case 1:/// article like
                 if(response.data.length==1)
                 {
                  $scope.articles[key].article_likes.push(response.data[0]);
                 }else{
                       for(let k in $scope.articles[key].article_likes)
                       {
                          var liktetype=$scope.articles[key].article_likes[k].type;
                          var likeuser_id=$scope.articles[key].article_likes[k].user_id;
                          if(liktetype==type && likeuser_id==value.user_id)
                             {
                              $scope.articles[key].article_likes.splice(k,1);
                             }
                       }
                 }
                 break;
            case 2://comment like
                  
                 if(response.data.length==1)
                 {
                     for(let obj of $scope.articles[key].comments)
                     {
                          if(obj.id==id)
                          {
                            obj.comment_likes.push(response.data[0]);
                          }
                     }
                  
                 }else{
                      for(let kk in $scope.articles[key].comments)
                      {
                           
                          for(let k in $scope.articles[key].comments[kk].comment_likes)
                         {
                            var commentid=$scope.articles[key].comments[kk].id;
                            var liktetype=$scope.articles[key].comments[kk].comment_likes[k].type;
                            var likeuser_id=$scope.articles[key].comments[kk].comment_likes[k].user_id;
                            
                            if(liktetype==type && likeuser_id==value.user_id && id==commentid )
                               {
                                $scope.articles[key].comments[kk].comment_likes.splice(k,1);
                               }
                          }
                        
                      }
                 }
                 break;

            case 3: // reply likes 
               
                if(response.data.length==1)
                 {
                    
                     for(let obj of $scope.articles[key].comments)
                     {    
                          for(let objj of obj.comment_reply)
                          {
                          
                           if(objj.id==id)
                           {
                             objj.reply_likes.push(response.data[0]);
                           }
                          }
                     }
                  
                 }else{
                      for(let kk in $scope.articles[key].comments)
                      {
                       
                         for(let k in $scope.articles[key].comments[kk].comment_reply)
                         {
                           for(let kkk in $scope.articles[key].comments[kk].comment_reply[k].reply_likes)
                           {
                            var liktetype=$scope.articles[key].comments[kk].comment_reply[k].reply_likes[kkk].type;
                            var likeuser_id=$scope.articles[key].comments[kk].comment_reply[k].reply_likes[kkk].user_id;
                            var repid=$scope.articles[key].comments[kk].comment_reply[k].id;
                            
                            if(liktetype==type && likeuser_id==value.user_id && id==repid)
                               {
                                
                                $scope.articles[key].comments[kk].comment_reply[k].reply_likes.splice(kkk,1);
                               }
                           }
                         }
                        
                      }
                 }

              break;

           }

           
      });
  }
   ///comment del
   $scope.commentDel=function(id)
   {
     var url=serverUrl+"community/comment-delete";
      apiCommunityServices.postMethod(url,id).then(function(response){
        if(response.isSuccessful==true)
        {
            $scope.redirectToAction();
        }
      })    
   }
   //comment edit
   $scope.commentEdit=function(id)
   {
    
      $("#comment"+id).addClass('form-control').removeClass('commenttxt');
      $("#comment"+id).attr("readonly", false);
       var el = $("#comment"+id).get(0);
       var elemLen = el.value.length;
       el.selectionStart = elemLen;
       el.selectionEnd = elemLen;
       el.focus(); 

      $("#comment"+id).keypress(function (e) {//enter key
          if(e.which==13)
          {

            var txt=$("#comment"+id).val();
            $scope.updatedata={id:id,comment:txt};
            var url=serverUrl+"community/comment-update";
            apiCommunityServices.postMethod(url,$scope.updatedata).then(function(response){
                $scope.redirectToAction();
                 $("#comment"+id).addClass('commenttxt').removeClass('form-control');
                 $("#comment"+id).attr("readonly", true); 
            }) 
          }
      });
      $("#comment"+id).blur(function (e) {// on blure
         var txt=$("#comment"+id).val();
         $scope.updatedata={id:id,comment:txt};
          var url=serverUrl+"community/comment-update";
          apiCommunityServices.postMethod(url,$scope.updatedata).then(function(response){
            $scope.redirectToAction();
           $("#comment"+id).addClass('commenttxt').removeClass('form-control');
           $("#comment"+id).attr("readonly", true); 
      }) 
     
      });
         
   }
   //reply del
    $scope.replyDel=function(id)
     {
       var url=serverUrl+"community/reply-delete";
        apiCommunityServices.postMethod(url,id).then(function(response){
          if(response.isSuccessful==true)
          {
            $scope.redirectToAction();
          }
        })    
     }
  //reply update
  $scope.replyEdit=function(id)
   {
    
      $("#reply"+id).addClass('form-control').removeClass('commenttxt');
      $("#reply"+id).attr("readonly", false);
       var el = $("#reply"+id).get(0);
       var elemLen = el.value.length;
       el.selectionStart = elemLen;
       el.selectionEnd = elemLen;
       el.focus(); 

      $("#reply"+id).keypress(function (e) {//enter key
         var txt=$("#reply"+id).val();
         $scope.updatedata={id:id,reply:txt};
         if(e.which==13)
          {
            var url=serverUrl+"community/reply-update";
            apiCommunityServices.postMethod(url,$scope.updatedata).then(function(response){
            $scope.redirectToAction();
             $("#reply"+id).addClass('commenttxt').removeClass('form-control');
             $("#reply"+id).attr("readonly", true); 
           }) 
       }
     
      });
      $("#reply"+id).blur(function (e) {// on blure
         var txt=$("#reply"+id).val();
         $scope.updatedata={id:id,reply:txt};
          var url=serverUrl+"community/reply-update";
          apiCommunityServices.postMethod(url,$scope.updatedata).then(function(response){
            $scope.redirectToAction();
           $("#reply"+id).addClass('commenttxt').removeClass('form-control');
           $("#reply"+id).attr("readonly", true); 
      }) 
     
      });
         
   }


   $scope.viewAll = function(key,type) {
        // console.log($scope.posts.data[key].description);
        let length = 150;
        if(type == 'more') {
            length = $scope.articles[key].description.length;
        } else if(type == 'less') {
            length = 150;
        }
        $scope.articles[key].limit = length;
    }
    $scope.seeLess = function(desc,limit) {
        return desc.substring(0,limit);
    }


    $scope.createEventRegister = function() 
    {
        var requestPayload = {
            "data": $scope.event,
        };

        var url = serverUrl + 'community/events-register';
            apiCommunityServices.addEventsRegisterUsers(url,requestPayload).then(function (response) {
            $scope.payload = response;

            if($scope.payload.status=='success');
            {
                $state.go("events-register");
            }
          
        });
    }

    var url = serverUrl + 'community/get-event-details/' + $state.params.title;
    $scope.eventDetails = function (url) {
        apiCommunityServices.eventDetails(url).then(function (response) {
            $scope.eventDetails = response;
            console.log('Event Details::', $scope.eventDetails);
        }); 
    }

    $scope.eventDetails(url);



    $scope.sharableData  = {};
    
    $scope.postUrl = function(url,id,commonData) 
    {
    $scope.sharableData.url = url+id;
    $scope.sharableData.commonData = commonData;
    $scope.sharableData.type='events';

    }
    
    
    
    $scope.insertPost=function(value) {
      var shareData = $scope.sharableData;
      // console.log('TESST VALUEEEEE',$scope.sharableData);
        var url = serverUrl + 'community/share';
        apiShareServices.insertPost(url,shareData).then(function(response){
          $scope.postData = response;
          console.log('POSST DATAA::', $scope.postData);
        })
            
    }

    
}]);