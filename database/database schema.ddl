CREATE SCHEMA streamie;

SET search_path to streamie, public;

CREATE TABLE IF NOT EXISTS Streamie_User (
	rid INT PRIMARY KEY,
	uid VARCHAR(100) NOT NULL,
	fullname VARCHAR(100) NOT NULL,
	gender VARCHAR(20) NOT NULL,
	title VARCHAR(20) NOT NULL,
    pass VARCHAR(100) NOT NULL,
    UNIQUE(rid, uid)
);

CREATE TABLE IF NOT EXISTS User_Youtube_Link(
	rid INT PRIMARY KEY,
	uid VARCHAR(100) NOT NULL,
	Youtube_link VARCHAR(256)
);

CREATE TABLE IF NOT EXISTS User_FriendList (
	rid INT PRIMARY KEY,
	uid VARCHAR(100) NOT NULL,
	friend_list TEXT(255)

);

CREATE TABLE IF NOT EXISTS User_Video_Link (
	rid INT PRIMARY KEY,
	uid VARCHAR(100) NOT NULL,
	video_link TEXT(255)
	
);

CREATE TABLE IF NOT EXISTS Cowathcing (
	rid INT PRIMARY KEY,
	uid VARCHAR(100) NOT NULL,
	sharing_with_rid TEXT,
	sharing_with_uid TEXT
	
);