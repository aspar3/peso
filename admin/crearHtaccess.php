<?php 
//error_reporting(E_ALL);
//ini_set("display_errors", 1);

	include("in_variables.php");

	$conMsi= crearConexionMysqli();
	
	$fp = fopen( "../.htaccess", "w" );
	
	fputs($fp,"Options +FollowSymlinks -multiViews -Indexes\n");
	fputs($fp,"ErrorDocument 404 /not-found\n");
	
	fputs($fp,"# BEGIN WordPress\n");
	fputs($fp,"AddHandler application/x-httpd-ea-php70 .php\n"); //WP
	fputs($fp,"<IfModule mod_expires.c>\n"); //WP
	fputs($fp,"ExpiresActive On\n"); //WP
	fputs($fp,"ExpiresByType image/jpg \"access plus 1 year\"\n"); //WP
	fputs($fp,"ExpiresByType image/jpeg \"access plus 1 year\"\n"); //WP
	fputs($fp,"ExpiresByType image/gif \"access plus 1 year\"\n"); //WP
	fputs($fp,"ExpiresByType image/png \"access plus 1 year\"\n"); //WP
	fputs($fp,"ExpiresByType text/css \"access plus 1 month\"\n"); //WP
	fputs($fp,"ExpiresByType application/pdf \"access plus 1 month\"\n"); //WP
	fputs($fp,"ExpiresByType text/javascript \"access plus 1 month\"\n"); //WP
	fputs($fp,"ExpiresByType text/html \"access plus 2 hours\"\n"); //WP
	fputs($fp,"ExpiresByType image/x-icon \"access plus 1 year\"\n"); //WP
	fputs($fp,"ExpiresDefault \"access plus 6 hours\"\n"); //WP
	fputs($fp,"</IfModule>\n"); //WP
	fputs($fp,"Header set X-Endurance-Cache-Level \"2\"\n"); //WP
	fputs($fp,"<IfModule mod_rewrite.c>\n"); //WP
	fputs($fp,"RewriteEngine On\n"); //WP
	fputs($fp,"RewriteBase /\n"); //WP
	//se mira los diferentes idiomas activados
	$sqlIdm = "SELECT *
				FROM IDIOM
				WHERE IDM_ACTIVO = 1
				ORDER BY IDM_ORDER";
	$resultIdm= $conMsi->query($sqlIdm);
	while($rowIdm=$resultIdm->fetch_assoc()){
		include("../lit/iURL_".$rowIdm["IDM_FILES_SUFIX"].".php");
		
		$idmLoc = $rowIdm["IDM_LOCALE"];
		$urlAbbrev = $rowIdm["IDM_URL_ABBREV"];
		if ($urlAbbrev!=""){
			fputs($fp,"RewriteRule ^".$urlAbbrev."$ /index.php?idm=$urlAbbrev [L]\n");
			fputs($fp,"RewriteRule ^".$urlAbbrev."/$ /index.php?idm=$urlAbbrev [L]\n");
			$urlAbbrev = $urlAbbrev."/";
		}
		fputs($fp,"RewriteRule ^".$urlAbbrev."ver/([0-9]+)/([^/]+)$ /login-app.php?idm=$urlAbbrev&idmLoc=$idmLoc&verify=S&idUser=$1&codValida=$2 [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev."res/([0-9]+)/([^/]+)$ /login-reset.php?idm=$urlAbbrev&idmLoc=$idmLoc&idUser=$1&codValida=$2 [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev."gue/([0-9]+)/([0-9]+)/([^/]+)$ /guest-rate.php?idm=$urlAbbrev&idmLoc=$idmLoc&guest=S&idMeeting=$1&idUser=$2&codValida=$3 [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLguestRateDates."$ /guest-rate-dates.php?idm=$urlAbbrev&idmLoc=$idmLoc [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLmyMeetingDetail."/([0-9]+)$ /my-meeting-detail.php?idm=$urlAbbrev&idmLoc=$idmLoc&idMeeting=$1 [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLmyMeetingClose."/([0-9]+)$ /my-meeting-detail.php?idm=$urlAbbrev&idmLoc=$idmLoc&close=1&idMeeting=$1 [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLmyMeetingApproveDates."/([0-9]+)$ /my-meeting-approve-dates.php?idm=$urlAbbrev&idmLoc=$idmLoc&close=1&idMeeting=$1 [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLmyMeetingDates."/([0-9]+)$ /my-meeting-dates.php?idm=$urlAbbrev&idmLoc=$idmLoc&close=1&idMeeting=$1 [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLmyMeetingGuests."/([0-9]+)$ /my-meeting-guests.php?idm=$urlAbbrev&idmLoc=$idmLoc&close=1&idMeeting=$1 [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLmyMeetingModify."/([0-9]+)$ /my-meeting-modify.php?idm=$urlAbbrev&idmLoc=$idmLoc&close=1&idMeeting=$1 [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLmyProfile."$ /my-profile.php?idm=$urlAbbrev&idmLoc=$idmLoc [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLnewMeeting."$ /new-meeting.php?idm=$urlAbbrev&idmLoc=$idmLoc [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLmyRecurringMeetings."$ /my-meetings-recurring.php?idm=$urlAbbrev&idmLoc=$idmLoc [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLmyMeetings."$ /my-meetings.php?idm=$urlAbbrev&idmLoc=$idmLoc [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLmyMeetings."/([0-9]+)/([0-9]+)$ /my-meetings.php?idm=$urlAbbrev&idmLoc=$idmLoc&order=$1&asc=$2 [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLmyContacts."$ /my-contacts.php?idm=$urlAbbrev&idmLoc=$idmLoc [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLmyContactDetail."/([0-9]+)$ /my-contact-detail.php?idm=$urlAbbrev&idmLoc=$idmLoc&iduserCon=$1&t=1 [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLmyContactModify."/([0-9]+)$ /my-contact-detail.php?idm=$urlAbbrev&idmLoc=$idmLoc&iduserCon=$1&t=2 [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLsignUp."$ /sign-up.php?idm=$urlAbbrev&idmLoc=$idmLoc [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLlogin."$ /login-app.php?idm=$urlAbbrev&idmLoc=$idmLoc [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLaboutUs."$ /about-us.php?idm=$urlAbbrev&idmLoc=$idmLoc [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLsignOut."$ /sign-out.php?idm=$urlAbbrev&idmLoc=$idmLoc [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLloginReminder."$ /login-reminder.php?idm=$urlAbbrev&idmLoc=$idmLoc [L]\n");
		fputs($fp,"RewriteRule ^".$urlAbbrev.$litURLnotFound."$ /not-found.php?idm=$urlAbbrev&idmLoc=$idmLoc [L]\n");
	}
	
	fputs($fp,"RewriteRule ^index\.php$ - [L]\n"); //WP
	fputs($fp,"RewriteCond %{REQUEST_FILENAME} !-f\n"); //WP
	fputs($fp,"RewriteCond %{REQUEST_FILENAME} !-d\n"); //WP
	fputs($fp,"RewriteRule . /index.php [L]\n"); //WP
	fputs($fp,"</IfModule>\n"); //WP
	fputs($fp,"# END WordPress\n"); //WP
		
	/*
	fputs($fp,"<IfModule mod_expires.c>\n");
	fputs($fp,"# Enable expirations\n");
	fputs($fp,"ExpiresActive On\n");
	fputs($fp,"# Default directive\n");
	fputs($fp,"ExpiresDefault \"access plus 1 month\"\n");
	fputs($fp,"# My favicon\n");
	fputs($fp,"ExpiresByType image/x-icon \"access plus 1 year\"\n");
	fputs($fp,"# Images\n");
	fputs($fp,"ExpiresByType image/gif \"access plus 1 month\"\n");
	fputs($fp,"ExpiresByType image/png \"access plus 1 month\"\n");
	fputs($fp,"ExpiresByType image/jpg \"access plus 1 month\"\n");
	fputs($fp,"ExpiresByType image/jpeg \"access plus 1 month\"\n");
	fputs($fp,"# CSS\n");
	fputs($fp,"ExpiresByType text/css \"access plus 1 month\"\n");
	fputs($fp,"# Javascript\n");
	fputs($fp,"ExpiresByType application/javascript \"access plus 1 month\"\n");
	fputs($fp,"</IfModule>\n");
	fputs($fp,"<FilesMatch \".(js|css|html|htm|php|xml)$\">\n");
	fputs($fp,"SetOutputFilter DEFLATE\n");
	fputs($fp,"</FilesMatch>\n");
	*/
	/*
	fputs($fp,"RewriteEngine on\n");
	fputs($fp,"RewriteCond %{HTTP_USER_AGENT} libwww-perl.*\n");
	fputs($fp,"RewriteRule .* ? [F,L]\n");
	fputs($fp,"RewriteCond %{HTTP:X-Forwarded-Proto} !https\n");
	fputs($fp,"RewriteRule .* https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]\n");
	fputs($fp,"RewriteRule ^index.html$ /index.php [L]\n");
	fputs($fp,"RewriteRule ^index.htm$ /index.php [L]\n");
	fputs($fp,"RewriteRule ^index.cgi$ /index.php [L]\n");
	fputs($fp,"RewriteRule ^images(/|$) - [L,NC]\n");
	
	*/
	echo "Done.";
		
	fclose($fp);
	mysqli_close($conMsi);
	die(); 
?>
