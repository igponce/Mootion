<?
// The source code packaged with this file is Free Software, Copyright (C) 2006 by
// Inigo Gonzalez igponce at corp dot mootion dot com
// It's licensed under the AFFERO GENERAL PUBLIC LICENSE unless stated otherwise.
// You can get copies of the licenses here:
// 		http://www.affero.org/oagpl.html
// AFFERO GENERAL PUBLIC LICENSE is also included in the file called "COPYING".

// From: blogscloud.php

// $range_named = shared with cloud.php
$hdr_blogscloud			= 'blogs cloud';
$msg_blogs			= 'blogs';
$msg_topblogs			= 'top blogs';

// From: checkfield.php

$err_name_too_short 		= 'name too short';
$err_invalid_characters 	= 'invalid characters detected';
$err_user_already_exists 	= 'user already exists';
$err_invalud_emailaddr 		= 'invalid e-mail address';
$err_duplicate_emailaddr	= 'there\'s a user with that e-mail address';

// From: cloud:php

$range_names  			= array('48 hours', 'last week', 'last month', 'last year', 'all times');
$hdr_tagscloud 			= 'tag cloud';
$msg_tags 			= 'tags';
$msg_top100tags			= 'top 100 tags';


// From: login.php
// From: signup.php

$hdr_login			= 'login';
$msg_forgottenpassword		= 'Forgot your password ?';
$msg_register_newuser           = 'New user ?';
$msg_login			= 'login';
$err_invaludauth		= 'username / password incorrect';
$msg_user			= 'user';
$msg_passwd			= 'password';
$msg_rememberme			= 'remember me';


$msg_passwordrecovery		= 'password recovery';
$err_invalidcaptcha		= 'wrong security code';
$err_inexistentuser		= 'user does not exist';
$err_accountdisabled		= 'account disabled';
$msg_willreceiveemail		= "you'll receive an email with instructions to change your password";
$msg_receiveemail		= 'send email';
$msg_emailsent			= "Please check your email for instructions to change your password.<br/>".
				  "Thank you for using mOOtion.<br/>\n";

$msg_newuser_emailsent		= "You'll receive an email with instructions to activate your mOOtion account.<br>".
				  "Thank you for using mOOtion.<br>\n";

// From: story_mini.php

$msg_removeframe		= 'Remove this Frame';
$msg_gohome			= 'back to mOOtion';
$msg_goback			= 'go back';

// From: profile.php

$header_edit_profile		= 'edit profile for user';
$msg_users			= 'users';
$msg_edit			= 'edit';
$msg_change_your_profile	= 'edit your profile';
$msg_user_realname		= 'RealWorld(tm) name';
$msg_user_email			= 'e-mail address';
$msg_user_webpage		= 'weblog';
$msg_user_passwarn		= 'Type here your new password (just leave it blank to keep the current one)';
$msg_user_pass			= 'password';
$msg_user_passrepeat		= 'confirm password';
$msg_user_updateprofile		= 'update';
$err_user_passdontmatch		= "<em>password</em> and <em>confirm password</em> don't match.";
$msg_user_passchanged		= 'changed password';
$msg_user_updated		= 'profile updated';

// from problem.php

$err_incorrectvote		= 'incorrect vote';
$err_noanonvotes		= 'anonymous voting currently disabled';
$err_wronguserid		= 'wrong user ID';
$err_wrongpass			= 'incorrect password';
$err_votedtwice			= 'already voted';
$err_voteinserterror		= "error registering your vote - That's our fault";
$msg_willtakecare		= "we'll take care of it, thanks"; // ? 'Será tomado en cuenta, gracias'

// from register.php

$msg_signup			= 'signup';
$msg_emailhlptxt		= "must be valid: we'll send you a message there to activate your account";
$btn_verifydata			= 'verify';
$msg_passwordrecovery		= 'password';
$msg_passhlptxt			= 'at least 5 characters long';
$msg_password			= 'password';
$msg_passwordretype		= 'confirm password';
$msg_signmeup			= 'sign me up';

$err_usernametooshort		= 'username: must have at least 3 alphanumeric characters';
$err_usernameinvalichars	= 'username: invalid characters detected';
$err_usernameexist		= 'username: already exists';
$err_emailinvalid		= 'email: invalid email address';
$err_emailduplicated		= 'email: another user has the same email address';

$err_passtooshort		= 'password too short (5 chars. min)';
$err_passmismatch		= "password and confirm password don't match";
$err_registerbot		= 'you must wait 48 hours to register a user from the same IP Address';

$err_invalidcode		= 'invalid security code';
$err_databaseinsert		= 'error adding user in database';
$sign_validation		= 'validation';

// from rss2.php

$rss_server_name		= 'mootion.com';
$msg_rssdescription		= 'mOOving pictures promotion and guide';
$rss_language			= 'EN';
$rrs_title_all			= 'mOOtion: all videos';
$rss_title_published		= 'mOOtion: published videos';
$rss_title_queued		= "mOOtion: what's new";
$rss_generator			= 'http://mOOtion.com';
// from feedburner-rss2.php

$rss_mostvotedon		= 'mOOtion: most popular on';

// from index.php

