<html>
<head>
	<title>Test App</title>
        <!-- CSS -->
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
	<link href="index.css" rel="stylesheet" type="text/css" />
    <style type="text/css">

      /* Sticky footer styles
      -------------------------------------------------- */

      html,
      body {
        height: 100%;
        /* The html and body elements cannot have any padding or margin. */
      }

      /* Wrapper for page content to push down footer */
      #wrap {
        min-height: 100%;
        height: auto !important;
        height: 100%;
        /* Negative indent footer by it's height */
        margin: 0 auto -60px;
      }

      /* Set the fixed height of the footer here */
      #push,
      #footer {
        height: 60px;
      }
      #footer {
        background-color: #f5f5f5;
      }

      /* Lastly, apply responsive CSS fixes as necessary */
      @media (max-width: 767px) {
        #footer {
          margin-left: -20px;
          margin-right: -20px;
          padding-left: 20px;
          padding-right: 20px;
        }
      }



      /* Custom page CSS
      -------------------------------------------------- */
      /* Not required for template or sticky footer method. */

      .container {
        width: auto;
        max-width: 680px;
      }
      .container .credit {
        margin: 20px 0;
      }

    </style>
    
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
	  printNames();
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
		name = name.replace("+", " ");
		name = name.replace("+", " ");
	console.log("name: " + name);
	FB.api('me/friends', function(response) {
	console.log('printPictures called');
		var names = new Array();
		var ids = new Array();
		for (var i = 0; i < response.data.length; i++) {
			names[i] = response.data[i]['name'];
			ids[i] = response.data[i]['id'];
		}
		getID(name.toLowerCase(), names, ids);
		console.log(ids[names.indexOf(name)] + "/photos");
		console.log(ids[names.indexOf(name)]);
		FB.api((ids[names.indexOf(name)] + "/photos"), function(response) {
			var pictures = new Array();
			for (var i = 0; i < 5; i++) {
				pictures[i] = response.data[i]['source'];
				console.log(response.data[i]['source']);
				document.getElementById('images').innerHTML += ('<img src="' + response.data[i]['source'] + '" alt="image" />');
			}
		});
		FB.api((ids[names.indexOf(name)] + "/statuses"), function(response) {
			console.log(response);
			// document.getElementById('demo2').innerHTML=(response.data[0]["message"]); 
			var gg = new Array();
			for (var i = 0; i < response.data.length; i++) {
				var likes;
				if (response.data[i]['likes'] != null) {
					likes = response.data[i]['likes']['data'].length;
				} else {
					likes = 0;
				}
				var message = response.data[i]['message'];
				message = message.replace(/\n/g, '<br />');
				console.log(message);
				gg[i] = new Array(message, likes);
				console.log(response.data[i]['message']);
				// document.getElementById("demo2").innerHTML+=(gg[i][0] + ' Likes: ' + gg[i][1] + '<br />');
			}
			gg.sort((function(index){
				return function(a, b) {
					return (a[index] === b[index] ? 0 : (a[index] > b[index] ? -1 : 1));
				};
			})(1));
			for (var i = 0; i < gg.length; i++) {
				document.getElementById('demo2').innerHTML+=(gg[i][0] + ' Likes: ' + gg[i][1] + '<br />');
			}
		});
		// getStatuses();
	});
	} else {
		console.log("no parameter passed");
	}
  };
  
    var gg;
  function getStatuses() {
  	FB.api((id + "/statuses"), function(response) {
		console.log(id);
		console.log(response);
		console.log(id + '/statuses/');
		gg = new Array();
  		// for (var i = 0; i < response.data.length; i++) {
  		// 	statuses[i] = response.data['message'];
  		// }
  		// for (var i = 0; i < response.data.length; i++) {
  		// 	likeCount[i] = response.data['likes']['data'].length;
  		// }
  		for (var i = 0; i < 1; i++) {
  			gg[i] = {message: response.data['message'], likes: response.data['likes']['data'].length};
			/*
  			document.getElementbyId("demo2").innerHTML+=(gg[i].message);
  			document.getElementbyId("demo4").innerHTML+=(gg[i].likes);
			*/
  			document.write(gg[i].message);
  			document.write(gg[i].likes);
  		}
  		gg.sort(function (a, b) {
  			if (a.likes > b.likes) {
  				return 1;
  			} else if (a.likes < b.likes) {
  				return -1;
  			} else {
  				return 0;
  			}
  		});
  	});
  }
  
  function printNames() {
  var ids = new Array();
  $(function() {
	FB.api('/me/friends', function(response) {
		/*
		var text = '<form action="https://webster.cs.washington.edu/params.php"><select name="name">';
		for (var i = 0; i < response.data.length; i++) {
			text += ('<option>' + response.data[i]['name'] + '<img src="https://graph.facebook.com/' + response.data[i]['id'] + '/picture" alt="pic" /></option>');
		}
		document.getElementById("text").innerHTML = (text + '</select><input type="submit" value="Go" /></form>');
		*/
		var availableTags = new Array(); 
		var ids = new Array();
		for (var i = 0; i < response.data.length; i++) {
			availableTags[i] = response.data[i]['name'];
			ids[i] = response.data[i]['id'];
			/*
			document.getElementById("text").innerHTML += (response.data[i]['name'] + '<img src="https://graph.facebook.com/' + response.data[i]['id'] + '/picture" alt="pic" /><br />');
			*/
			availableTags.sort();
			$( "#tags" ).autocomplete({
			  source: availableTags
			});
		}
	});
  });
	
  }
};

	var id;
	function getID (input, names, ids) {
		var index;
	    	for(var i = 0; i < names.length; i++) {
		  	var lower = names[i].toLowerCase();
		  	if (lower == input) {
		  		index = i;	 
		  	}
	  	}
	  	id = ids[index];
	}

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
<!--
  Below we include the Login Button social plugin. This button uses the JavaScript SDK to
  present a graphical Login button that triggers the FB.login() function when clicked. --><!-- Part 1: Wrap all page content here -->
<div id="wrap">

  <!-- Begin page content -->
  <div class="container">
    <div class="page-header">
      <h1>Name</h1>
    </div>
    <p class="lead" id="images"></p>
    <p id="text"></p>
    <div class="ui-widget" id="test">
    <form action="">
  		<label for="tags">Search: </label>
  		<input id="tags" name="name" />
        <input type="submit" />
        </form>
	</div>
    <p>Use <a href="./sticky-footer-navbar.html">the sticky footer</a> with a fixed navbar if need be, too.</p>
	<p class="muted credit"><fb:login-button show-faces="true" scope="basic_info, friends_photos, friends_status" width="300" max-rows="1"></fb:login-button></p>
	<p id="demo2"></p>
	<p id="demo4"></p>
  </div>

  <div id="push"></div>
</div>

<div id="footer">
  <div class="container">
  </div>
</div>
    	
</div>
</body>
</html>
