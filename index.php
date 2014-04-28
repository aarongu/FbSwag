<html>
<head>
	<title>QuickView</title>
    <!-- CSS / JS imports  -->
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="css/bootstrap-responsive.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="//code.jquery.com/ui/1.10.4/themes/smoothness/jquery-ui.css">
    <link href="css/index.css" rel="stylesheet" type="text/css">
    <link href="icon.png" rel="icon" type="image/png" />

    <script src="//code.jquery.com/jquery-1.10.2.js"></script>
    <script src="//code.jquery.com/ui/1.10.4/jquery-ui.js"></script>
    <script src="css/toggle.js" type="text/javascript"></script>
    <style>
		#loggedIn{display:none;}
		#noParam{display:none;}
		#login{display:block;}
	</style>
</head>

<body>
<div id="fb-root"></div>



<script>
var loggedIn = false;
window.fbAsyncInit = function() {
	FB.init({
		appId      : '498051066990191',
		status     : true, // check login status
		cookie     : true, // enable cookies to allow the server to access the session
		xfbml      : true  // parse XFBML
	});
	
	FB.Event.subscribe('auth.authResponseChange', function(response) {
		if (response.status === 'connected') {
			console.log('Logged in');
			document.getElementById("login").style.display="none";
			// drawUI();
			main();
			testAPI();
			loggedIn = true;
		} else if (response.status === 'not_authorized') {
			console.log('Not Authorized');
			document.getElementById("login").style.display="block";
			FB.login();
		} else {
			console.log('Else');
			document.getElementById("login").style.display="block";
			FB.login();
		}
	});
	
	function main() {
		//
		var since = location.search;
		var name = location.search;
		console.log(name);
		if (name.search("name=") != -1) {
			if (name.search("since=") == -1)
				name = name.substr(6);
			else {
				name = name.substr(6, name.indexOf("since=") - 7);
				since = since.substr(since.indexOf("since=") + 6);
			}
			if (since == "All")
				since = 0;
			/*
			604800">The Past Week</option>
                <option id="month" value="2629743">The Past Month</option>
                <option id="year" value="31556926
			*/
			else {
				document.getElementById('all').selected='not selected';
				if (since == '604800')
					document.getElementById('week').selected='selected';
				else if (since == '2629743')
					document.getElementById('month').selected='selected';
				else
					document.getElementById('year').selected='selected';
				// console.log(Date.now());	
				// console.log(since);	
				since = (Date.now()  - (since * 1000));
				// console.log(since);
				since = Math.floor(since / 1000);
			}
			console.log(since);
			name = name.replace(/\+/g, " ");
			//document.getElementById("name").innerHTML=name;
			//console.log("name: " + name);
			
			///////////////
			// INSERTION //
			//////////////
			document.getElementById("current_name").innerHTML = name;
			///////////////
			// INSERTION //
			//////////////
			
			// Get the name and id of yourself
			FB.api('me?fields=name,id', function(response) {
				// console.log(response);
				// console.log(name);
				var me = response.name;
				var meId = response.id;
				// Get the name and id of all your friends
				FB.api('me/friends?limit=6000&fields=name,id', function(response) {
					var names = new Array();
					var ids = new Array();
					for (var i = 0; i < response.data.length; i++) {
						names[i] = response.data[i].name;
						ids[i] = response.data[i].id;
					}
					// Add yourself to the array
					names[names.length] = me;
					ids[ids.length] = meId;
					if (name == me)
						id = meId;
					else
						var id = ids[names.indexOf(name)];
					// Create a copy of the names to sort and then become part of the search function
					var availableTags = names.slice();
					availableTags.sort();
					$( "#tags" ).autocomplete({
						source: availableTags
					});
					// Print an error if the name is not found
					if (names.indexOf(name) == -1) {
						document.getElementById("invalid").innerHTML='Error: ' + name + ' is not a Facebook friend';
						return;
					}
					document.getElementById("loggedIn").style.display="block";
					document.getElementById("tags").value=name;
					// console.log(ids[names.indexOf(name)] + "/photos");
					// console.log(ids[names.indexOf(name)]);
					var first = name.substring(0, name.indexOf(" "));
					// document.getElementById('mostComments').innerHTML += ' ' + first + "'s Statuses";
					// document.getElementById('mostLikes').innerHTML += ' ' + first + "'s Statuses";
					// Get the relationship status of the friend
					FB.api((id + '?fields=relationship_status,significant_other'), function(response) {
						if (response.relationship_status != null) {
							var status = response.relationship_status;
							var so;
							if (response.significant_other != null)
								so = response.significant_other.name;
							var connector;
							var to;
							if (so != null) {
								if (status == "Married" || status == "Widowed" || status == "Separated" || status == "Divorced")
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
						
						#####RELATIONSHIP STATUS#####
						
						*/
						
						//document.getElementById("relationship").innerHTML=string + '.';
						
						///////////////
						// INSERTION //
						//////////////
						document.getElementById("relationship_status").innerHTML += string;
						///////////////
						// INSERTION //
						//////////////
						
					});
					
					// Get a list of the liked pages of the user
					FB.api((id + '/likes?since=' + since), function(response) {
						// If no likes print that out
						if (response.data.length == 0) {
							document.getElementById('interest_1').innerHTML="No likes found";
						}
						// Get 1-3 likes
						var index = Math.min(response.data.length, 3);
						for (var i = 0; i < index; i++) {
							document.getElementById('interest_' + (i + 1)).innerHTML+=response.data[i].name;
						}
						if (index < 3) {
							for (var i = 3; i > index; i--) {
								document.getElementById('interest_' + (i + 1)).innerHTML+='No liked page in  the timeframe given';
							}
						}
					});
					
					// Get most recent location of user
					FB.api((id + '?fields=location'), function(response) {
						if (response['location'] != null) {
							///
							document.getElementById('location').innerHTML='<p style="margin:0;padding:0">' + response.location.name + '</p>';
						} else
							document.getElementById('location').innerHTML="No Location Data";
					});
					
					FB.api((id + "/photos/uploaded?limit=2500&fields=likes.limit(1000),comments.limit(1000),source&since=" + since), function(response) {
						var pictures = new Array();
						var picLikes = new Array();
						var picComments = new Array();
						// console.log(response);
						// if (response.data.length == 0)
							// alert("Warning: friend may have app privacy settings set that interfere with the running of this application");
						// else {
							for (var i = 0; i < response.data.length; i++) {
								var likes = 0;
								var dat = response.data[i];
								if (dat.likes != null)
									likes = dat.likes.data.length; //EDITED, CHECK
								pictures[i] = new Array(dat.source, likes);
								for (var j = 0; j < likes; j++) {
									var liker = dat.likes.data[j].name;
									var found = false;
									for (var k = 0; k < picLikes.length; k++) {
										if (picLikes[k][0] == liker) {
											picLikes[k][1]++;
											found = true;
											break;
										}
									}
									if (!found)
										picLikes.push(new Array(liker, 1));
								}
								var comments = 0;
								if (dat.comments != null) {
									comments = (dat.comments.data).length;
								}
								for (var j = 0; j < comments; j++) {
									var commentor = dat.comments.data[j].from.name;
									if (commentor != name) {
										var found = false;
										for (var k = 0; k < picComments.length; k++) {
											if (picComments[k][0] == commentor) {
												picComments[k][1]++;
												found = true;
												break;
											}
										}
										if (!found)
											picComments.push(new Array(commentor, 1));
									}
								}
							}
							// console.log(picLikes);
							// console.log(picComments);
							// Sort the pictures based on the number of likes (index 1)
							pictures.sort((function(index){
								return function(a, b) {
									return (a[index] === b[index] ? 0 : (a[index] > b[index] ? -1 : 1));
								};
							})(1));
							/*
							
							#####PHOTOS#####
							
							*/
							
							///////////////
							// INSERTION //
							//////////////
							// console.log(name);
							document.getElementById('uploaded').innerHTML=name.substring(0, name.indexOf(" ")) + "'s Most Popular Pictures";
							index = Math.min(3, pictures.length);
							for (var i = 1; i <= index; i++) {
									var current_picture = document.getElementById(("pic_" + i));
									var current_name = document.getElementById(("pic_name_" + i));
								
								current_picture.src = pictures[i - 1][0];
								current_name.innerHTML = pictures[i - 1][1] + " likes";
							}
							if (index < 3) {
								for (var i = 3; i > index; i--) {
									document.getElementById('pic1' + i).innerHTML = '<p style="text-align:center;maring-top:20%;">No tagged picture in timeframe given</p>';
								}
							}
						// }
						
						FB.api((id + "/photos/tagged?limit=400&fields=likes.limit(10000),source&since=" + since), function(response) {
							var taggedPictures = new Array();
							// if (response.data.length == 0)
								// alert("Warning: friend may have app privacy settings set that interfere with the running of this application");
							for (var i = 0; i < response.data.length; i++) {
								var likes;
								var dat = response.data[i];
								if (dat.likes != null)
									likes = dat.likes.data.length; //EDITED, CHECK
								else
									likes = 0;
								taggedPictures[i] = new Array(dat.source, likes);
							}
							
							// Sort the pictures based on the number of likes (index 1)
							taggedPictures.sort((function(index){
								return function(a, b) {
									return (a[index] === b[index] ? 0 : (a[index] > b[index] ? -1 : 1));
								};
							})(1));
							/*
							
							#####PHOTOS#####
							
							*/
							
							///////////////
							// INSERTION //
							//////////////
							document.getElementById('tagged').innerHTML=("Most Popular Pictures of " + name.substring(0, name.indexOf(" ")));
							index = Math.min(3, taggedPictures.length);
							for (var i = 1; i <= index; i++) {
									var current_picture = document.getElementById(("pic2_" + i));
									var current_name = document.getElementById(("pic2_name_" + i));
								
									current_picture.src = taggedPictures[i - 1][0];
									current_name.innerHTML = taggedPictures[i - 1][1] + " likes";
							}
							if (index < 3) {
								for (var i = 3; i > index; i--) {
									document.getElementById('pic2' + i).innerHTML = '<p style="text-align:center">No uploaded picture in time grame given</p>';
								}
							}
						});
					
					// Get most liked posts
					/*
					FB.api((id + "/statuses?limit=10000&fields=likes.limit(10000),message"), function(response) {
						// document.getElementById('demo2').innerHTML=(response.data[0]["message"]);
						// console.log('LIKES ARRAY');
						// console.log(response);
						var statuses = new Array();
						for (var i = 0; i < response.data.length; i++) {
							var likes;
							if (response.data[i].likes != null) {
								likes = response.data[i]['likes'].data.length;
							} else {
								likes = 0;
							}
							var message = response.data[i].message;
							if (message == null)
								message = "(Hidden/Deleted Status)"
							// convert new line characters into break tags
							message = message.replace(/\n/g, '<br />');
							statuses[i] = new Array(message, likes);
							// document.getElementById("demo2").innerHTML+=(gg[i][0] + ' Likes: ' + gg[i][1] + '<br />');
						}
						// Sort the statuses based on the number of likes
						statuses.sort((function(index){
							return function(a, b) {
								return (a[index] === b[index] ? 0 : (a[index] > b[index] ? -1 : 1));
							};
						})(1));
						
						for (var i = 1; i <= 3; i++) {
							document.getElementById("status_likes_" + i).innerHTML = statuses[i - 1][1] + " likes";
							document.getElementById("status_" + i).innerHTML = statuses[i - 1][0];
						}
					});
					*/
					
						// Get names of commentors
						FB.api((id + "/statuses?limit=10000&fields=comments.limit(10000),from&since=" + since), function(response) {
							var comment = new Array();
							// commentnames = new Array();
							// if (response.data.length == 0)
								// alert("Either this friend has no status updates, or app security settings are preventing this application from executing");
							for (var i = 0; i < response.data.length; i++) {
								
								
									/* FOR REFERENCE
									
									var found = false;
									for (var k = 0; k < picLikes.length; k++) {
										if (picLikes[k][0] == liker) {
											picLikes[k][1]++;
											found = true;
											break;
										}
									}
									if (!found)
										picLikes.push(new Array(liker, 1));
									
									*/
								
								
								if (response.data[i].comments != null) {
									comment[i] = response.data[i].comments.data; // returns the data array containing comments
									for (var j = 0; j < comment[i].length; j++) {
										var dudename = comment[i][j].from.name;
										if (dudename != name) {
											var found = false;
											for (var k = 0; k < picComments.length; k++) {
												if (picComments[k][0] == dudename) {
													picComments[k][1]++;
													found = true;
													break;
												}
											}
											if (!found)
												picComments.push(new Array(dudename, 1));
										}
											
											/*
											var index = getIndex(commentnames, dudename);
											if (index == -1) {
												commentnames.push(new Array(dudename, 1));
											} else {
												commentnames[index][1]++;
											}
										}
										*/
									}
								}
							}
							// Sort comments based on 
							picComments.sort((function(index){
								return function(a, b) {
									return (a[index] === b[index] ? 0 : (a[index] > b[index] ? -1 : 1));
								};
							})(1));
							/*
							
							#####MOST COMMENTS#####
							
							*/
							// console.log(picComments);
							index = Math.min(4, picComments.length);
							for (var i = 1; i <= index; i++) {
								//document.getElementById('demo3').innerHTML+=(commentnames[i][0] + ' count ' + commentnames[i][1] + '<br />');
								var num = parseInt(100.0 * picComments[i - 1][1] / picComments[0][1], 10) + "%";
								document.getElementById("comments_graph_name_" + i).innerHTML =  picComments[i - 1][0]+ " / " + picComments[i - 1][1];
								document.getElementById("comments_graph_" + i).style.width = num;
								
								
								//document.getElementById("comments_graph_" + i).style.width = "100%";
							}
							
							function getIndex(commentnames, dudename) {
								for (var i = 0; i < commentnames.length; i++) {
									if (commentnames[i][0] == dudename) {
										return i;
									}
								}
								return -1;
							}
							
							// get like information from each person
							FB.api((id + "/statuses?limit=10000&fields=likes.limit(10000),comments.limit(10000),message&since=" + since), function(response) {
								var comment = new Array();
								var statuses = new Array();
								likecount = new Array();
								for (var i = 0; i < response.data.length; i++) {
									
									//////////////////////////
									////Most Popular Posts////
									//////////////////////////
									var message = response.data[i].message;
									// console.log(message);
									if (message == null)
										message = "(Hidden/Deleted Status)"
									// convert new line characters into break tags
									message = message.replace(/\n/g, '<br />');
									var likes = 0;
									if (response.data[i].likes != null)
										likes = response.data[i].likes.data.length;
									statuses[i] = new Array(message, likes);
									
									////////////////////////
									////Individual Likes////
									////////////////////////
									
									/* FOR REFERENCE
									
									var found = false;
									for (var k = 0; k < picLikes.length; k++) {
										if (picLikes[k][0] == liker) {
											picLikes[k][1]++;
											found = true;
											break;
										}
									}
									if (!found)
										picLikes.push(new Array(liker, 1));
									
									*/
									
									if (response.data[i].likes != null) {
										comment[i] = response.data[i].likes.data; // returns the data array containing likes
										for (var j = 0; j < comment[i].length; j++) {
											var dudename = comment[i][j].name;
											var found = false;
											for (var k = 0; k < picLikes.length; k++) {
												if (picLikes[k][0] == dudename) {
													picLikes[k][1]++;
													found=true;
													break;
												}
											}
											if (!found) 
												picLikes.push(new Array(dudename, 1));
										}
										/*
											if (dudename != name) {
												var index = getIndex(likecount, dudename);
												if (index == -1) {
													likecount.push(new Array(dudename, 1));
												} else {
													likecount[index][1]++;
												}
											}
										}
										*/
									}
								}
								
								picLikes.sort((function(index){
									return function(a, b) {
										return (a[index] === b[index] ? 0 : (a[index] > b[index] ? -1 : 1));
									};
								})(1));
								/*
								
								#####MOST LIKES#####
								
								*/
								index = Math.min(4, picLikes.length);
								for (var i = 1; i <= index; i++) {
									//document.getElementById('demo6').innerHTML+=(likecount[i][0] + ' count ' + likecount[i][1] + '<br />');
									var num = parseInt(100.0 * picLikes[i - 1][1] / picLikes[0][1], 10) + "%";
									document.getElementById("likes_graph_name_" + i).innerHTML =  picLikes[i - 1][0]+ " / " + picLikes[i - 1][1];
									document.getElementById("likes_graph_" + i).style.width = num;
									
									
									//document.getElementById("comments_graph_" + i).style.width = "100%";
								}
								
								// Sort the statuses based on the number of likes
								statuses.sort((function(index){
									return function(a, b) {
										return (a[index] === b[index] ? 0 : (a[index] > b[index] ? -1 : 1));
									};
								})(1));
								
								index = Math.min(3, statuses.length);
								
								for (var i = 0; i < index; i++) {
									document.getElementById("status_likes_" + (i+1)).innerHTML = statuses[i][1] + " likes";
									document.getElementById("status_" + (i+1)).innerHTML = statuses[i][0];
								}
								if (index < 3) {
									for (var i = 3; i > index; i--) {
										document.getElementById("status_likes_" + i).innerHTML = '<h2>-</h2>';
										document.getElementById("status_" + i).innerHTML = 'No status in the timeframe given';
									}
								}
								
								
								/*
								function getIndex(likecount, dudename) {
									for (var i = 0; i < likecount.length; i++) {
										if (likecount[i][0] == dudename) {
											return i;
										}
									}
									return -1;
								}
								*/
								
								// var total;
								
								var total = new Array();
								for (var i = 0; i < picLikes.length; i++) {
									total[i] = new Array(picLikes[i][0], picLikes[i][1], 0, 0);
								}
								for (var i = 0; i < picComments.length; i++) {
									var found = false;
									var currentName = picComments[i][0];
									var currentComments = picComments[i][1];
									for (var j = 0; j < picLikes.length; j++) {
										if (total[j][0] == currentName) {
											found = true;
											total[j][2] = currentComments;
											break;
										}
									}
									if (!found) {
										total.push(new Array(currentName, 0, currentComments, 0));
									}
								}
								for (var i = 0; i < total.length; i++) {
									total[i][3] = (total[i][1] + (total[i][2] * 1.5));
								}
								// Sorted list of "best" friends where 1 comment is equal to 1.5 likes
								total.sort((function(index){
									return function(a, b) {
										return (a[index] === b[index] ? 0 : (a[index] > b[index] ? -1 : 1));
									};
								})(3));
								/*
								
								#####TOP FRIENDS#####
								
								*/
								index = Math.min(total.length, 3);
								for (var i = 0; i < index; i++) {
									document.getElementById("best_friend_" + (i + 1)).innerHTML = total[i][0];
									document.getElementById("friend_" + (i + 1)).innerHTML = total[i][2] + ' comments and ' + total[i][1] + ' likes';
								}
								if (index < 3) {
									for (var i = 3; i > index; i--) {
										document.getElementById("best_friend_" + i).innerHTML = '-';
										document.getElementById("friend_" + i).innerHTML = 'No friend for the timeframe given';
									}
								}
								
							});
						});
					
					
					});
					
					// getStatuses();
				});
				
			});
			
		} else {
			/*
			
			#####DISPLAY IF NO  PARAMETER PASSED#####
			
			*/
			document.getElementById('noParam').style.display="block";
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
<div id="banner">
	<img src="banner.png" alt="" />
</div>
<div id="wrapper">
    <div class="jumbotron hero-spacer">
        
        <div class="ui-widget" id="test">
        <form style="float: right; clear: right; margin-top: -20px" action="">
            <label for="tags">Search Friends: </label>
            <input id="tags" name="name" /> <br /> from
            <select name="since">
            	<option id="week" value="604800">The Past Week</option>
                <option id="month" value="2629743">The Past Month</option>
                <option id="year" value="31556926">The Past Year</option>
                <option id="all" value="All" selected="selected">The Origin of the Account</option>
            </select>
            <input type="submit" />
            </form>
        <p id="invalid"></p>
        <!--
        <p class="muted credit"><fb:login-button show-faces="false" scope="basic_info, friends_photos, friends_status, friends_online_presence, friends_relationships, user_photos, user_status, user_relationships, user_likes, friends_likes, user_location, friends_location" width="300px"></fb:login-button></p> 
        -->
        
        
    </div>
    <h1 id="current_name"></h1>
    </div>
    
      
    <!--main-->
    <div class="container" id="main">
    	<div id="loggedIn">
       <div class="row">
       <div class="col-md-4 col-sm-6">
            <div class="panel panel-default">
              <div class="panel-heading"><a href="" class="pull-right"></a> <h4>Likes</h4></div>
            <div class="panel-body">
                  <div class="list-group" id="list-group-items"> 
                    <a href="#" class="list-group-item" id="interest_1"></a>
                    <a href="#" class="list-group-item" id="interest_2"></a>
                    <a href="#" class="list-group-item" id="interest_3"></a>
                  </div>
                </div>
          </div>
    
         
     
    
      </div>
        <div class="col-md-4 col-sm-6">
             
    
             <div class="panel panel-default">
               <div class="panel-heading"><a href="" class="pull-right"></a> <h4>Relationship Status: </h4></div>
            <div class="panel-body">
                  <p id="relationship_status"><img src="http://www.iconarchive.com/download/i66644/designbolts/free-valentine-heart/Heart-Shadow.ico" style="width: 40px" class="img-circle pull-right" ></p>
                  <div class="clearfix"></div>
                  <hr>
                 
                </div>
             </div>
          
    
        </div>
        <div class="col-md-4 col-sm-6">
             <div class="panel panel-default">
               <div class="panel-heading"><a href="" class="pull-right"></a> <h4>Most Recent Location</h4></div>
            <div class="panel-body">
                  <ul class="list-group">
                  <li class="list-group-item" id="location"></li>
                  </ul>
                </div>
          </div>
          
        </div>
      </div><!--/row-->
          <hr>
      
      <div class="row">
          <h2 id="tagged"></h2><h3>(out of 400 most recent)</h3>
      
         <div class="col-sm-4 col-xs-6">
          
            <div class="panel panel-default">
              <div class="panel-thumbnail" id="pic21"><img id="pic2_1" src="" class="img-responsive"></div>
              <div class="panel-body">
                <p class="lead" id="pic2_name_1"></p>
    
              </div>
            </div>
    
            
          </div><!--/col-->
          
          <div class="col-sm-4 col-xs-6">
          
            <div class="panel panel-default">
              <div class="panel-thumbnail" id="pic22"><img id="pic2_2" src="" class="img-responsive"></div>
              <div class="panel-body">
                <p class="lead" id="pic2_name_2"></p>
              </div>
            </div>
    
            
          </div><!--/col-->
          
          <div class="col-sm-4 col-xs-6">
          
            <div class="panel panel-default">
              <div class="panel-thumbnail" id="pic23"><img id="pic2_3" src="" class="img-responsive"></div>
              <div class="panel-body">
                <p class="lead" id="pic2_name_3"></p>
              </div>
            </div>
    
          
          </div>
      </div>
    <hr>
      
      <div class="row">
          <h2 id="uploaded"></h2>
      
         <div class="col-sm-4 col-xs-6">
          
            <div class="panel panel-default">
              <div class="panel-thumbnail" id="pic11"><img id="pic_1" src="" class="img-responsive"></div>
              <div class="panel-body">
                <p class="lead" id="pic_name_1"></p>
    
              </div>
            </div>
    
            
          </div><!--/col-->
          
          <div class="col-sm-4 col-xs-6">
          
            <div class="panel panel-default">
              <div class="panel-thumbnail" id="pic12"><img id="pic_2" src="" class="img-responsive"></div>
              <div class="panel-body">
                <p class="lead" id="pic_name_2"></p>
              </div>
            </div>
    
            
          </div><!--/col-->
          
          <div class="col-sm-4 col-xs-6">
          
            <div class="panel panel-default">
              <div class="panel-thumbnail" id="pic13"><img id="pic_3" src="" class="img-responsive"></div>
              <div class="panel-body">
                <p class="lead" id="pic_name_3"></p>
              </div>
            </div>
    
          
          </div>
      </div>
    <hr>
      
      
      <div class="row">
        <div class="col-md-12"><h2>Most Liked Posts</h2></div>
        <div class="col-md-4 col-sm-6">
          <div class="panel panel-default">
               <div class="panel-heading"> <h4 id="status_likes_1"></h4></div>
            <div class="panel-body">
                  <div class="clearfix"></div>
                  <p id="status_1"></p>
                  
                </div>
             </div> 
        </div>
        
            <div class="col-md-4 col-sm-6">
          <div class="panel panel-default">
               <div class="panel-heading"> <h4 id="status_likes_2"></h4></div>
            <div class="panel-body">
                  <div class="clearfix"></div>
                  <p id="status_2"></p>
                  
                </div>
             </div> 
        </div>
        
            <div class="col-md-4 col-sm-6">
          <div class="panel panel-default">
               <div class="panel-heading"> <h4 id="status_likes_3"></h4></div>
            <div class="panel-body">
                  <div class="clearfix"></div>
                  <p id="status_3"></p>
                  
                </div>
             </div> 
        </div>
      </div>
    
    <hr>   
    
    
          <div class="row">
        <div class="col-md-12"><h2>Best Friends</h2></div>
        <div class="col-md-4 col-sm-6">
          <div class="panel panel-default">
               <div class="panel-heading"> <h4 id="best_friend_1"></h4></div>
            <div class="panel-body">
                  <div class="clearfix"></div>
                  <p id="friend_1"></p>
                  
                </div>
             </div> 
        </div>
        
            <div class="col-md-4 col-sm-6">
          <div class="panel panel-default">
               <div class="panel-heading"> <h4 id="best_friend_2"></h4></div>
            <div class="panel-body">
                  <div class="clearfix"></div>
                  <p id="friend_2"></p>
                  
                </div>
             </div> 
        </div>
        
            <div class="col-md-4 col-sm-6">
          <div class="panel panel-default">
               <div class="panel-heading"> <h4 id="best_friend_3"></h4></div>
            <div class="panel-body">
                  <div class="clearfix"></div>
                  <p id="friend_3"></p>
                  
                </div>
             </div> 
        </div>
      </div>
    
    <hr>  
     
        
    <!--/col-->
    
    
    <hr>  
        <div class="row">
          <!--<div class="col-md-12"><h2>Graphs</h2></div> -->
            <div class="col-md-12">
    
            </div>
    
                   <div class="col-md-6 col-sm-6">
          <div class="panel panel-default">
               <div class="panel-heading"><a href="" class="pull-right"></a> <h4 id="mostComments">Biggest Commenters</h4></div>
            <div class="panel-body">
                  <span class = "bar_names" id="comments_graph_name_1">  </span>
                  <div class="progress">
                    <div class="progress-bar progress-bar-info" id="comments_graph_1" style="" title="Stuff"></div>
                  </div>
                  <span class = "bar_names" id="comments_graph_name_2">  </span>
                  <div class="progress">
                    <div class="progress-bar progress-bar-success" id="comments_graph_2" style="" title="stuff 2"></div>
                  </div>
                  <span class = "bar_names" id="comments_graph_name_3">  </span>
                  <div class="progress">
                    <div class="progress-bar progress-bar-warning" id="comments_graph_3" style="" title="stuff 3"></div>
                  </div>
                  <span class = "bar_names" id="comments_graph_name_4">  </span>
                  <div class="progress">
                    <div class="progress-bar progress-bar-danger" id="comments_graph_4" style="" stuff="stuff4"></div>
                  </div>
                  
                </div>
             </div> 
        </div>         
                            
                            
                            
     <div class="col-md-6 col-sm-6">
          <div class="panel panel-default">
               <div class="panel-heading"><a href="" class="pull-right"></a> <h4 id="mostLikes">Most Likes</h4></div>
            <div class="panel-body">
                  <span class = "bar_names" id="likes_graph_name_1">  </span>
                  <div class="progress">
                    <div id="likes_graph_1" class="progress-bar progress-bar-info" style="width: 0" title="Stuff"></div>
                  </div>
                  <span class = "bar_names" id="likes_graph_name_2">  </span>
                  <div class="progress">
                    <div id="likes_graph_2" class="progress-bar progress-bar-success" style="width: 0" title="stuff 2"></div>
                  </div>
                  <span class = "bar_names" id="likes_graph_name_3">  </span>
                  <div class="progress">
                    <div id="likes_graph_3" class="progress-bar progress-bar-warning" style="width: 0" title="stuff 3"></div>
                  </div>
                  <span class = "bar_names" id="likes_graph_name_4">  </span>
                  <div class="progress">
                    <div id="likes_graph_4" class="progress-bar progress-bar-danger" style="width: 0" stuff="stuff4"></div>
                  </div>
                  
                </div>
             </div> 
        </div>
        </div>
      </div>
      <div id="login">
      	<div class="fb-login-button" scope="basic_info, friends_photos, friends_status, friends_online_presence, friends_relationships, 
        			user_photos, user_status, user_relationships, user_likes, friends_likes, user_location, friends_location" 
                    data-size="xlarge" data-show-faces="false" data-auto-logout-link="true"></div>
        </div>
        
        <div id="noParam" style="width: 100%;text-align:center;font-family:"Myriad Pro","Lucidia Grande", Helvetica, sans-serif;font-size:24pt;margin-top: 10%">
        	<h1>Welcome to QuickView! Enter a name in the search box to the right to get started!</h1>
        </div>
        <!--playground-->
        
        
        <div class="clearfix"></div>
          
      </div>
     </div>
</body>
</html>
