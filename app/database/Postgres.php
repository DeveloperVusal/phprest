<?php
namespace App\Database;

use App\Core\Database;

class Postgres extends Database {
	
	public static function __constructStatic()
	{
		self::load_config();
		self::connect_db();
	}

	protected static function connect_db()
	{
		try {
			$cfg = self::get_config()['connections']['w_pgsql_accounts'];
			$dsn = $cfg['driver'].':host='.$cfg['host'].';port='.$cfg['port'].';dbname='.$cfg['dbname'].';user='.$cfg['username'].';password='.$cfg['password'];

			self::$dbn = new \PDO($dsn);

			return self::$dbn;
		} catch (\PDOException $e) {
			return $e->getMessage();
		}
	}
}

/*
CREATE TABLE users (
	id SERIAL PRIMARY KEY,
	email VARCHAR (150) UNIQUE NOT NULL,
	password VARCHAR (255) NOT NULL,
	activation BOOLEAN DEFAULT false,
	name VARCHAR (200),
	surname VARCHAR (200),
	token_secret_key TEXT NOT NULL,
	updated_at TIMESTAMP DEFAULT NULL,
    created_at TIMESTAMP NOT NULL
);

CREATE TABLE tokens (
	id SERIAL PRIMARY KEY,
	user_id BIGINT NOT NULL,
	access TEXT UNIQUE NOT NULL,
	refresh TEXT UNIQUE NOT NULL,
    created_at TIMESTAMP NOT NULL,
    CONSTRAINT fk_userId FOREIGN KEY(user_id) REFERENCES users(id)
);

CREATE TABLE apps (
	id SERIAL PRIMARY KEY,
	user_id BIGINT UNIQUE NOT NULL,
	secret_key TEXT UNIQUE NOT NULL,
	domain VARCHAR(255),
	update_at TIMESTAMP,
    created_at TIMESTAMP NOT NULL,
    CONSTRAINT fk_userId FOREIGN KEY(user_id) REFERENCES users(id)
);


CREATE FUNCTION create_refresh_token() RETURNS text AS $$
declare
	part1 text;
	part2 text;
	part3 text;
    r_token text;
    done bool;
BEGIN
    done := false;
    WHILE NOT done loop
    	part1 := substring(md5(''||now()::text||random()::text), 1 , 20);
		part2 := substring(md5(''||now()::text||random()::text), 1 , 20);
		part2 := substring(md5(''||now()::text||random()::text), 1 , 20);
        r_token := @part1||'-'||@part2||'-'||@part3;
        done := NOT exists(SELECT 1 FROM tokens WHERE refresh = r_token);
    END LOOP;
    RETURN r_token;
END;
$$ LANGUAGE PLPGSQL VOLATILE;
*/