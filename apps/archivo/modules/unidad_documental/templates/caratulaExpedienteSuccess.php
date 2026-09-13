<?php
//*************************************************************************************************************
$path_theme = sfConfig::get('theme_simad');
$base_path = sfConfig::get('base_simad');
//************************************************************************************************************* 
/*header("Pragma: cache");
header("Expires: 0");
header("Cache-control: private");
header('Content-type: application/pdf');
header('Content-disposition: inline');*/
?>

<html>
<head>
<style>
tr
	{mso-height-source:auto;}
col
	{mso-width-source:auto;}
br
	{mso-data-placement:same-cell;}
.style0
	{mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	white-space:nowrap;
	mso-rotate:0;
	mso-background-source:auto;
	mso-pattern:auto;
	color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	border:none;
	mso-protection:locked visible;
	mso-style-name:Normal;
	mso-style-id:0;}
.font0
	{color:black;
	font-size:11.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;}
.font11
	{color:black;
	font-size:11.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;}
.font12
	{color:black;
	font-size:12.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;}
.font13
	{color:windowtext;
	font-size:10.0pt;
	font-weight:400;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;}
.font14
	{color:windowtext;
	font-size:10.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;}
td
	{mso-style-parent:style0;
	padding:0px;
	mso-ignore:padding;
	color:black;
	font-size:11.0pt;
	font-weight:700;
	font-style:normal;
	text-decoration:none;
	font-family:Calibri, sans-serif;
	mso-font-charset:0;
	mso-number-format:General;
	text-align:general;
	vertical-align:bottom;
	border:none;
	mso-background-source:auto;
	mso-pattern:auto;
	mso-protection:locked visible;
	white-space:nowrap;
	mso-rotate:0;}
.xl65
	{mso-style-parent:style0;
	text-align:center;}
.xl66
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid #999999;
	border-bottom:1.0pt solid #999999;
	border-left:none;
	white-space:normal;}
.xl67
	{mso-style-parent:style0;
	white-space:normal;}
.xl68
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	white-space:normal;}
.xl69
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;}
.xl70
	{mso-style-parent:style0;
	font-size:10.0pt;
	text-align:left;
	border:.5pt solid windowtext;
	background:#C5D9F1;
	mso-pattern:black none;
	white-space:normal;}
.xl71
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	border:.5pt solid windowtext;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl72
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;}
.xl73
	{mso-style-parent:style0;
	font-weight:700;}
.xl74
	{mso-style-parent:style0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;}
.xl75
	{mso-style-parent:style0;
	text-align:center;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;}
.xl76
	{mso-style-parent:style0;
	text-align:center;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;}
.xl77
	{mso-style-parent:style0;
	text-align:center;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;}
.xl78
	{mso-style-parent:style0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl79
	{mso-style-parent:style0;
	text-align:center;
	border:.5pt solid windowtext;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl80
	{mso-style-parent:style0;
	border:.5pt solid windowtext;}
.xl81
	{mso-style-parent:style0;
	text-align:center;
	border:.5pt solid windowtext;}
.xl82
	{mso-style-parent:style0;
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	background:#C5D9F1;
	mso-pattern:black none;
	white-space:normal;}
.xl83
	{mso-style-parent:style0;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#C5D9F1;
	mso-pattern:black none;
	white-space:normal;}
.xl84
	{mso-style-parent:style0;
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	white-space:normal;}
.xl85
	{mso-style-parent:style0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;}
.xl86
	{mso-style-parent:style0;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	white-space:normal;}
.xl87
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:left;
	vertical-align:middle;
	border:.5pt solid windowtext;
	white-space:normal;}
.xl88
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	border:.5pt solid windowtext;}
.xl89
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;}
.xl90
	{mso-style-parent:style0;
	font-size:20.0pt;
	font-weight:700;
	text-align:center;}
.xl91
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl92
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;}
.xl93
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl94
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl95
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;}
.xl96
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl97
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;}
.xl98
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl99
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl100
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;
	border:.5pt solid windowtext;
	background:#DCE6F1;
	mso-pattern:black none;}
.xl101
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-weight:700;
	text-align:center;
	border:.5pt solid windowtext;
	background:#DCE6F1;
	mso-pattern:black none;}
.xl102
	{mso-style-parent:style0;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;}
