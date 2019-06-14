CREATE TABLE access (
	id				INTEGER PRIMARY KEY AUTOINCREMENT,
	service		TEXT NOT NULL,
	uid				TEXT NOT NULL,
	token			TEXT,
	refresh		TEXT,
	status			INTEGER NOT NULL DEFAULT 1,
	_errors			TEXT,
	created_at		DATETIME,
	updated_at 	DATETIME,
	expires_at		DATETIME,
	deleted_at		DATETIME
);

CREATE TABLE items (
	id				INTEGER PRIMARY KEY AUTOINCREMENT,

	service		TEXT NOT NULL,
	account_id		TEXT NOT NULL,
	campaign_id	TEXT NOT NULL,
	adgroup_id		TEXT NOT NULL,
	item_id			TEXT NOT NULL,

	_access			TEXT,
	_account		TEXT,
	_campaign		TEXT,
	_adgroup		TEXT,
	_iteminfo		TEXT,
	_errors			TEXT,

	status			INTEGER NOT NULL DEFAULT 1,

	created_at		DATETIME,
	updated_at		DATETIME,
	visited_at		DATETIME,
	deleted_at		DATETIME
);

CREATE TABLE records (
	item_id			INTEGER NOT NULL,
	day_id			DATE NOT NULL,

	impressions	INTEGER,
	clicks			INTEGER,
	actions		INTEGER,
	costs			REAL,

	_stats			TEXT,

	created_at		DATETIME,
	updated_at		DATETIME,

	PRIMARY KEY (item_id, day_id)
);