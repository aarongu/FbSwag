<html>
<head>
	<title>Test App</title>
        <!-- CSS -->
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="index.css" rel="stylesheet" type="text/css" />
	<script src="toggle.js" type="text/javascript"></script>
    
    <link href="css/style.css" rel="stylesheet" type="text/css">
    <link href="css/bootstrap-responsive.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
  <script src="//code.jquery.com/jquery-1.10.2.js"></script>
  <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
  
</head>
<body>
<div id="fb-root"></div>
<script>
var name;
  window.fbAsyncInit = function() {
  FB.init({
    appId      : '1414557272147252',
    status     : true, // check login status
    cookie     : true, // enable cookies to allow the server to access the session
    xfbml      : true  // parse XFBML
  });

FB.Event.subscribe('auth.authResponseChange', function(response) {
    if (response.status === 'connected') {
      console.log('Logged in');
	  printPictures();
	 // printNames();
	  testAPI();
    } else {
      FB.login();
    }
  });
  
  function printPictures() {
	name = location.search;
	if (name.search("name=") != -1) {
	console.log("name: " + name);
	name = name.substr(6); 
	name = name.replace(/\+/g, " ");
	document.getElementById("name").innerHTML=name;
	console.log("name: " + name);
    
    
    // INSERTION
    var current_name = document.getElementById("current_name");
    current_name.innerHTML = name; 
    
    
    
    
    
	FB.api('me?fields=name,id', function(response) {
		var me = response['name'];
		var meId = response['id'];
		console.log(me + ' ' + meId);
	FB.api('me/friends', function(response) {
		var names = new Array();
		var ids = new Array();
		for (var i = 0; i < response.data.length; i++) {
			names[i] = response.data[i]['name'];
			ids[i] = response.data[i]['id'];
		}
		var id = ids[names.indexOf(name)];
		names[names.length] = me;
		ids[ids.length] = meId;
		if (name == me)
			id = meId;
		var availableTags = names.slice();
		availableTags.sort();
		$( "#tags" ).autocomplete({
		  source: availableTags
		});
		if (names.indexOf(name) == -1) {
			document.getElementById("invalid").innerHTML='Error: ' + name + ' is not a Facebook friend';
			return;
		}
		console.log(ids[names.indexOf(name)] + "/photos");
		console.log(ids[names.indexOf(name)]);
		
		FB.api((id + '?fields=relationship_status,significant_other'), function(response) {
			if (response['relationship_status'] != null) {
				var status = response['relationship_status'];
				var so;
				if (response['significant_other'] != null)
					so = response['significant_other'].name;
				var connector;
				var to;
				if (so != null) {
					if (status == "Married" || status == "Widowed" || status == "Separated" || status == "Divoreced")
						connector = "to";
					else
						connector = "with";
				}
				var string = name + ' is ' + status.toLowerCase();
				if (connector != null)
					string += ' ' + connector + ' ' + so;
			} else 
				var string = name + ' has no relationship status';
			/*
			var type;
			if (so.length != 0) {
				
			}
			var to = new Array("Engaged", "Married"
			if (status == "Married")
				type = "to";
			else if (status == "In a Relationship"
			var string = name + ' is ' + status + type + so;
			*/
			/*
			
			#####RELATIONSHIP STATUS#####
			
			*/
			document.getElementById("relationship").innerHTML=string + '.';
		});
		console.log(id);
		FB.api((id + "/photos?limit=10000&fields=likes.limit(1000),source"), function(response) {
			var pictures = new Array();
			console.log(response.data.length);
			for (var i = 0; i < response.data.length; i++) {
				var likes;
				var dat = response.data[i];
				if (dat['likes'] != null)
					likes = dat['likes'].data.length;
				else
					likes = 0;
				pictures[i] = new Array(dat['source'], likes);
			}
			pictures.sort((function(index){
				return function(a, b) {
					return (a[index] === b[index] ? 0 : (a[index] > b[index] ? -1 : 1));
				};
			})(1));
			/*
			
			#####PHOTOS#####
			
			*/
			for (var i = 0; i < 5; i++) {
				document.getElementById('images').innerHTML += ('<img src="' + pictures[i][0] + '" alt="image" />' + pictures[i][1]);
			}

		});
		FB.api((id + "/statuses?limit=1000&fields=likes.limit(1000),message"), function(response) {
			// document.getElementById('demo2').innerHTML=(response.data[0]["message"]); 
			var gg = new Array();
			for (var i = 0; i < response.data.length; i++) {
				var likes;
				if (response.data[i]['likes'] != null) {
					likes = response.data[i]['likes'].data.length;
				} else {
					likes = 0;
				}
				var message = response.data[i]['message'];
				message = message.replace(/\n/g, '<br />');
				gg[i] = new Array(message, likes);
				// document.getElementById("demo2").innerHTML+=(gg[i][0] + ' Likes: ' + gg[i][1] + '<br />');
			}
			gg.sort((function(index){
				return function(a, b) {
					return (a[index] === b[index] ? 0 : (a[index] > b[index] ? -1 : 1));
				};
			})(1));
			for (var i = 0; i < 5/*gg.length*/; i++) {
				document.getElementById('demo2').innerHTML+=(gg[i][0] + ' Likes: ' + gg[i][1] + '<br />');
			}
		});
		
			FB.api((ids[names.indexOf(name)] + "/statuses?limit=1000&fields=comments.limit(1000),from"), function(response) {
        var comment = new Array();
        commentnames = new Array();
        for (var i = 0; i < response.data.length; i++) {

          if (response.data[i]['comments'] != null) {
            comment[i] = response.data[i]['comments']['data']; // returns the data array containing comments
            for (var j = 0; j < comment[i].length; j++) {
              var dudename = comment[i][j]['from']['name'];
              if (dudename != name) {
                var index = getIndex(commentnames, dudename);
                if (index == -1) {
                  commentnames.push(new Array(dudename, 1));
                } else {
                  commentnames[index][1]++;
                }
              }
            }
          }
        }

        commentnames.sort((function(index){
          return function(a, b) {
            return (a[index] === b[index] ? 0 : (a[index] > b[index] ? -1 : 1));
          };
        })(1));
		/*
		
		#####MOST COMMENTS#####
		
		*/
         for (var i = 0; i < commentnames.length; i++) {
           document.getElementById('demo3').innerHTML+=(commentnames[i][0] + ' count ' + commentnames[i][1] + '<br />');
         }

        function getIndex(commentnames, dudename) {
          for (var i = 0; i < commentnames.length; i++) {
            if (commentnames[i][0] == dudename) {
              return i;
            }
          }
          return -1;
        }

      FB.api((ids[names.indexOf(name)] + "/statuses?limit=1000&fields=likes.limit(1000)"), function(response) {
        var comment = new Array();
        likecount = new Array();
        for (var i = 0; i < response.data.length; i++) {
          if (response.data[i]['likes'] != null) {
            comment[i] = response.data[i]['likes']['data']; // returns the data array containing likes
            for (var j = 0; j < comment[i].length; j++) {
              var dudename = comment[i][j]['name'];
              if (dudename != name) {
                var index = getIndex(likecount, dudename);
                if (index == -1) {
                  likecount.push(new Array(dudename, 1));
                } else {
                  likecount[index][1]++;
                }
              }
            }
          }
        }

        likecount.sort((function(index){
          return function(a, b) {
            return (a[index] === b[index] ? 0 : (a[index] > b[index] ? -1 : 1));
          };
        })(1));
		/*
		
		#####MOST LIKES#####
		
		*/
         for (var i = 0; i < likecount.length; i++) {
           document.getElementById('demo6').innerHTML+=(likecount[i][0] + ' count ' + likecount[i][1] + '<br />');
         }

        function getIndex(likecount, dudename) {
          for (var i = 0; i < likecount.length; i++) {
            if (likecount[i][0] == dudename) {
              return i;
            }
          }
          return -1;
        }

       // var total;
       console.log(likecount);
       console.log(commentnames);
       var total = likecount.slice();
       for (var i = 0; i < commentnames.length; i++) {
          var found = false;
        for (var j = 0; j < likecount.length; j++) {
          if (total[j][0] == commentnames[i][0]) {
            found = true;
            total[j][1] = total[j][1] * 1.5 + commentnames[i][1];
            break;
          }
        }
        if (!found) {
          total.push(commentnames[i]);
        }
       }
      total.sort((function(index){
          return function(a, b) {
            return (a[index] === b[index] ? 0 : (a[index] > b[index] ? -1 : 1));
          };
        })(1));
		/*
		
		#####TOP FRIENDS#####
		
		*/
       for (var i = 0; i < total.length; i++)
       document.getElementById('demo5').innerHTML+=(total[i] + '<br />');
       //for (var i = 0; i < likecount.length)
       // getTotal(commentnames, likecount);
        function getTotal (commentnames, likecount) {
          console.log(likecount);
          var total = likecount.slice();
          console.log(total);
          for (var j = 0; j < commentnames.length; j++) {
            var sc = commentnames[j][0];
            var indezz = getIndex(total, sc);
            console.log(indezz);
            if (indezz != -1) {
              total[indezz][1] = ((total[indezz][1] * 1.53278) + commentnames[j][1]);
            } else {
              total.push(commentnames[i]);
            }
          }
          for (var i = 0; i < total.length; i++) {
            document.getElementById('demo5').innerHTML+=(total[i].toString() + '<br />');            
          }
        }
    });
    });

		// getStatuses();
	});
	
	});
	
	} else {
			/*
			
			#####DISPLAY IF NOT LOGGED IN TO FACEBOOK YET#####
			
			*/
		  var ids = new Array();
		  $(function() {
			FB.api('/me/friends', function(response) {
				var availableTags = new Array(); 
				var ids = new Array();
				for (var i = 0; i < response.data.length; i++) {
					availableTags[i] = response.data[i]['name'];
				}
				availableTags.sort();
				$( "#tags" ).autocomplete({
				  source: availableTags
				});
			});
		  });
		console.log("no parameter passed");
	}
  };
  
  function printNames() {

	
  }
};

  // Load the SDK asynchronously
  (function(d){
   var js, id = 'facebook-jssdk', ref = d.getElementsByTagName('script')[0];
   if (d.getElementById(id)) {return;}
   js = d.createElement('script'); js.id = id; js.async = true;
   js.src = "https://connect.facebook.net/en_US/all.js";
   ref.parentNode.insertBefore(js, ref);
  }(document));

  // Here we run a very simple test of the Graph API after login is successful. 
  // This testAPI() function is only called in those cases. 
  function testAPI() {
    console.log('Welcome!  Fetching your information.... ');
    FB.api('/me', function(response) {
      console.log('Good to see you, ' + response.name + '.');
    });
  }