.xl103
	{mso-style-parent:style0;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl104
	{mso-style-parent:style0;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl105
	{mso-style-parent:style0;
	font-size:10.0pt;
	font-weight:700;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:#DCE6F1;
	mso-pattern:black none;}
.xl106
	{mso-style-parent:style0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl107
	{mso-style-parent:style0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl108
	{mso-style-parent:style0;
	font-weight:700;
	text-align:left;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl109
	{mso-style-parent:style0;
	text-align:left;
	vertical-align:top;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;}
.xl110
	{mso-style-parent:style0;
	text-align:left;
	vertical-align:top;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;}
.xl111
	{mso-style-parent:style0;
	text-align:left;
	vertical-align:top;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;}
.xl112
	{mso-style-parent:style0;
	text-align:left;
	vertical-align:top;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;}
.xl113
	{mso-style-parent:style0;
	text-align:left;
	vertical-align:top;}
.xl114
	{mso-style-parent:style0;
	text-align:left;
	vertical-align:top;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;}
.xl115
	{mso-style-parent:style0;
	text-align:left;
	vertical-align:top;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;}
.xl116
	{mso-style-parent:style0;
	text-align:left;
	vertical-align:top;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl117
	{mso-style-parent:style0;
	text-align:left;
	vertical-align:top;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl118
	{mso-style-parent:style0;
	text-align:center;
	vertical-align:middle;}
.xl119
	{mso-style-parent:style0;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl120
	{mso-style-parent:style0;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl121
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	white-space:normal;}
.xl122
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;
	white-space:normal;}
.xl123
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	white-space:normal;}
.xl124
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	white-space:normal;}
.xl125
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;}
.xl126
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;}
.xl127
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;}
.xl128
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl129
	{mso-style-parent:style0;
	color:windowtext;
	font-size:10.0pt;
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl130
	{mso-style-parent:style0;
	color:windowtext;
	font-size:10.0pt;
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl131
	{mso-style-parent:style0;
	color:windowtext;
	font-size:10.0pt;
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl132
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl133
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl134
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	white-space:normal;}
.xl135
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	white-space:normal;}
.xl136
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	white-space:normal;}
.xl137
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	white-space:normal;}
.xl138
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	white-space:normal;}
.xl139
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	white-space:normal;}
.xl140
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:1.0pt solid windowtext;
	white-space:normal;}
.xl141
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	white-space:normal;}
.xl142
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	white-space:normal;}
.xl143
	{mso-style-parent:style0;
	font-size:10.0pt;
	text-align:center;
	vertical-align:middle;
	border:.5pt solid windowtext;
	background:#C5D9F1;
	mso-pattern:black none;
	white-space:normal;}
.xl144
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	white-space:normal;}
.xl145
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	white-space:normal;}
.xl146
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	white-space:normal;}
.xl147
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	white-space:normal;}
.xl148
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	white-space:normal;}
.xl149
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	white-space:normal;}
.xl150
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	white-space:normal;}
.xl151
	{mso-style-parent:style0;
	font-size:12.0pt;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;
	white-space:normal;}
.xl152
	{mso-style-parent:style0;
	text-align:left;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;}
.xl153
	{mso-style-parent:style0;
	text-align:left;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;}
