Skip to content
 
This repository
Explore
Gist
Blog
Help
danrahn danrahn
 
!

You don't have any verified emails. We recommend verifying at least one email.
Email verification helps our support team help you in case you have any email issues or lose your password.
5  Unwatch
Star 0 Fork 4PUBLICaarongu/FbSwag
 branch: master  FbSwag / index.php 
danrahn danrahn 44 minutes ago Create index.php
1 contributor
 file  168 lines (146 sloc)  5.031 kb  Open EditRawBlameHistory Delete
1
2
3
4
5
6
7
8
9
10
11
12
13
14
15
16
17
18
19
20
21
22
23
24
25
26
27
28
29
30
31
32
33
34
35
36
37
38
39
40
41
42
43
44
45
46
47
48
49
50
51
52
53
54
55
56
57
58
59
60
61
62
63
64
65
66
67
68
69
70
71
72
73
74
75
76
77
78
79
80
81
82
83
84
85
86
87
88
89
90
91
92
93
94
95
96
97
98
99
100
101
102
103
104
105
106
107
108
109
110
111
112
113
114
115
116
117
118
119
120
121
122
123
124
125
126
127
128
129
130
131
132
133
134
135
136
137
138
139
140
141
142
143
144
145
146
147
148
149
150
151
152
153
154
155
156
157
158
159
160
161
162
163
164
165
166
167
<html>
<head>
	<title>Test App</title>
        <!-- CSS -->
    <link href="css/bootstrap.css" rel="stylesheet" type="text/css">
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
  window.fbAsyncInit = function() {
  FB.init({
    appId      : '757126884332239',
    status     : true, // check login status
    cookie     : true, // enable cookies to allow the server to access the session
    xfbml      : true  // parse XFBML
  });

FB.Event.subscribe('auth.authResponseChange', function(response) {
    if (response.status === 'connected') {
      console.log('Logged in');
	  printNames();
	  testAPI();
    } else {
      FB.login();
    }
  });
  
  function printNames() {
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
		for (var i = 0; i < response.data.length; i++) {
			availableTags[i] = response.data[i]['name'];
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
      <h1>Sticky footer</h1>
    </div>
    <p class="lead">Pin a fixed-height footer to the bottom of the viewport in desktop browsers with this custom HTML and CSS.</p>
    <p id="text"></p>
    <div class="ui-widget">
    <form action="//webster.cs.washington.edu/params.php">
  		<label for="tags">Search: </label>
  		<input id="tags" name="name" />
        <input type="submit" />
        </form>
	</div>
    <p>Use <a href="./sticky-footer-navbar.html">the sticky footer</a> with a fixed navbar if need be, too.</p>
	<p class="muted credit"><fb:login-button show-faces="true" width="300" max-rows="1"></fb:login-button></p>
  </div>

  <div id="push"></div>
</div>

<div id="footer">
  <div class="container">
    <p class="muted credit"><fb:login-button show-faces="true" width="300" max-rows="1"></fb:login-button></p>
  </div>
</div>
    	
</div>
</body>
</html>
Status API Training Shop Blog About © 2014 GitHub, Inc. Terms Privacy Security Contact 
