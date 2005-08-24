-- initial configuration for PhpWebGallery

INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('prefix_thumbnail','TN-','thumbnails filename prefix');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('mail_webmaster','','webmaster mail');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('default_language','en_UK.iso-8859-1','Default gallery language');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('default_template','default','Default gallery style');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('default_maxwidth','','maximum width authorized for displaying images');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('default_maxheight','','maximum height authorized for the displaying images');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('nb_comment_page','10','number of comments to display on each page');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('upload_maxfilesize','150','maximum filesize for the uploaded pictures');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('upload_maxwidth','800','maximum width authorized for the uploaded images');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('upload_maxheight','600','maximum height authorized for the uploaded images');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('upload_maxwidth_thumbnail','150','maximum width authorized for the uploaded thumbnails');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('upload_maxheight_thumbnail','100','maximum height authorized for the uploaded thumbnails');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('log','false','keep an history of visits on your website');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('comments_validation','false','administrators validate users comments before becoming visible');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('comments_forall','false','even guest not registered can post comments');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('nb_image_line','5','Number of images displayed per row');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('nb_line_page','3','Number of rows displayed per page');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('recent_period','7','Period within which pictures are displayed as new (in days)');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('auto_expand','false','Auto expand of the category tree');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('show_nb_comments','false','Show the number of comments under the thumbnails');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('use_iptc','false','Use IPTC data during database synchronization with files metadata');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('use_exif','false','Use EXIF data during database synchronization with files metadata');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('show_iptc','false','Show IPTC metadata on picture.php if asked by user');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('show_exif','true','Show EXIF metadata on picture.php if asked by user');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('authorize_remembering','true','Authorize users to be remembered, see $conf{remember_me_length}');
INSERT INTO phpwebgallery_config (param,value,comment) VALUES ('gallery_locked','false','Lock your gallery temporary for non admin users');
