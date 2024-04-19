-- Entity Sets (relations)

create table users
	(user_ID		int NOT NULL AUTO_INCREMENT,
	 username		varchar(20),
	 password		varchar(64), -- 12 char minimum, this should ideally store a salted hash later on
	 member_since	int,
	 email			varchar(320),
	 primary key (user_ID)
	);

create table purchase
	(bill_ID			int NOT NULL,
	 tier				varchar(8)
	 	check (tier in ('Basic', 'Standard', 'Premium')), -- subscription tiers
	 price				numeric(5,2),
	 name_on_card		varchar(30),
	 exp_date			date,
	 cvv				numeric(4,0),
	 zip				numeric(5,0),
	 credit_card_number	numeric(19,0)
	 	check (credit_card_number > 8), -- credit card numbers range from 8 to 19 digits in length
--	 phone				numeric(15,0)
--	 	check (phone = 7 or phone = 10), -- phone numbers range from 7 to 15 digits in length
	 primary key (bill_ID)
	);

create table subscription
	(sub_ID			int NOT NULL,
	 tier			varchar(8)
	 	check (tier in ('Basic', 'Standard', 'Premium')), -- subscription tiers
	 sub_begin		int, --unix timestamp
	 sub_expire     int, --unix timestamp + 2592000 (30 days)
	 auto_renew		boolean NOT NULL, -- a T or F flag, must be defined
	 primary key (sub_ID)
	);

create table ban
	(incident_ID	int NOT NULL,
	 ban_date		date,
	 ban_length		numeric(4,0)
	 	check (ban_length >= 1), -- ban must last 1 to 9,999 days (over 27 years; a permanent ban)
	 reason			varchar(300) NOT NULL, -- must provide a reason for banning a user
	 primary key (incident_ID)
	);

-- Relationships
-- Note: the on deletes may be incorrect, some might need
-- to be on delete set null instead of on delete cascase

create table buys
	(user_ID	int,
	 bill_ID	int,
	 primary key (user_ID, bill_ID),
	 foreign key (user_ID) references users (user_ID)
	 	on delete cascade,
	 foreign key (bill_ID) references purchase (bill_ID)
	 	on delete cascade
	);

create table subscribes
	(bill_ID	int,
	 sub_ID		int,
	 primary key (bill_ID, sub_ID),
	 foreign key (bill_ID) references purchase (bill_ID)
	 	on delete cascade,
	 foreign key (sub_ID) references subscription (sub_ID)
	 	on delete cascade
	);

create table unlocks
	(user_ID	int,
	 sub_ID		int,
	 primary key (user_ID, sub_ID),
	 foreign key (user_ID) references users (user_ID)
	 	on delete cascade,
	 foreign key (sub_ID) references subscription (sub_ID)
	 	on delete cascade
	);

create table bans
	(user_ID		int,
	 incident_ID	int,
	 primary key (user_ID, incident_ID),
	 foreign key (user_ID) references users (user_ID)
	 	on delete cascade,
	 foreign key (incident_ID) references ban (incident_ID)
	 	on delete cascade
	);
