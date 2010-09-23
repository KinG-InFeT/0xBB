<?php
/**************************************************************************
 * 		        		0xBB ~ Bullettin Board						      *
 **************************************************************************
 * This program is free software: you can redistribute it and/or modify 
 * it under the terms of the GNU General Public License as published by 
 * the Free Software Foundation, either version 3 of the License, or   
 * (at your option) any later version.                                  
 *                                                                      
 * This program is distributed in the hope that it will be useful,      
 * but WITHOUT ANY WARRANTY; without even the implied warranty of       
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the        
 * GNU General Public License for more details.                         
 *                                                                      
 * You should have received a copy of the GNU General Public License    
 * along with this program.  If not, see <http://www.gnu.org/licenses/>
 * =========================================================================*
 * Software:					0xBB
 * Software version:			1.0 ~ RC2
 * Author:						KinG-InFeT
 * Copyleft:					GNU General Public License              
 * =========================================================================*
 * style.php                                                        
 ***************************************************************************/
 
 /******************************
  * Thanks BlackLight (nullBB) *
  ******************************/
include "kernel.php";

print "
html  {
	height: 100%;
}

body {
	font-size: 11px;
	font-family: Terminus, Monospace, Courier;
	position: center;
	height: 100%;
	margin: 0;
}

div {
	position: center;
	margin-left: auto;
	margin-right: auto;
	overflow: hidden;
	width: hidden;
}

table {
	position: center;
	font-size: 11px;
	overflow: none;
}

a:link, a:visited {
	text-decoration: underline;
	cursor: crosshair;
}

a:hover {
	text-decoration: none;
	cursor: crosshair;
}


pre {
	font-family: Verdana;
	font-size: 11px;
}

input, textarea {
	font-family: Verdana;
	font-size: 11px;
}

input {
	width: 90px;
}

.menu {
	width: 990px;
	height: 24px;
	top: 70px;
}

.main {
	vertical-align: top;
	position: center;
	font-size: 13px;
	width: 990
	height: 403px;
	padding: 6px;
	margin: 0 auto;
}

.table_info_user {
	vertical-align: top;
	width: 290px;
}

.table_main {
	vertical-align: top;
	width: 950px;
	padding: 6px;
	text-align: left;
	font-size: 11px;
	margin: 0 auto;
}

.topicuserinfo  {
	padding: 10px;
	width: 50px;
	vertical-align: top;
}

.topiccontent  {
	vertical-align: top;
	text-align: left;
	padding: 10px;
	width:700;
}

.userinfoentry  {
	font-size: 11px;
}

li.posttime  {
	vertical-align: top;
	text-align: right;
	font-size: 10px;
	list-style-type: none
}


.path {
	width: 990px;
	height: 35px;
	top: 110px;
}

.footer  {
	position: center;
	height: 70px;
	text-align: center;
}

.login {
	text-align: center;
	width: 30%;
	height: 30%;
	left: 34%;
	top: 30%;
}

.register {
	text-align: center;
	width: 32%;
	height: 55%;
	left: 34%;
	top: 20%;
}

.forum_menu {
	width: 90%;
	height: 5%;
	left: 5%;
	top: 5%;
}

.forums {
	position: auto;
	padding-top: 5px;
	padding-bottom: 5px;
}

.topic_title {
	width: 60%;
}

.topic_author {
	width: 25%;
}

.topic_replies {
	width: 15%;
}

.user {
	width: 200;
	height: 200;
	left: 200;
	padding: 10px;
}

.message {
	vertical-align: top;
	width: 700;
	height: 200;
	left: 425;
	padding: 10px;
	overflow: auto;
}

.reply {
	width: 930;
	height: 240;
	left: 200;
	padding: 10px;
}

.reply_text {
	width: 100%;
	height: 60%;
	left: 2%;
}

.new_topic {
	width: 87%;
	height: 90%;
	left: 5%;
	top: 5%;
}

.topic_data {
	width: 100%;
	height: 60%;
	left: 2%;
	top: 7%;
}

.title {
	width: 90%;
}

.edit {
	width: 80%;
	height: 80%;
	left: 5%;
	top: 2%;
	padding: 10px;
}

.profile {
	position: center;
	width: 30%;
	height: 40%;
	left: 30%;
	top: 20%;
	overflow: auto;
	padding: 10px;
}

.main_admin {
	width: 90%;
	height: 75%;
	left: 5%;
	top: 5%;
	text-decoration:none
	overflow: auto;
	padding: 10px;
}

.forum_text {
	width: 100%;
	height: 20%;
}

.pm {
	width: 90%;
	height: 80%;
	top: 5%;
	left: 5%;
	overflow: auto;
	padding: 10px;
}

.pm_text {
	width: 100%;
	height: 75%;
}

.settings {
	width: 90%;
	height: 75%;
	top: 5%;
	left: 5%;
	padding: 10px;
}

.error_msg {
	width: 95%;
	height: 50%;
	top: 10%;
	color: #FF0000;
	position: center;
	padding: 10px;
}

.success_msg {
	width: 30%;
	height: 50%;
	left: 35%;
	top: 10%;
	color: green;
	padding: 10px;
}

/* BBcode */

.code{
	border : 1px solid #444;
	background-color: #222;
	width : 500px;
    padding: 10px;
    position: static;
    overflow: auto;
}

.quote{
	border : 1px dotted #444;
	background-color: #000;
	color:#fff;
	width : 500px;
	font-size : 12px;
    padding: 10px;
    position: static;
    overflow: auto;
}

td.users  {
	border: 1px solid #424242;
	padding: 3px;
}

/* Fine CSS BBcode */

/* CSS Karma */
.karma_meno {
	width: 30px;
	border: 1px solid red;
}
.karma_più {
	width: 30px;
	border: 1px solid green;
}
/* Fine CSS Karma */
";

list ($username, $password) = get_data ();
if (!login ($username, $password)) {
	$text = "#FFFFFF";//#FFFFFF
	$background = "#000000";//#000000
}else{
	$query = "SELECT text, background FROM ".PREFIX."users WHERE username = '{$username}'";
	list ($text, $background) = mysql_fetch_row (mysql_query ($query));
}

print "
div {
	border: 1px solid $text;
}

input, textarea {
	background: $background;;
	color: $text;
	border: 1px solid $text;
}

body {
	background: $background;
	color: $text;
}

a:link, a:visited {
	color: $text;
}

a:hover {
	color: $text;
}
.main {
	border: 0px solid $background;
}

.path {
	border: 0px solid $background;
}

.new_topic {
	border: 0px solid $background;
}

.forum_menu {
	border: 1px solid $text;
}

.forum {
	border: 1px solid $text;
}

.all_topics {
	border: 1px solid $text;
}

.footer {
	border: 0.1px solid $background;
}

.edit {
	border: 0px solid $background;
}

.forums {
	border-bottom: 1px solid $text;
}

.reply {
	border: 1px solid $background;
}

.error_msg {
	border: 0px solid $background;
}

.table_main {
	border: 1px solid $text;
	background-color: $background;
}

.topicuserinfo  {
	border-right: 1px solid $text;
	border-bottom: 1px solid $text;
}

.topiccontent  {
	border-bottom: 1px solid $text;
}

.userinfoentry  {
	border-bottom: 1px dotted $text;
}

li.posttime  {
	border-bottom: 1px dotted $text;
}
";
?>
