#
#	@author		bluelovers
#	@copyright	2012
#

Options All -Indexes

RewriteEngine On
RewriteBase <?php e(BASE_URL_ROOT) ?>

RewriteRule (\.svn|\.git|trust_path)[\/\\].* ReadMe.html [NC,L]

RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^static/image/(char|char_rev)/.*$ static/image/$1/noimage.png [L]
RewriteRule ^static/image/(icon/(item|skill))/.*$ static/image/$1/noimage.png [L]

RewriteRule ^(static/image) - [L]

RewriteCond %{REQUEST_FILENAME} !-d
RewriteCond %{REQUEST_FILENAME} !-f
RewriteRule ^.*	index.php [L,QSA]

<Files *.dat>
	order allow,deny
	deny from all
</Files>

<Files 403.shtml>
	order allow,deny
	allow from all
</Files>

<Files *.js.gz>
	AddEncoding gzip .js
	ForceType application/x-javascript
</Files>

<Files *.css.gz>
	AddEncoding gzip .css
	ForceType text/css
</Files>

<FilesMatch "\.(ttf|otf|fon|ttc)$">
	<IfModule mod_headers.c>
		Header set Access-Control-Allow-Origin "*"
	</IfModule>
	<IfModule mod_headers>
		Header set Access-Control-Allow-Origin "*"
	</IfModule>
</FilesMatch>