.xl154
	{mso-style-parent:style0;
	font-size:8.0pt;
	font-weight:700;
	text-align:center;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl155
	{mso-style-parent:style0;
	font-size:8.0pt;
	font-weight:700;
	text-align:center;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl156
	{mso-style-parent:style0;
	font-size:8.0pt;
	font-weight:700;
	text-align:center;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl157
	{mso-style-parent:style0;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	white-space:normal;}
.xl158
	{mso-style-parent:style0;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	white-space:normal;}
.xl159
	{mso-style-parent:style0;
	text-align:center;
	vertical-align:middle;
	border-top:.5pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	white-space:normal;}
.xl160
	{mso-style-parent:style0;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:1.0pt solid windowtext;
	white-space:normal;}
.xl161
	{mso-style-parent:style0;
	text-align:center;
	vertical-align:middle;
	white-space:normal;}
.xl162
	{mso-style-parent:style0;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:none;
	border-left:none;
	white-space:normal;}
.xl163
	{mso-style-parent:style0;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	white-space:normal;}
.xl164
	{mso-style-parent:style0;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	white-space:normal;}
.xl165
	{mso-style-parent:style0;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	white-space:normal;}
.xl166
	{mso-style-parent:style0;
	text-align:center;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl167
	{mso-style-parent:style0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	white-space:normal;}
.xl168
	{mso-style-parent:style0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:none;
	border-bottom:none;
	border-left:none;
	white-space:normal;}
.xl169
	{mso-style-parent:style0;
	text-align:center;
	border-top:.5pt solid windowtext;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;
	white-space:normal;}
.xl170
	{mso-style-parent:style0;
	text-align:center;
	border-top:none;
	border-right:none;
	border-bottom:none;
	border-left:.5pt solid windowtext;
	white-space:normal;}
.xl171
	{mso-style-parent:style0;
	text-align:center;
	white-space:normal;}
.xl172
	{mso-style-parent:style0;
	text-align:center;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:none;
	border-left:none;
	white-space:normal;}
.xl173
	{mso-style-parent:style0;
	text-align:center;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:.5pt solid windowtext;
	white-space:normal;}
.xl174
	{mso-style-parent:style0;
	text-align:center;
	border-top:none;
	border-right:none;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	white-space:normal;}
.xl175
	{mso-style-parent:style0;
	text-align:center;
	border-top:none;
	border-right:.5pt solid windowtext;
	border-bottom:.5pt solid windowtext;
	border-left:none;
	white-space:normal;}
.xl176
	{mso-style-parent:style0;
	font-size:8.0pt;
	font-family:Arial, sans-serif;
	mso-font-charset:0;
	text-align:right;
	white-space:normal;}
.xl177
	{mso-style-parent:style0;
	text-align:center;
	border:.5pt solid windowtext;
	white-space:normal;}
.xl178
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl179
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl180
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	border-top:1.0pt solid windowtext;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	background:#C5D9F1;
	mso-pattern:black none;}
.xl181
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:1.0pt solid windowtext;
	white-space:normal;}
.xl182
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:none;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	white-space:normal;}
.xl183
	{mso-style-parent:style0;
	font-weight:700;
	text-align:center;
	vertical-align:middle;
	border-top:none;
	border-right:1.0pt solid windowtext;
	border-bottom:1.0pt solid windowtext;
	border-left:none;
	white-space:normal;}
</style>
</head>
<body link=blue vlink=purple >

<table border=0 cellpadding=0 cellspacing=0 width=993 style='border-collapse:
 collapse;table-layout:fixed;width:745pt'>
 <col class=xl65 width=7 style='mso-width-source:userset;mso-width-alt:256;
 width:5pt'>
 <col class=xl65 width=122 style='mso-width-source:userset;mso-width-alt:4461;
 width:92pt'>
 <col class=xl65 width=55 style='mso-width-source:userset;mso-width-alt:2011;
 width:41pt'>
 <col class=xl65 width=31 style='mso-width-source:userset;mso-width-alt:1133;
 width:23pt'>
 <col class=xl65 width=6 style='mso-width-source:userset;mso-width-alt:219;
 width:5pt'>
 <col class=xl65 width=119 style='mso-width-source:userset;mso-width-alt:4352;
 width:89pt'>
 <col class=xl65 width=51 style='mso-width-source:userset;mso-width-alt:1865;
 width:38pt'>
 <col class=xl65 width=31 style='mso-width-source:userset;mso-width-alt:1133;
 width:23pt'>
 <col class=xl65 width=5 style='mso-width-source:userset;mso-width-alt:182;
 width:4pt'>
 <col class=xl65 width=109 style='mso-width-source:userset;mso-width-alt:3986;
 width:82pt'>
 <col class=xl65 width=101 style='mso-width-source:userset;mso-width-alt:3693;
 width:76pt'>
 <col class=xl65 width=90 style='mso-width-source:userset;mso-width-alt:3291;
 width:68pt'>
 <col class=xl65 width=95 style='mso-width-source:userset;mso-width-alt:3474;
 width:71pt'>
 <col class=xl65 width=11 style='mso-width-source:userset;mso-width-alt:402;
 width:8pt'>
 <col class=xl65 width=80 span=2 style='width:60pt'>
 <tr height=13 style='mso-height-source:userset;height:10.15pt'>
  <td height=13 class=xl65 width=7 style='height:10.15pt;width:5pt'></td>
  <td class=xl65 width=122 style='width:92pt'></td>
  <td class=xl65 width=55 style='width:41pt'></td>
  <td class=xl65 width=31 style='width:23pt'></td>
  <td class=xl65 width=6 style='width:5pt'></td>
  <td class=xl65 width=119 style='width:89pt'></td>
  <td class=xl65 width=51 style='width:38pt'></td>
  <td class=xl65 width=31 style='width:23pt'></td>
  <td class=xl65 width=5 style='width:4pt'></td>
  <td class=xl65 width=109 style='width:82pt'></td>
  <td class=xl65 width=101 style='width:76pt'></td>
  <td class=xl65 width=90 style='width:68pt'></td>
  <td class=xl65 width=95 style='width:71pt'></td>
  <td class=xl65 width=11 style='width:8pt'></td>
  <td class=xl65 width=80 style='width:60pt'></td>
  <td class=xl65 width=80 style='width:60pt'></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl65 style='height:15.0pt'><a name="Print_Area"></a></td>
  <td align=left valign=top><span style='mso-ignore:vglayout;
  position:absolute;z-index:9;margin-left:3px;margin-top:5px;width:229px;
  height:83px'><img width=229 height=83 src="<?php echo $base_path.'/images/lincenciadoa.jpg';?>"/></span><span
  style='mso-ignore:vglayout2'>
  <table cellpadding=0 cellspacing=0>
   <tr>
    <td height=20 class=xl65 width=122 style='height:15.0pt;width:92pt'></td>
   </tr>
  </table>
  </span></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=35 style='height:26.25pt'>
  <td height=35 class=xl65 style='height:26.25pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td colspan=8 class="xl90 font14">PORTADA DE EXPEDIENTE</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl65 style='height:15.0pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl65 style='height:15.0pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl65 style='height:15.0pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td colspan=3 class=xl100>CLASIFICACIÓN</td>
  <td></td>
  <td colspan=4 class=xl102 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=23 style='mso-height-source:userset;height:17.25pt'>
  <td height=23 class=xl65 style='height:17.25pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td colspan=3 class=xl100>CLASIFICACIÓN LOCAL</td>
  <td></td>
  <td colspan=4 class=xl102 style='border-right:.5pt solid black'>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=23 style='mso-height-source:userset;height:17.25pt'>
  <td height=23 class=xl65 style='height:17.25pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td colspan=3 class="xl105 font14" style='border-right:.5pt solid black'>ID</td>
  <td></td>
  <td colspan=4 class="xl102 font12" style='border-right:.5pt solid black'><?php echo $unidad_documental->getCodigoBarras(); ?></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=21 style='height:15.75pt'>
  <td height=21 class=xl65 style='height:15.75pt'></td>
  <td colspan=12 class="xl91 font14">CLASIFICACION ARCHIVISTICA</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=6 style='mso-height-source:userset;height:4.5pt'>
  <td height=6 class=xl65 style='height:4.5pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=22 style='height:16.5pt'>
  <td height=22 class=xl65 style='height:16.5pt'></td>
  <td class=xl71>FONDO</td>
  <td colspan=9 class="xl92 font12" style='border-right:.5pt solid black;border-left:none'>SECRETARÍA DE CULTURA<span style='mso-spacerun:yes'> </span></td>
  <td colspan=2 class=xl95 style='border-right:.5pt solid black;border-left:none'>SC</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=4 style='mso-height-source:userset;height:3.0pt'>
  <td height=4 class=xl65 style='height:3.0pt'></td>
  <td class=xl72></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=21 style='height:15.75pt'>
  <td height=21 class=xl65 style='height:15.75pt'></td>
  <td class="xl71 font12">SUBFONDO</td>
  <td colspan=9 class="xl97 font12" style='border-right:.5pt solid black;border-left:none'><?php echo $unidad_documental->getSubserie()->getSerie()->getDependencia()->getNombre(); ?></td>
  <td colspan=2 class="xl95 font12" style='border-right:.5pt solid black;border-left:none'><?php echo $unidad_documental->getSubserie()->getSerie()->getDependencia()->getCodigo(); ?></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=5 style='mso-height-source:userset;height:3.75pt'>
  <td height=5 class=xl65 style='height:3.75pt'></td>
  <td class=xl72></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=21 style='height:15.75pt'>
  <td height=21 class=xl65 style='height:15.75pt'></td>
  <td class="xl71 font12">SECCION</td>
  <td colspan=9 class=xl97 style='border-right:.5pt solid black;border-left:none'>&nbsp;</td>
  <td colspan=2 class=xl95 style='border-right:.5pt solid black;border-left:none'>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=5 style='mso-height-source:userset;height:3.75pt'>
  <td height=5 class=xl65 style='height:3.75pt'></td>
  <td class=xl72></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=21 style='height:15.75pt'>
  <td height=21 class=xl65 style='height:15.75pt'></td>
  <td class="xl71 font12">SERIE</td>
  <td colspan=9 class="xl97 font12" style='border-right:.5pt solid black;border-left:none'><?php echo $unidad_documental->getSubserie()->getSerie()->getDescripcion(); ?></td>
  <td colspan=2 class="xl95 font12" style='border-right:.5pt solid black;border-left:none'><?php echo $unidad_documental->getSubserie()->getSerie()->getCodigo(); ?></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=5 style='mso-height-source:userset;height:3.75pt'>
  <td height=5 class=xl65 style='height:3.75pt'></td>
  <td class=xl72></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl69></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=21 style='height:15.75pt'>
  <td height=21 class=xl65 style='height:15.75pt'></td>
  <td class="xl71 font12">SUB-SERIE</td>
  <td colspan=9 class="xl97 font12" style='border-right:.5pt solid black;border-left:none'><?php echo $unidad_documental->getSubserie()->getDescripcion(); ?></td>
  <td colspan=2 class="xl95 font12" style='border-right:.5pt solid black;border-left:none'><?php echo $unidad_documental->getSubserie()->getCodigo(); ?></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=5 style='mso-height-source:userset;height:3.75pt'>
  <td height=5 class=xl65 style='height:3.75pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=22 style='height:16.5pt'>
  <td height=22 class=xl65 style='height:16.5pt'></td>
  <td colspan=12 class="xl108 font12">ASUNTO<span style='mso-spacerun:yes'>                                                                           
  </span><font class="font12"><span style='mso-spacerun:yes'>&nbsp;</span></font></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=5 style='mso-height-source:userset;height:3.75pt'>
  <td height=5 class=xl65 style='height:3.75pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl65 style='height:15.0pt'></td>
  <td colspan=12 rowspan=5 class="xl109 font12" style='border-right:.5pt solid black;border-bottom:.5pt solid black'><?php echo $unidad_documental->getTitulo(); ?></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl65 style='height:15.0pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl65 style='height:15.0pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl65 style='height:15.0pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl65 style='height:15.0pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=5 style='mso-height-source:userset;height:3.75pt'>
  <td height=5 class=xl65 style='height:3.75pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=21 style='height:15.75pt'>
  <td height=21 class=xl65 style='height:15.75pt'></td>
  <td colspan=7 class="xl91 font12">FECHAS</td>
  <td class=xl73></td>
  <td colspan=4 class="xl91 font12">CLASIFICACION</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=6 style='mso-height-source:userset;height:4.5pt'>
  <td height=6 class=xl65 style='height:4.5pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl65 style='height:15.0pt'></td>
  <td rowspan=2 class="xl119 font12" style='border-bottom:.5pt solid black'>APERTURA</td>
  <td colspan=2 rowspan=2 class="xl121 font12" width=86 style='border-right:.5pt solid black;border-bottom:.5pt solid black;width:64pt'><?php echo $unidad_documental->getFechaApertura("Y-m-d"); ?></td>
  <td class=xl74 style='border-left:none'>&nbsp;</td>
  <td rowspan=2 class=xl119 style='border-bottom:.5pt solid black'>CIERRE</td>
  <td colspan=2 rowspan=2 class="xl125 font12" style='border-right:.5pt solid black;border-bottom:.5pt solid black'><?php echo $unidad_documental->getFechaCierre("Y-m-d"); ?></td>
  <td class=xl65></td>
  <td colspan=4 rowspan=2 class=xl118></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl65 style='height:15.0pt'></td>
  <td class=xl75 style='border-left:none'>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=4 style='mso-height-source:userset;height:3.0pt'>
  <td height=4 class=xl65 style='height:3.0pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=21 style='height:15.75pt'>
  <td height=21 class=xl65 style='height:15.75pt'></td>
  <td colspan=7 class=xl91>VIGENCIAS DOCUMENTALES<span
  style='mso-spacerun:yes'>      </span><font class="font0"><span
  style='mso-spacerun:yes'> </span></font></td>
  <td class=xl65></td>
  <td colspan=4 class=xl129 style='border-right:1.0pt solid black'>Llenar en
  caso de Clasificación <font class="font14">RESERVADA</font><font
  class="font13"> o </font><font class="font14">CONFIDENCIAL</font></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=4 style='mso-height-source:userset;height:3.0pt'>
  <td height=4 class=xl65 style='height:3.0pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl76>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl77>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=21 style='mso-height-source:userset;height:15.75pt'>
  <td height=21 class=xl65 style='height:15.75pt'></td>
  <td rowspan=3 class=xl82 width=122 style='width:92pt'>TRAMITE<span style='mso-spacerun:yes'></span>(AÑOS)</td>
  <td colspan=2 rowspan=3 class=xl81><?php echo $unidad_documental->getSubserie()->getAnosEnGestion(); ?></td>
  <td class=xl65></td>
  <td rowspan=3 class=xl82 width=119 style='width:89pt'>CONCENTRACION (AÑOS)</td>
  <td colspan=2 rowspan=3 class=xl81><?php echo $unidad_documental->getSubserie()->getAnosEnCentral(); ?></td>
  <td class=xl65></td>
  <td class=xl78>FECHA</td>
  <td class=xl88 style='border-left:none'>&nbsp;</td>
  <td class=xl79 style='border-left:none'>PERIODO</td>
  <td class=xl89 style='border-left:none'>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=5 style='mso-height-source:userset;height:3.75pt'>
  <td height=5 class=xl65 style='height:3.75pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl76>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl77>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=21 style='height:15.75pt'>
  <td height=21 class=xl65 style='height:15.75pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td colspan=4 class=xl132 style='border-right:1.0pt solid black'>FUNDAMENTO
  LEGAL<span style='mso-spacerun:yes'> </span></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=5 style='mso-height-source:userset;height:3.75pt'>
  <td height=5 class=xl65 style='height:3.75pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl76>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl77>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=25 style='mso-height-source:userset;height:18.75pt'>
  <td height=25 class=xl65 style='height:18.75pt'></td>
  <td rowspan=3 class=xl143 width=122 style='width:92pt'>CONSULTA INAI FECHA</td>
  <td colspan=2 rowspan=3 class=xl81>&nbsp;</td>
  <td class=xl65></td>
  <td rowspan=3 class=xl143 width=119 style='width:89pt'>AMPLIACION VIGENCIA
  POR CONSULTA INAI</td>
  <td colspan=2 rowspan=3 class=xl121 width=82 style='border-right:.5pt solid black;
  border-bottom:.5pt solid black;width:61pt'>&nbsp;</td>
  <td class=xl65></td>
  <td colspan=4 rowspan=3 class=xl134 width=395 style='border-right:1.0pt solid black;
  border-bottom:.5pt solid black;width:297pt'>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=25 style='mso-height-source:userset;height:18.75pt'>
  <td height=25 class=xl65 style='height:18.75pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=6 style='mso-height-source:userset;height:4.5pt'>
  <td height=6 class=xl65 style='height:4.5pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=5 style='mso-height-source:userset;height:3.75pt'>
  <td height=5 class=xl65 style='height:3.75pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl76>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl77>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=22 style='mso-height-source:userset;height:16.5pt'>
  <td height=22 class=xl65 style='height:16.5pt'></td>
  <td colspan=7 class=xl91>VALORES DOCUMENTALES<span
  style='mso-spacerun:yes'>      </span></td>
  <td class=xl65></td>
  <td colspan=4 class=xl132 style='border-right:1.0pt solid black'>PARTES O
  SECCIONES EN EL DOCUMENTO<span style='mso-spacerun:yes'>  </span></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=6 style='mso-height-source:userset;height:4.5pt'>
  <td height=6 class=xl65 style='height:4.5pt'></td>
  <td class=xl65>7</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl76>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl77>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=21 style='height:15.75pt'>
  <td height=21 class=xl65 style='height:15.75pt'></td>
  <td colspan=3 class=xl166>PRIMARIO</td>
  <td class=xl65></td>
  <td colspan=3 class=xl166>SECUNDARIO</td>
  <td class=xl65></td>
  <td colspan=4 rowspan=3 class=xl144 width=395 style='border-right:1.0pt solid black;
  width:297pt'>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=4 style='mso-height-source:userset;height:3.0pt'>
  <td height=4 class=xl65 style='height:3.0pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=24 style='mso-height-source:userset;height:18.0pt'>
  <td height=24 class=xl65 style='height:18.0pt'></td>
  <td colspan=2 class=xl152 style='border-right:.5pt solid black'>ADMINISTRATIVO</td>
  <td height=24 class=xl80 width=31 style='height:18.0pt;border-left:none;
  width:23pt'><span style='mso-ignore:vglayout'>
  <table cellpadding=0 cellspacing=0>
   <tr>
    <td width=9 height=7></td>
   </tr>
   <tr>
    <td></td>
    <td></td>
    <td width=10></td>
   </tr>
   <tr>
    <td height=5></td>
   </tr>
  </table>
  </span></td>
  <td class=xl65></td>
  <td colspan=2 class=xl152 style='border-right:.5pt solid black'>EVIDENCIAL</td>
  <td height=24 class=xl81 width=31 style='height:18.0pt;border-left:none;width:23pt'><span style='mso-ignore:vglayout'>
  <table cellpadding=0 cellspacing=0>
   <tr>
    <td width=9 height=6></td>
   </tr>
   <tr>
    <td></td>
    <td></td>
    <td width=10></td>
   </tr>
   <tr>
    <td height=6></td>
   </tr>
  </table>
  </span></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=6 style='mso-height-source:userset;height:4.5pt'>
  <td height=6 class=xl65 style='height:4.5pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl76>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl77>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=22 style='mso-height-source:userset;height:16.5pt'>
  <td height=22 class=xl65 style='height:16.5pt'></td>
  <td colspan=2 class=xl152 style='border-right:.5pt solid black'>LEGAL</td>
  <td height=22 class=xl80 width=31 style='height:16.5pt;border-left:none;width:23pt'><span style='mso-ignore:vglayout'>
  <table cellpadding=0 cellspacing=0>
   <tr>
    <td width=9 height=5></td>
   </tr>
   <tr>
    <td></td>
    <td></td>
    <td width=10></td>
   </tr>
   <tr>
    <td height=5></td>
   </tr>
  </table>
  </span></td>
  <td class=xl65></td>
  <td colspan=2 class=xl152 style='border-right:.5pt solid black'>TESTIMONIAL</td>
  <td height=22 class=xl81 width=31 style='height:16.5pt;border-left:none;width:23pt'><span style='mso-ignore:vglayout'>
  <table cellpadding=0 cellspacing=0>
   <tr>
    <td width=9 height=4></td>
   </tr>
   <tr>
    <td></td>
    <td></td>
    <td width=10></td>
   </tr>
   <tr>
    <td height=6></td>
   </tr>
  </table>
  </span></td>
  <td class=xl65></td>
  <td colspan=4 class=xl132 style='border-right:1.0pt solid black'>TITULAR DE
  LA UNIDAD ADMINISTRATIVA</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=6 style='mso-height-source:userset;height:4.5pt'>
  <td height=6 class=xl65 style='height:4.5pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl76>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl77>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl65 style='height:15.0pt'></td>
  <td colspan=2 class=xl152 style='border-right:.5pt solid black'>CONTABLE</td>
  <td height=20 class=xl80 width=31 style='height:15.0pt;border-left:none;width:23pt'><span style='mso-ignore:vglayout'>
  <table cellpadding=0 cellspacing=0>
   <tr>
    <td width=9 height=5></td>
   </tr>
   <tr>
    <td></td>
    <td></td>
    <td width=10></td>
   </tr>
   <tr>
    <td height=3></td>
   </tr>
  </table>
  </span></td>
  <td class=xl65></td>
  <td colspan=2 class=xl152 style='border-right:.5pt solid black'>INFORMATIVO</td>
  <td height=20 class=xl81 width=31 style='height:15.0pt;border-left:none;width:23pt'><span style='mso-ignore:vglayout'>
  <table cellpadding=0 cellspacing=0>
   <tr>
    <td width=9 height=4></td>
   </tr>
   <tr>
    <td></td>
    <td></td>
    <td width=10></td>
   </tr>
   <tr>
    <td height=4></td>
   </tr>
  </table>
  </span></td>
  <td class=xl65></td>
  <td colspan=4 rowspan=3 class=xl157 width=395 style='border-right:1.0pt solid black;
  border-bottom:1.0pt solid black;width:297pt'>RUBRICA<span
  style='mso-spacerun:yes'>                      </span><font class="font12"><span
  style='mso-spacerun:yes'>  </span></font></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=6 style='mso-height-source:userset;height:4.5pt'>
  <td height=6 class=xl65 style='height:4.5pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=22 style='mso-height-source:userset;height:16.5pt'>
  <td height=22 class=xl65 style='height:16.5pt'></td>
  <td colspan=2 class=xl152 style='border-right:.5pt solid black'>FISCAL</td>
  <td height=22 class=xl80 width=31 style='height:16.5pt;border-left:none;width:23pt'><span style='mso-ignore:vglayout'>
  <table cellpadding=0 cellspacing=0>
   <tr>
    <td width=9 height=5></td>
   </tr>
   <tr>
    <td></td>
    <td></td>
    <td width=10></td>
   </tr>
   <tr>
    <td height=5></td>
   </tr>
  </table>
  </span></td>
  <td class=xl65></td>
  <td colspan=2 class=xl152 style='border-right:.5pt solid black'>OTRO</td>
  <td height=22 class=xl81 width=31 style='height:16.5pt;border-left:none;width:23pt'><span style='mso-ignore:vglayout'>
  <table cellpadding=0 cellspacing=0>
   <tr>
    <td width=9 height=4></td>
   </tr>
   <tr>
    <td></td>
    <td></td>
    <td width=10></td>
   </tr>
   <tr>
    <td height=5></td>
   </tr>
  </table>
  </span></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=7 style='mso-height-source:userset;height:5.25pt'>
  <td height=7 class=xl65 style='height:5.25pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=21 style='height:15.75pt'>
  <td height=21 class=xl65 style='height:15.75pt'></td>
  <td colspan=7 class=xl91>IDENTIFICACION ARCHIVISTICA<span
  style='mso-spacerun:yes'>    </span></td>
  <td class=xl65></td>
  <td colspan=4 class=xl178 style='border-right:1.0pt solid black'>DESCLASIFICACION</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=7 style='mso-height-source:userset;height:5.25pt'>
  <td height=7 class=xl65 style='height:5.25pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl76>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl77>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=35 style='mso-height-source:userset;height:26.25pt'>
  <td height=35 class=xl65 style='height:26.25pt'></td>
  <td class=xl82 width=122 style='width:92pt'>FOJAS</td>
  <td colspan=2 class=xl177 width=86 style='border-left:none;width:64pt'>&nbsp;</td>
  <td class=xl67 width=6 style='width:5pt'></td>
  <td class=xl82 width=119 style='width:89pt'>LEGAJO</td>
  <td colspan=2 class=xl177 width=82 style='border-left:none;width:61pt'>&nbsp;</td>
  <td class=xl67 width=5 style='width:4pt'></td>
  <td class=xl83 width=109 style='width:82pt'>FECHA</td>
  <td class=xl84 width=101 style='border-left:none;width:76pt'>&nbsp;</td>
  <td class=xl70 width=90 style='border-left:none;width:68pt'>NUEVA
  CLASIFICACION</td>
  <td class=xl85 style='border-left:none'>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=4 style='mso-height-source:userset;height:3.0pt'>
  <td height=4 class=xl65 style='height:3.0pt'></td>
  <td class=xl67 width=122 style='width:92pt'></td>
  <td class=xl67 width=55 style='width:41pt'></td>
  <td class=xl67 width=31 style='width:23pt'></td>
  <td class=xl67 width=6 style='width:5pt'></td>
  <td class=xl67 width=119 style='width:89pt'></td>
  <td class=xl67 width=51 style='width:38pt'></td>
  <td class=xl67 width=31 style='width:23pt'></td>
  <td class=xl67 width=5 style='width:4pt'></td>
  <td class=xl86 width=109 style='width:82pt'>&nbsp;</td>
  <td class=xl67 width=101 style='width:76pt'></td>
  <td class=xl67 width=90 style='width:68pt'></td>
  <td class=xl77>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=21 style='height:15.75pt'>
  <td height=21 class=xl65 style='height:15.75pt'></td>
  <td rowspan=3 class=xl82 width=122 style='width:92pt'>CAJA</td>
  <td colspan=2 rowspan=3 class=xl177 width=86 style='width:64pt'>&nbsp;</td>
  <td class=xl67 width=6 style='width:5pt'></td>
  <td rowspan=3 class=xl82 width=119 style='width:89pt'>LOCALIZACION</td>
  <td colspan=2 rowspan=3 class=xl177 width=82 style='width:61pt'>&nbsp;</td>
  <td class=xl67 width=5 style='width:4pt'></td>
  <td colspan=4 class=xl154 style='border-right:1.0pt solid black'>NOMBRE Y
  FIRMA DEL TITULAR DE LA UNIDAD DE ENLACE</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=5 style='mso-height-source:userset;height:3.75pt'>
  <td height=5 class=xl65 style='height:3.75pt'></td>
  <td class=xl67 width=6 style='width:5pt'></td>
  <td class=xl67 width=5 style='width:4pt'></td>
  <td class=xl76>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl77>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=12 style='mso-height-source:userset;height:9.0pt'>
  <td height=12 class=xl65 style='height:9.0pt'></td>
  <td class=xl67 width=6 style='width:5pt'></td>
  <td class=xl67 width=5 style='width:4pt'></td>
  <td colspan=4 rowspan=3 class=xl144 width=395 style='border-right:1.0pt solid black;
  border-bottom:1.0pt solid black;width:297pt'><font class="font0">RUBRICA<span
  style='mso-spacerun:yes'>      </span></font><font class="font11"><span
  style='mso-spacerun:yes'>    </span></font><font class="font12"><span
  style='mso-spacerun:yes'> </span></font></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=6 style='mso-height-source:userset;height:4.5pt'>
  <td height=6 class=xl65 style='height:4.5pt'></td>
  <td class=xl67 width=122 style='width:92pt'></td>
  <td class=xl67 width=55 style='width:41pt'></td>
  <td class=xl67 width=31 style='width:23pt'></td>
  <td class=xl67 width=6 style='width:5pt'></td>
  <td class=xl67 width=119 style='width:89pt'></td>
  <td class=xl67 width=51 style='width:38pt'></td>
  <td class=xl67 width=31 style='width:23pt'></td>
  <td class=xl67 width=5 style='width:4pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=27 style='mso-height-source:userset;height:20.25pt'>
  <td height=27 class=xl65 style='height:20.25pt'></td>
  <td colspan=7 class=xl91>UBICACIÓN FISICA DEL EXPEDIENTE</td>
  <td class=xl67 width=5 style='width:4pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=7 style='mso-height-source:userset;height:5.25pt'>
  <td height=7 class=xl65 style='height:5.25pt'></td>
  <td class=xl67 width=122 style='width:92pt'></td>
  <td class=xl67 width=55 style='width:41pt'></td>
  <td class=xl67 width=31 style='width:23pt'></td>
  <td class=xl67 width=6 style='width:5pt'></td>
  <td class=xl67 width=119 style='width:89pt'></td>
  <td class=xl67 width=51 style='width:38pt'></td>
  <td class=xl67 width=31 style='width:23pt'></td>
  <td class=xl67 width=5 style='width:4pt'></td>
  <td class=xl86 width=109 style='width:82pt'>&nbsp;</td>
  <td class=xl67 width=101 style='width:76pt'></td>
  <td class=xl67 width=90 style='width:68pt'></td>
  <td class=xl77>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=21 style='height:15.75pt'>
  <td height=21 class=xl65 style='height:15.75pt'></td>
  <td colspan=7 rowspan=5 class=xl167 width=415 style='border-right:.5pt solid black;border-bottom:.5pt solid black;width:311pt'>
  <?php 
  switch( $unidad_documental->getLocalizacionunidaddocumentalId())
  {
    case 1:
        echo $unidad_documental->getUbicacionengestion();
        break;
    case 2:
        echo $unidad_documental->getUbicacionencentral();
        break;
    case 3:
        echo $unidad_documental->getUbicacionenhistorico();
        break;
    default:
        echo "&nbsp;";
  }
  ?>
  </td>
  <td class=xl67 width=5 style='width:4pt'></td>
  <td colspan=4 class=xl154 style='border-right:1.0pt solid black'>NOMBRE Y
  PUESTO FUNCIONARIO QUE DESCLASIFICA</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=4 style='mso-height-source:userset;height:3.0pt'>
  <td height=4 class=xl65 style='height:3.0pt'></td>
  <td class=xl67 width=5 style='width:4pt'></td>
  <td class=xl76>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl77>&nbsp;</td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl65 style='height:15.0pt'></td>
  <td class=xl67 width=5 style='width:4pt'></td>
  <td colspan=4 rowspan=3 class=xl157 width=395 style='border-right:1.0pt solid black;border-bottom:1.0pt solid black;width:297pt'>RUBRICA<span
  style='mso-spacerun:yes'>           </span></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=20 style='height:15.0pt'>
  <td height=20 class=xl65 style='height:15.0pt'></td>
  <td class=xl67 width=5 style='width:4pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr height=21 style='height:15.75pt'>
  <td height=21 class=xl65 style='height:15.75pt'></td>
  <td class=xl67 width=5 style='width:4pt'></td>
  <td class=xl65></td>
  <td class=xl65></td>
  <td class=xl65></td>
 </tr>
 <tr class=xl67 height=4 style='mso-height-source:userset;height:3.0pt'>
  <td height=4 class=xl67 width=7 style='height:3.0pt;width:5pt'></td>
  <td class=xl68 width=122 style='width:92pt'></td>
  <td class=xl67 width=55 style='width:41pt'></td>
  <td class=xl67 width=31 style='width:23pt'></td>
  <td class=xl67 width=6 style='width:5pt'></td>
  <td class=xl67 width=119 style='width:89pt'></td>
  <td class=xl67 width=51 style='width:38pt'></td>
  <td class=xl67 width=31 style='width:23pt'></td>
  <td class=xl67 width=5 style='width:4pt'></td>
  <td class=xl67 width=109 style='width:82pt'></td>
  <td class=xl67 width=101 style='width:76pt'></td>
  <td class=xl67 width=90 style='width:68pt'></td>
  <td class=xl67 width=95 style='width:71pt'></td>
  <td class=xl67 width=11 style='width:8pt'></td>
  <td class=xl67 width=80 style='width:60pt'></td>
  <td class=xl67 width=80 style='width:60pt'></td>
 </tr>  
</table>
</body>
</html>