$msg_all			= 'all';
$msg_pendinglinks		= 'New Videos';
$msg_searchpending		= 'Search new Videos';
$msg_recommended		= 'recommended';
$msg_discarded			= 'discarded';
$msg_yourvoteimportant		= 'Your vote (mOOve) is important';
$msg_whyvote			= 'Vote your favorite videos. The most voted will be mOOved up to the front page.';
$msg_usecategories		= 'use categories to narrow your search';
$msg_searchfor			= 'search for';
$msg_search			= 'search';
$msg_searchresults		= 'search results';
$msg_lastpublished		= 'Top Videos';
$msg_lastvideos			= 'Top Videos';



// from topstories.php

$hdr_mostpopular		= 'popular';
$msg_videos			= 'videos';
$msg_stats			= 'statistics';
$msg_mostpopular		= 'most popular videos';
$msg_subscribepopular		= 'subscribe to the most popular videos';

// from topusers.php

$items = array('user', 'videos', 'published videos', 'comments', 'mOOves', 'mOOves from published');
$hdr_topusers			= 'top users';
$msg_users			= 'users';
$msg_topusers			= 'users';

// from user.php

$hdr_userprofile		= 'user profile';
$personal_data			= 'profile';
$sent_videos 			= 'videoblog';
$published 			= 'published';
$comments 			= 'comments';
$voted 				= 'voted';
$prefered_editors		= 'prefered editors';
$voted_by 			= 'voted by';
$top_users			= 'users';
$personal_info			= 'personal info';
$msg_user			= 'user';
$msg_name			= 'username';
$msg_karma			= 'karma';
$msg_website			= 'website';
$msg_sentvideos			= 'videoblog';
$msg_published			= 'published';
$msg_allcomments		= 'comments';
$msg_votes			= 'votes';
$msg_from			= 'user since';
$msg_timesvoted			= 'received votes';
$msg_votingstats		= 'statistics';


// from video_submit.php

$title_submitvideos		= 'Submit Video';
$msg_videoaddress		= 'Video Address';
$btn_continue			= 'continue &#187;';
$msg_step			= 'step';
$msg_step1			= $msg_step . ' 1: address';
$msg_step2                      = $msg_step . ' 2: details';
$msg_step3                      = $msg_step . ' 3: final checkup';
$msg_submitvideo1		= $title_submitvideos . ': ' .$msg_step.' 1 (of 3)';
$msg_submitvideo2		= $title_submitvideos . ': ' .$msg_step.' 2 (of 3)';
$msg_submitvideo3		= $title_submitvideos . ': ' .$msg_step.' 3 (of 3)';
$err_needsmorevotes		= 'You have to vote more videos before you send us new ones.';
$err_neededtosubmit		= "To submit videos, you'll have to vote at least ";
$msg_videos			= " videos";
$msg_govoting			= 'clic here to start voting';
$err_invalidurl	                = 'invalid URL';
$msg_tryanoter                  = 'try another URL';
$err_recentsubmitted            = "You've just submited a link from the same site some minutes ago.";
$msg_youmustwait                = 'you must wait ';
$msg_whywait                    = ' hours before sending videos from the same site (to avoid <em>spam</em> and <em>self-promotion</em>)';
$msg_RTFM                       = 'Read the FAQ';
$err_duplicated                 = 'Already Submitted!';
$msg_weresorry                  = "we're sorry";
$msg_voteduplicated             = 'clic here to vote or comment the video you sent before';
$btn_goback                     = '&#171; go back';
$msg_videoinfo                  = 'Video Information';
$msg_filesize                   = 'Size';
$msg_pagetitle                  = 'Page Title';
$msg_itsablog                   = 'hmmmmm... looks like a blog (vlog?)';
$msg_details                    = 'Details';
$msg_videotitle                 = 'Title';
$msg_inserttitle                = 'video title (120 chars max.)';
$msg_tags                       = 'Tags';
$msg_taghlp                     = 'some short, generic, words comma-separated (,).</strong> Example: <em>nyw your, minorca , new year, chicken</em></span>';
$msg_description                = 'Description';
$msg_descriptionhlp             = 'Describe this video with your own words. Two to five sencenceses are okay.';
$msg_categories                 = 'Categories';
$msg_categorieshlp              = 'Select the category that describes best this video';
$msg_trackback                  = 'trackback';
$msg_trackbackhml               = "Add or change the trackback if it isn't detected automagically";
$msg_videodetails               = 'Video Details';
$warn_thisisademo               = 'ATTENTION: this is just a <b>preview</b>!';
$msg_nowyoucan                  = 'Now you can 1) ';
$msg_sendqueue                  = 'send this video to the publishing queue and end submitting';
$msg_dogfoodhlp                 = '. Any other clic will turn your submission into <del>catfood</del>coowfood (or not).';
$btn_submit                     = 'submit &#187;';
$err_incorrectkey               = 'Incorrect key';
$err_alreadyqueued              = 'Video already in the publishing queue';
$err_incompletetitltxt          = 'Incomplete title or text';
$err_toolongtitltxt             = 'Title or text too long';
$err_untagged                   = 'Must supply some tags for this video';
$err_titleisurl                 = "Don't write URLs as a title; URLs don't give information";
$err_nocategory                 = 'Category not selected';
$msg_oops                       = 'oops!';
$msg_instructionlist =		'<li><strong>Videos:</strong> This URL has a video? </li>' .
				'<li><string>Link to the original source :</strong> Give credit where credit is due</li>' .
				'<li><strong>Search Fist:</strong> Please do your best not to submit duplicate content</li>' .
				'<li><strong>Write a gOOd description:</strong> Tell us why do you feel this video is interesting.</li>';

// From: fubar.php

?>