</script>


<!-- WHERE THE HTML STARTS OMGGG -->


<div class="jumbotron hero-spacer">
    <div id="poop">
  <img src ="http://i.imgur.com/Kf0Papj.png" alt="pic">
</div>
  <h1 id="current_name"> Aaron Gupta</h1>
</div>

  
<!--main-->
<div class="container" id="main">
   <div class="row">
   <div class="col-md-4 col-sm-6">
        <div class="panel panel-default">
          <div class="panel-heading"><a href="#" class="pull-right">View all</a> <h4>Interests</h4></div>
        <div class="panel-body">
              <div class="list-group"> 
                <a href="#" class="list-group-item">sleeping </a>
                <a href="#" class="list-group-item">books </a>
                <a href="#" class="list-group-item">swag</a>
              </div>
            </div>
      </div>

     
 

  </div>
    <div class="col-md-4 col-sm-6">
         

         <div class="panel panel-default">
           <div class="panel-heading"><a href="#" class="pull-right">View all</a> <h4>Relationship Status</h4></div>
        <div class="panel-body">
              <p><img src="http://www.iconarchive.com/download/i66644/designbolts/free-valentine-heart/Heart-Shadow.ico" class="img-circle pull-right"> <a href="#">YES</a></p>
              <div class="clearfix"></div>
              <hr>
             
            </div>
         </div>
      

    </div>
    <div class="col-md-4 col-sm-6">
         <div class="panel panel-default">
           <div class="panel-heading"><a href="#" class="pull-right">View all</a> <h4>Most Recent Location</h4></div>
        <div class="panel-body">
              <ul class="list-group">
              <li class="list-group-item">somewhere</li>
              </ul>
            </div>
      </div>
      
    </div>
  </div><!--/row-->
      <hr>
  
  <div class="row">
      <h2> Top 3 Pictures Based on # of Likes &amp; Comments</h2>
  
     <div class="col-sm-4 col-xs-6">
      
        <div class="panel panel-default">
          <div class="panel-thumbnail"><img src="/assets/example/bg_suburb.jpg" class="img-responsive"></div>
          <div class="panel-body">
            <p class="lead">NAME</p>

          </div>
        </div>

        
      </div><!--/col-->
      
      <div class="col-sm-4 col-xs-6">
      
        <div class="panel panel-default">
          <div class="panel-thumbnail"><img src="//placehold.it/450X300/DD66DD/FFF" class="img-responsive"></div>
          <div class="panel-body">
            <p class="lead">NAME</p>
          </div>
        </div>

        
      </div><!--/col-->
      
      <div class="col-sm-4 col-xs-6">
      
        <div class="panel panel-default">
          <div class="panel-thumbnail"><img src="//placehold.it/450X300/2222DD/FFF" class="img-responsive"></div>
          <div class="panel-body">
            <p class="lead">NAME</p>
          </div>
        </div>

      
      </div>
  </div>
