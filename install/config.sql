-- initial configuration for PhpWebGallery

INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('default_language','en_UK.iso-8859-1','Default gallery language');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('default_template','yoga/clear','Default gallery style');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('default_maxwidth','','maximum width authorized for displaying images');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('default_maxheight','','maximum height authorized for the displaying images');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('nb_comment_page','10','number of comments to display on each page');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('log','false','keep an history of visits on your website');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('comments_validation','false','administrators validate users comments before becoming visible');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('comments_forall','false','even guest not registered can post comments');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('nb_image_line','5','Number of images displayed per row');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('nb_line_page','3','Number of rows displayed per page');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('recent_period','7','Period within which pictures are displayed as new (in days)');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('auto_expand','false','Auto expand of the category tree');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('show_nb_comments','false','Show the number of comments under the thumbnails');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('gallery_locked','false','Lock your gallery temporary for non admin users');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('gallery_title','PhpWebGallery demonstration site','Title at top of each page and for RSS feed');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('gallery_description','My photos web site','Short description displayed with gallery title');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('gallery_url','http://demo.phpwebgallery.net','URL given in RSS feed');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('rate','true','Rating pictures feature is enabled');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('rate_anonymous','true','Rating pictures feature is also enabled for visitors');

