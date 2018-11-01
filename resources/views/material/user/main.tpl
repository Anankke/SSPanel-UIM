<!DOCTYPE html>
<html lang="zh-cn">
<head>
	<meta charset="UTF-8">
	<meta content="IE=edge" http-equiv="X-UA-Compatible">
	<meta content="initial-scale=1.0, maximum-scale=1.0, user-scalable=no, width=device-width" name="viewport">
	<meta name="theme-color" content="#ff9800">
	<title>{$config["appName"]}</title>


	<!-- css -->
	<link href="/theme/material/css/base.min.css" rel="stylesheet">
	<link href="/theme/material/css/project.min.css" rel="stylesheet">
	<link href="https://fonts.loli.net/css?family=Roboto:300,300italic,400,400italic,500,500italic" rel="stylesheet">
	<link href="https://fonts.loli.net/css?family=Material+Icons" rel="stylesheet">

 <style>
    body {
        background: #eee;
    }

    @keyframes mysnow {
        0% {
            bottom: 100%;
            opacity: 0;
        }
        50% {
            opacity: 1;
            transform: rotate(1080deg);
        }
        100% {
            transform: rotate(0deg);
            opacity: 0;
            bottom: 0;
        }
    }

    @-webkit-keyframes mysnow {
        0% {
            bottom: 100%;
            opacity: 0;
        }
        50% {
            opacity: 1;
            -webkit-transform: rotate(1080deg);
        }
        100% {
            -webkit-transform: rotate(0deg);
            opacity: 0;
            bottom: 0;
        }
    }

    @-moz-keyframes mysnow {
        0% {
            bottom: 100%;
            opacity: 0;
        }
        50% {
            opacity: 1;
            -moz-transform: rotate(1080deg);
        }
        100% {
            -moz-transform: rotate(0deg);
            opacity: 0;
            bottom: 0;
        }
    }

    @-ms-keyframes mysnow {
        0% {
            bottom: 100%;
            opacity: 0;
        }
        50% {
            opacity: 1;
            -ms-transform: rotate(1080deg);
        }
        100% {
            -ms-transform: rotate(0deg);
            opacity: 0;
            bottom: 0;
        }
    }

    @-o-keyframes mysnow {
        0% {
            bottom: 100%;
            opacity: 0;
        }
        50% {
            opacity: 1;
            -o-transform: rotate(1080deg);
        }
        100% {
            -o-transform: rotate(0deg);
            opacity: 0;
            bottom: 0;
        }
    }

    .roll {
        position: absolute;
        opacity: 0;
        animation: mysnow 5s;
        -webkit-animation: mysnow 5s;
        -moz-animation: mysnow 5s;
        -ms-animation: mysnow 5s;
        -o-animation: mysnow 5s;
        height: 80px;
    }

    .div {
        position: fixed;
    }
    </style>



	<!-- favicon -->
	<!-- ... -->
	<style>
		.pagination {
			display:inline-block;
			padding-left:0;
			margin:20px 0;
			border-radius:4px
		}
		.pagination>li {
			display:inline
		}
		.pagination>li>a,.pagination>li>span {
			position:relative;
			float:left;
			padding:6px 12px;
			margin-left:-1px;
			line-height:1.42857143;
			color:#337ab7;
			text-decoration:none;
			background-color:#fff;
			border:1px solid #ddd
		}
		.pagination>li:first-child>a,.pagination>li:first-child>span {
			margin-left:0;
			border-top-left-radius:4px;
			border-bottom-left-radius:4px
		}
		.pagination>li:last-child>a,.pagination>li:last-child>span {
			border-top-right-radius:4px;
			border-bottom-right-radius:4px
		}
		.pagination>li>a:focus,.pagination>li>a:hover,.pagination>li>span:focus,.pagination>li>span:hover {
			color:#23527c;
			background-color:#eee;
			border-color:#ddd
		}
		.pagination>.active>a,.pagination>.active>a:focus,.pagination>.active>a:hover,.pagination>.active>span,.pagination>.active>span:focus,.pagination>.active>span:hover {
			z-index:2;
			color:#fff;
			cursor:default;
			background-color:#337ab7;
			border-color:#337ab7
		}
		.pagination>.disabled>a,.pagination>.disabled>a:focus,.pagination>.disabled>a:hover,.pagination>.disabled>span,.pagination>.disabled>span:focus,.pagination>.disabled>span:hover {
			color:#777;
			cursor:not-allowed;
			background-color:#fff;
			border-color:#ddd
		}
		.pagination-lg>li>a,.pagination-lg>li>span {
			padding:10px 16px;
			font-size:18px
		}
		.pagination-lg>li:first-child>a,.pagination-lg>li:first-child>span {
			border-top-left-radius:6px;
			border-bottom-left-radius:6px
		}
		.pagination-lg>li:last-child>a,.pagination-lg>li:last-child>span {
			border-top-right-radius:6px;
			border-bottom-right-radius:6px
		}
		.pagination-sm>li>a,.pagination-sm>li>span {
			padding:5px 10px;
			font-size:12px
		}
		.pagination-sm>li:first-child>a,.pagination-sm>li:first-child>span {
			border-top-left-radius:3px;
			border-bottom-left-radius:3px
		}
		.pagination-sm>li:last-child>a,.pagination-sm>li:last-child>span {
			border-top-right-radius:3px;
			border-bottom-right-radius:3px
		}
		.pager {
			padding-left:0;
			margin:20px 0;
			text-align:center;
			list-style:none
		}
		.pager li {
			display:inline
		}
		.pager li>a,.pager li>span {
			display:inline-block;
			padding:5px 14px;
			background-color:#fff;
			border:1px solid #ddd;
			border-radius:15px
		}
		.pager li>a:focus,.pager li>a:hover {
			text-decoration:none;
			background-color:#eee
		}
		.pager .next>a,.pager .next>span {
			float:right
		}
		.pager .previous>a,.pager .previous>span {
			float:left
		}
		.pager .disabled>a,.pager .disabled>a:focus,.pager .disabled>a:hover,.pager .disabled>span {
			color:#777;
			cursor:not-allowed;
			background-color:#fff
		}





		.pagination>li>a,
		.pagination>li>span {
		  border: 1px solid white;
		}
		.pagination>li.active>a {
		  background: #f50057;
		  color: #fff;
		}

		.pagination>li>a {
		  background: white;
		  color: #000;
		}


		.pagination > .active > a, .pagination > .active > a:focus, .pagination > .active > a:hover, .pagination > .active > span, .pagination > .active > span:focus, .pagination > .active > span:hover {
			color: #fff;
			background-color: #000;
			border-color: #000;
		}

		.pagination>.active>span {
		  background-color: #f50057;
		  color: #fff;
		  border-color: #fff;
		}



		.pagination > .disabled > span {
		  border-color: #fff;
		}


		pre {
			white-space: pre-wrap;
			word-wrap: break-word;
		}

		.progress-green .progress-bar {
			background-color: #f0231b;
		}

		.progress-green {
			background-color: #000;
		}

		.progress-green .progress-bar {
			background-color: #ff0a00;
		}

		.page-orange .ui-content-header {
			background-image: url(/theme/material/css/images/bg/amber.jpg);
		}

		.content-heading {
			font-weight: 300;
			color: #fff;
		}

		.shop-flex,.label-flex {
	        display: flex;
	        justify-content: space-between;
	        flex-wrap: wrap;
		}
		
		.shop-display {
	        display: none;
        }
		
		.reset-invitelink {
			margin-left: 1em;
		}

		.enable-flag {
			color: #383838;
		}

		.node-icon {
			color: #ff9000;
		}

		.node-alive {
			color: #474747;
		}

		.node-load,.node-mothed {
			color: #828282;
		}

		.node-band {
			color: #aaaaaa;
		}

		.node-tr {
			color: #a5a5a5;
		}

		.node-status {
			color: #c4c4c4;
		}

	</style>

    <style>