<hr>
  
  <div class="row">
    <div class="col-md-12"><h2>Most Liked Posts</h2></div>
    <div class="col-md-4 col-sm-6">
      <div class="panel panel-default">
           <div class="panel-heading"> <h4>Persons Name</h4></div>
        <div class="panel-body">
              <div class="clearfix"></div>
              <p>Migrating from Bootstrap 2.x to 3 is not a simple matter of swapping out the JS and CSS files.
              Bootstrap 3 is a major overhaul, and there are a lot of changes from Bootstrapis intended to help 2.x developers transition to 3.
              </p>
              
            </div>
         </div> 
    </div>
    
        <div class="col-md-4 col-sm-6">
      <div class="panel panel-default">
           <div class="panel-heading"> <h4>Persons Name</h4></div>
        <div class="panel-body">
              <div class="clearfix"></div>
              <p>Migrating from Bootstrap 2.x to 3 is not a simple matter of swapping out the JS and CSS files.
              Bootstrap 3 is a major overhaul, and there are a lot of changes from Bootstrap 2.is intended to help 2.x developers transition to 3.
              </p>
              
            </div>
         </div> 
    </div>
    
        <div class="col-md-4 col-sm-6">
      <div class="panel panel-default">
           <div class="panel-heading"> <h4>Persons Name</h4></div>
        <div class="panel-body">
              <div class="clearfix"></div>
              <p>Migrating from Bootstrap 2.x to 3 is not a simple matter of swapping out the JS and CSS files.
              Bootstrap 3 is a major overhaul, and there are a lot of changes from Bootstrap 2.x.  is intended to help 2.x developers transition to 3.
              </p>
              
            </div>
         </div> 
    </div>
  </div>

