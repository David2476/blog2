create table if not exists comment_reply(  #未使用
	id          int      	    auto_increment primary key,
	content     text          not null,
	user_id     int           not null,
	comment_id  int           not null,
	img1	   varchar(100)  , #用户头像
	foreign key (user_id)     references user(id),
	foreign key (comment_id)    references comment(id)
);