.progressbar{
    position:relative;
	display:block;
    width:90%;
    height:20px;
    padding:10px 20px;
    border-bottom:1px solid rgba(255,255,255,0.25);
    border-radius:16px;
	margin:40px auto;
	margin-top: 60px;
}

.progressbar .before{
    position:absolute;
    display:block;
    content:"";
    width:calc(100% - 42px);
    height:18px;
    top:10px;
    left:20px;
	-webkit-border-radius:20px;
    border-radius:20px;
    background:#fff;
	-webkit-box-shadow:inset 0px 0px 6px 0px rgba(78, 78, 78, 0.85);;
    box-shadow:inset 0px 0px 6px 0px rgba(78, 78, 78, 78.85);
	border:1px solid rgba(222,222,222,222.8);
}
.progressbar .bar {
	position:absolute;
	display:block;
	width:0px;
	height:16px;
	top:12px;
	left:22px;
	background:rgb(126,234,25);
	background:-moz-linear-gradient(top,  rgba(126,234,25,1) 0%, rgba(83,173,0,1) 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(126,234,25,1)), color-stop(100%,rgba(83,173,0,1)));
	background:-webkit-linear-gradient(top,  rgba(126,234,25,1) 0%,rgba(83,173,0,1) 100%);
	background:-o-linear-gradient(top,  rgba(126,234,25,1) 0%,rgba(83,173,0,1) 100%);
	background:-ms-linear-gradient(top,  rgba(126,234,25,1) 0%,rgba(83,173,0,1) 100%);
	background:linear-gradient(to bottom,  rgba(126,234,25,1) 0%,rgba(83,173,0,1) 100%);
	filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#7eea19', endColorstr='#53ad00',GradientType=0 );
	-webkit-border-radius:16px;
	border-radius:16px;
	-webkit-box-shadow:0px 0px 12px 0px rgba(126, 234, 25, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
	box-shadow:0px 0px 12px 0px rgba(126, 234, 25, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
	overflow:hidden;
}
.progressbar .label .bar {
	position: relative;
	display: inline-block;
	top: 12%;
	left: 0;
	margin-right: 5px;
	width: 16px;
	
}
.progressbar .label .bar.color {
	box-shadow:0px 0px 5px 0px rgba(126, 234, 25, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
}
.progressbar .label .bar.color2 {
	box-shadow:0px 0px 5px 0px rgba(229, 195, 25, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
}
.progressbar .label .bar.color3 {
	box-shadow:0px 0px 5px 0px rgba(232, 25, 87, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
}
.progressbar .label .bar.color4 {
	box-shadow:0px 0px 5px 0px rgba(24, 109, 226, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
}
.progressbar .bar.color2 {
	background:rgb(229,195,25);
	background:-moz-linear-gradient(top,  rgba(229,195,25,1) 0%, rgba(168,140,0,1) 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(229,195,25,1)), color-stop(100%,rgba(168,140,0,1)));
	background:-webkit-linear-gradient(top,  rgba(229,195,25,1) 0%,rgba(168,140,0,1) 100%);
	background:-o-linear-gradient(top,  rgba(229,195,25,1) 0%,rgba(168,140,0,1) 100%);
	background:-ms-linear-gradient(top,  rgba(229,195,25,1) 0%,rgba(168,140,0,1) 100%);
	background:linear-gradient(to bottom,  rgba(229,195,25,1) 0%,rgba(168,140,0,1) 100%);
	filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#e5c319', endColorstr='#a88c00',GradientType=0 );
	-webkit-box-shadow:0px 0px 12px 0px rgba(229, 195, 25, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
	box-shadow:0px 0px 12px 0px rgba(229, 195, 25, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
}
.progressbar .bar.color3 {
	background:rgb(232,25,87);
	background:-moz-linear-gradient(top,  rgba(232,25,87,1) 0%, rgba(170,0,51,1) 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(232,25,87,1)), color-stop(100%,rgba(170,0,51,1)));
	background:-webkit-linear-gradient(top,  rgba(232,25,87,1) 0%,rgba(170,0,51,1) 100%);
	background:-o-linear-gradient(top,  rgba(232,25,87,1) 0%,rgba(170,0,51,1) 100%);
	background:-ms-linear-gradient(top,  rgba(232,25,87,1) 0%,rgba(170,0,51,1) 100%);
	background:linear-gradient(to bottom,  rgba(232,25,87,1) 0%,rgba(170,0,51,1) 100%);
	filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#e81957', endColorstr='#aa0033',GradientType=0 );
	-webkit-box-shadow:0px 0px 12px 0px rgba(232, 25, 87, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
	box-shadow:0px 0px 12px 0px rgba(232, 25, 87, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
}
.progressbar .bar.color4 {
	background:rgb(24,109,226);
	background:-moz-linear-gradient(top,  rgba(24,109,226,1) 0%, rgba(0,69,165,1) 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(24,109,226,1)), color-stop(100%,rgba(0,69,165,1)));
	background:-webkit-linear-gradient(top,  rgba(24,109,226,1) 0%,rgba(0,69,165,1) 100%);
	background:-o-linear-gradient(top,  rgba(24,109,226,1) 0%,rgba(0,69,165,1) 100%);
	background:-ms-linear-gradient(top,  rgba(24,109,226,1) 0%,rgba(0,69,165,1) 100%);
	background:linear-gradient(to bottom,  rgba(24,109,226,1) 0%,rgba(0,69,165,1) 100%);
	filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#186de2', endColorstr='#0045a5',GradientType=0 );
	-webkit-box-shadow:0px 0px 12px 0px rgba(24, 109, 226, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
	box-shadow:0px 0px 12px 0px rgba(24, 109, 226, 1),inset 0px 1px 0px 0px rgba(255, 255, 255, 0.45),inset 1px 0px 0px 0px rgba(255, 255, 255, 0.25),inset -1px 0px 0px 0px rgba(255, 255, 255, 0.25);
}
.progressbar .bar:before {
	position:absolute;
	display:block;
	content:"";
	width:606px;
	height:150%;
	top:-25%;
	left:-25px;
	background:-moz-radial-gradient(center, ellipse cover,  rgba(255,255,255,0.35) 0%, rgba(255,255,255,0.01) 50%, rgba(255,255,255,0) 51%, rgba(255,255,255,0) 100%);
	background:-webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,rgba(255,255,255,0.35)), color-stop(50%,rgba(255,255,255,0.01)), color-stop(51%,rgba(255,255,255,0)), color-stop(100%,rgba(255,255,255,0)));
	background:-webkit-radial-gradient(center, ellipse cover,  rgba(255,255,255,0.35) 0%,rgba(255,255,255,0.01) 50%,rgba(255,255,255,0) 51%,rgba(255,255,255,0) 100%);
	background:-o-radial-gradient(center, ellipse cover,  rgba(255,255,255,0.35) 0%,rgba(255,255,255,0.01) 50%,rgba(255,255,255,0) 51%,rgba(255,255,255,0) 100%);
	background:-ms-radial-gradient(center, ellipse cover,  rgba(255,255,255,0.35) 0%,rgba(255,255,255,0.01) 50%,rgba(255,255,255,0) 51%,rgba(255,255,255,0) 100%);
	background:radial-gradient(ellipse at center,  rgba(255,255,255,0.35) 0%,rgba(255,255,255,0.01) 50%,rgba(255,255,255,0) 51%,rgba(255,255,255,0) 100%);
	filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#59ffffff', endColorstr='#00ffffff',GradientType=1 );
}
.progressbar .bar:after {
	position:absolute;
	display:block;
	content:"";
	width:64px;
	height:16px;
	right:0;
	top:0;
	-webkit-border-radius:0px 16px 16px 0px;
	border-radius:0px 16px 16px 0px;
	background:-moz-linear-gradient(left,  rgba(255,255,255,0) 0%, rgba(255,255,255,0.6) 98%, rgba(255,255,255,0) 100%);
	background:-webkit-gradient(linear, left top, right top, color-stop(0%,rgba(255,255,255,0)), color-stop(98%,rgba(255,255,255,0.6)), color-stop(100%,rgba(255,255,255,0)));
	background:-webkit-linear-gradient(left,  rgba(255,255,255,0) 0%,rgba(255,255,255,0.6) 98%,rgba(255,255,255,0) 100%);
	background:-o-linear-gradient(left,  rgba(255,255,255,0) 0%,rgba(255,255,255,0.6) 98%,rgba(255,255,255,0) 100%);
	background:-ms-linear-gradient(left,  rgba(255,255,255,0) 0%,rgba(255,255,255,0.6) 98%,rgba(255,255,255,0) 100%);
	background:linear-gradient(to right,  rgba(255,255,255,0) 0%,rgba(255,255,255,0.6) 98%,rgba(255,255,255,0) 100%);
	filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#00ffffff', endColorstr='#00ffffff',GradientType=1 );
}
.progressbar .bar span {
	position:absolute;
	display:block;
	width:100%;
	height:64px;
	-webkit-border-radius:16px;
	border-radius:16px;
	top:0;
	left:0;
	background:url("data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAG4AAABACAYAAAD7/UK9AAAAGXRFWHRTb2Z0d2FyZQBBZG9iZSBJbWFnZVJlYWR5ccllPAAAAyJpVFh0WE1MOmNvbS5hZG9iZS54bXAAAAAAADw/eHBhY2tldCBiZWdpbj0i77u/IiBpZD0iVzVNME1wQ2VoaUh6cmVTek5UY3prYzlkIj8+IDx4OnhtcG1ldGEgeG1sbnM6eD0iYWRvYmU6bnM6bWV0YS8iIHg6eG1wdGs9IkFkb2JlIFhNUCBDb3JlIDUuMC1jMDYxIDY0LjE0MDk0OSwgMjAxMC8xMi8wNy0xMDo1NzowMSAgICAgICAgIj4gPHJkZjpSREYgeG1sbnM6cmRmPSJodHRwOi8vd3d3LnczLm9yZy8xOTk5LzAyLzIyLXJkZi1zeW50YXgtbnMjIj4gPHJkZjpEZXNjcmlwdGlvbiByZGY6YWJvdXQ9IiIgeG1sbnM6eG1wPSJodHRwOi8vbnMuYWRvYmUuY29tL3hhcC8xLjAvIiB4bWxuczp4bXBNTT0iaHR0cDovL25zLmFkb2JlLmNvbS94YXAvMS4wL21tLyIgeG1sbnM6c3RSZWY9Imh0dHA6Ly9ucy5hZG9iZS5jb20veGFwLzEuMC9zVHlwZS9SZXNvdXJjZVJlZiMiIHhtcDpDcmVhdG9yVG9vbD0iQWRvYmUgUGhvdG9zaG9wIENTNS4xIFdpbmRvd3MiIHhtcE1NOkluc3RhbmNlSUQ9InhtcC5paWQ6MjdFQ0M2MzdDQThBMTFFMUE3NzJFNzY4M0ZDMTA3MTIiIHhtcE1NOkRvY3VtZW50SUQ9InhtcC5kaWQ6MjdFQ0M2MzhDQThBMTFFMUE3NzJFNzY4M0ZDMTA3MTIiPiA8eG1wTU06RGVyaXZlZEZyb20gc3RSZWY6aW5zdGFuY2VJRD0ieG1wLmlpZDoyN0VDQzYzNUNBOEExMUUxQTc3MkU3NjgzRkMxMDcxMiIgc3RSZWY6ZG9jdW1lbnRJRD0ieG1wLmRpZDoyN0VDQzYzNkNBOEExMUUxQTc3MkU3NjgzRkMxMDcxMiIvPiA8L3JkZjpEZXNjcmlwdGlvbj4gPC9yZGY6UkRGPiA8L3g6eG1wbWV0YT4gPD94cGFja2V0IGVuZD0iciI/PoTG0pMAABr+SURBVHjavJ1nj1zX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") 0 0;
	-webkit-animation:sparkle 1500ms linear infinite;
    -moz-animation:sparkle 1500ms linear infinite;
    -o-animation:sparkle 1500ms linear infinite;
    animation:sparkle 1500ms linear infinite;
	opacity:0.2;
}
.progressbar .label {
	font-family:'Aldrich', sans-serif;
	position:relative;
	display:block;
	width:30%;
	height:30px;
	line-height:30px;
	bottom:40px;
	left:0px;
	background:rgb(255, 255, 255);
	/* background:-moz-linear-gradient(top,  rgba(76,76,76,1) 0%, rgba(38,38,38,1) 100%);
	background:-webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(76,76,76,1)), color-stop(100%,rgba(38,38,38,1)));
	background:-webkit-linear-gradient(top,  rgba(76,76,76,1) 0%,rgba(38,38,38,1) 100%);
	background:-o-linear-gradient(top,  rgba(76,76,76,1) 0%,rgba(38,38,38,1) 100%);
	background:-ms-linear-gradient(top,  rgba(76,76,76,1) 0%,rgba(38,38,38,1) 100%);
	background:linear-gradient(to bottom,  rgba(76,76,76,1) 0%,rgba(38,38,38,1) 100%); */
	filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#4c4c4c', endColorstr='#262626',GradientType=0 );
	font-weight:bold;
	font-size:12px;
	color:#252525;
    filter:dropshadow(color=#000000, offx=0, offy=-1);
}
.progressbar .label span {
	position:absolute;
	display:block;
	width:12px;
	height:9px;
	top:-9px;
	left:14px;
	background:transparent;
	overflow:hidden;
}
.progressbar .label span:before {
	position:absolute;
	display:block;
	content:"";
	width:8px;
	height:8px;
	top:4px;
	left:2px;
	border:1px solid rgba(0,0,0,0.5);
	background:rgb(86,86,86);
	background:-moz-linear-gradient(-45deg,  rgba(86,86,86,1) 0%, rgba(76,76,76,1) 50%);
	background:-webkit-gradient(linear, left top, right bottom, color-stop(0%,rgba(86,86,86,1)), color-stop(50%,rgba(76,76,76,1)));
	background:-webkit-linear-gradient(-45deg,  rgba(86,86,86,1) 0%,rgba(76,76,76,1) 50%);
	background:-o-linear-gradient(-45deg,  rgba(86,86,86,1) 0%,rgba(76,76,76,1) 50%);
	background:-ms-linear-gradient(-45deg,  rgba(86,86,86,1) 0%,rgba(76,76,76,1) 50%);
	background:linear-gradient(135deg,  rgba(86,86,86,1) 0%,rgba(76,76,76,1) 50%);
	filter:progid:DXImageTransform.Microsoft.gradient( startColorstr='#565656', endColorstr='#4c4c4c',GradientType=1 );
	-webkit-box-shadow:0px -1px 2px 0px rgba(0, 0, 0, 0.15);
	box-shadow:0px -1px 2px 0px rgba(0, 0, 0, 0.15);
	-moz-transform:rotate(45deg);
	-webkit-transform:rotate(45deg);
	-o-transform:rotate(45deg);
	-ms-transform:rotate(45deg);
	transform:rotate(45deg);
}

.progressbar .label.la-top span:before {
	-moz-transform:rotate(45deg);
	-webkit-transform:rotate(45deg);
	-o-transform:rotate(45deg);
	-ms-transform:rotate(45deg);
	transform:rotate(45deg);
}
	</style>

</head>
<body class="page-orange">
	<header class="header header-orange header-transparent header-waterfall ui-header">
		<ul class="nav nav-list pull-left">
			<div>
				<a data-toggle="menu" href="#ui_menu">
					<span class="icon icon-lg text-white">format_align_justify</span>
				</a>
			</div>
		</ul>

		<ul class="nav nav-list pull-right">
			<div class="dropdown margin-right">
				<a class="dropdown-toggle padding-left-no padding-right-no" data-toggle="dropdown">
				{if $user->isLogin}
					<span class="access-hide">{$user->user_name}</span>
              	    <span class="icon icon-cd margin-right">account_circle</span>
					</a>
					<ul class="dropdown-menu dropdown-menu-right">
						<li>
							<a class="padding-right-lg waves-attach" href="/user/"><span class="icon icon-lg margin-right">account_box</span>用户中心</a>
						</li>

						<li>
							<a class="padding-right-cd waves-attach" href="/user/logout"><span class="icon icon-lg margin-right">exit_to_app</span>登出</a>
						</li>
					</ul>
				{else}
					<span class="access-hide">未登录</span>
             		 <span class="icon icon-lg margin-right">account_circle</span>
					<ul class="dropdown-menu dropdown-menu-right">
						<li>
							<a class="padding-right-lg waves-attach" href="/auth/login"><span class="icon icon-lg margin-right">account_box</span>登录</a>
						</li>
						<li>
							<a class="padding-right-lg waves-attach" href="/auth/register"><span class="icon icon-lg margin-right">pregnant_woman</span>注册</a>
						</li>
					</ul>
				{/if}

			</div>
		</ul>
	</header>
	<nav aria-hidden="true" class="menu menu-left nav-drawer nav-drawer-md" id="ui_menu" tabindex="-1">
		<div class="menu-scroll">
			<div class="menu-content">
				<a class="menu-logo" href="/"><i class="icon icon-lg" >language</i>&nbsp;{$config["appName"]}</a>
				<ul class="nav">
					<li>
						<a class="waves-attach" data-toggle="collapse" href="#ui_menu_me">我的</a>
						<ul class="menu-collapse collapse in" id="ui_menu_me">
							<li>
								<a href="/user">
									<i class="icon icon-lg">account_balance_wallet</i>&nbsp;用户中心
								</a>
							</li>

							<li>
								<a href="/user/profile">
									<i class="icon icon-lg">account_box</i>&nbsp;账户信息
								</a>
							</li>

							<li>
								<a href="/user/edit">
									<i class="icon icon-lg">sync_problem</i>&nbsp;资料编辑
								</a>
							</li>

							{if $config['enable_ticket']=='true'}
                            <li>
								<a href="/user/ticket">
									<i class="icon icon-lg">question_answer</i>&nbsp;工单系统
								</a>
							</li>
							{/if}

                            <li>
								<a href="/user/invite">
									<i class="icon icon-lg">loyalty</i>&nbsp;邀请链接
								</a>
							</li>
							
						</ul>


						<a class="waves-attach" data-toggle="collapse" href="#ui_menu_use">使用</a>
						<ul class="menu-collapse collapse in" id="ui_menu_use">
							<li>
								<a href="/user/node">
									<i class="icon icon-lg">airplanemode_active</i>&nbsp;节点列表
								</a>
							</li>

							<li>
								<a href="/user/relay">
									<i class="icon icon-lg">compare_arrows</i>&nbsp;中转规则
								</a>
							</li>

							<li>
								<a href="/user/trafficlog">
									<i class="icon icon-lg">hourglass_empty</i>&nbsp;流量记录
								</a>
							</li>

							<li>
								<a href="/user/lookingglass">
									<i class="icon icon-lg">visibility</i>&nbsp;延迟检测
								</a>
								<a href="/user/announcement">
									<i class="icon icon-lg">start</i>&nbsp;使用教程
								</a>
							</li>
						</ul>

						<a class="waves-attach" data-toggle="collapse" href="#ui_menu_detect">审计</a>
						<ul class="menu-collapse collapse in" id="ui_menu_detect">
							<li><a href="/user/detect"><i class="icon icon-lg">account_balance</i>&nbsp;审计规则</a></li>
							<li><a href="/user/detect/log"><i class="icon icon-lg">assignment_late</i>&nbsp;审计记录</a></li>
						</ul>

						{if $config['enable_wecenter']=='true'}
						<a class="waves-attach" data-toggle="collapse" href="#ui_menu_trade">帮助</a>
						<ul class="menu-collapse collapse in" id="ui_menu_trade">
							<li>
								<a href="{$config["wecenter_url"]}" target="_blank">
									<i class="icon icon-lg">help</i>&nbsp;问答系统
								</a>
							</li>
						</ul>
						{/if}

						<a class="waves-attach" data-toggle="collapse" href="#ui_menu_help">商店</a>
						<ul class="menu-collapse collapse in" id="ui_menu_help">
                          	<li>
								<a href="/user/code">
									<i class="icon icon-lg">code</i>&nbsp;充值
								</a>
							</li>

							<li>
								<a href="/user/shop">
									<i class="icon icon-lg">shop</i>&nbsp;套餐购买
								</a>
							</li>

							<li><a href="/user/bought"><i class="icon icon-lg">shopping_cart</i>&nbsp;购买记录</a></li>




                          {if $config['enable_donate']=='true'}
							<li>
								<a href="/user/donate">
									<i class="icon icon-lg">attach_money</i>&nbsp;捐赠公示
								</a>
							</li>
							{/if}

						</ul>


						{if $user->isAdmin()}
							<li>
								<a href="/admin">
									<i class="icon icon-lg">person_pin</i>&nbsp;管理面板
								</a>
							</li>
						{/if}
                                          	{if $can_backtoadmin}
                                         	    <li>
                                <a class="padding-right-cd waves-attach" href="/user/backtoadmin"><span class="icon icon-lg margin-right">backtoadmin</span>返回管理员身份</a>
                                                    <li>
                                                {/if}


					</li>
				</ul>
			</div>
		</div>
	</nav>

{if $config["enable_crisp"] == 'true'}{include file='crisp.tpl'}{/if}