<hr>    
    
<!--/col-->
  <div class="row">
      <h2> Most Liked Pictures</h2>
  
     <div class="col-sm-4 col-xs-6">
      
        <div class="panel panel-default">
          <div class="panel-thumbnail"><img src="/assets/example/bg_suburb.jpg" class="img-responsive"></div>
          <div class="panel-body">
            <p class="lead">Name</p>

          </div>
        </div>

        
      </div><!--/col-->
      
      <div class="col-sm-4 col-xs-6">
      
        <div class="panel panel-default">
          <div class="panel-thumbnail"><img src="//placehold.it/450X300/DD66DD/FFF" class="img-responsive"></div>
          <div class="panel-body">
            <p class="lead">Name</p>
          </div>
        </div>

        
      </div><!--/col-->
      
      <div class="col-sm-4 col-xs-6">
      
        <div class="panel panel-default">
          <div class="panel-thumbnail"><img src="//placehold.it/450X300/2222DD/FFF" class="img-responsive"></div>
          <div class="panel-body">
            <p class="lead">Name</p>
          </div>
        </div>

      
      </div>
  </div>
<hr>  
    <div class="row">
      <div class="col-md-12"><h2>Graphs</h2></div>
        <div class="col-md-12">

        </div>

               <div class="col-md-6 col-sm-6">
      <div class="panel panel-default">
           <div class="panel-heading"><a href="#" class="pull-right">View all</a> <h4>Most Comments by Friend</h4></div>
        <div class="panel-body">
              <span class = "bar_names"> POOP </span>
              <div class="progress">
                <div class="progress-bar progress-bar-info" style="width: 70%" title="Stuff"></div>
              </div>
              <span class = "bar_names"> POOP </span>
              <div class="progress">
                <div class="progress-bar progress-bar-success" style="width: 80%" title="stuff 2"></div>
              </div>
              <span class = "bar_names"> POOP </span>
              <div class="progress">
                <div class="progress-bar progress-bar-warning" style="width: 80%" title="stuff 3"></div>
              </div>
              <span class = "bar_names"> POOP </span>
              <div class="progress">
                <div class="progress-bar progress-bar-danger" style="width: 50%" stuff="stuff4"></div>
              </div>
              
            </div>
         </div> 
    </div>         
                        
                        
                        
 <div class="col-md-6 col-sm-6">
      <div class="panel panel-default">
           <div class="panel-heading"><a href="#" class="pull-right">View all</a> <h4>Most Likes by Friends</h4></div>
        <div class="panel-body">
              <span class = "bar_names"> POOP </span>
              <div class="progress">
                <div class="progress-bar progress-bar-info" style="width: 70%" title="Stuff"></div>
              </div>
              <span class = "bar_names"> POOP </span>
              <div class="progress">
                <div class="progress-bar progress-bar-success" style="width: 80%" title="stuff 2"></div>
              </div>
              <span class = "bar_names"> POOP </span>
              <div class="progress">
                <div class="progress-bar progress-bar-warning" style="width: 80%" title="stuff 3"></div>
              </div>
              <span class = "bar_names"> POOP </span>
              <div class="progress">
                <div class="progress-bar progress-bar-danger" style="width: 50%" stuff="stuff4"></div>
              </div>
              
            </div>
         </div> 
    </div>
    </div><!--playground-->
    
    
    <div class="clearfix"></div>
      
    
  </div>
</body>
</html>
