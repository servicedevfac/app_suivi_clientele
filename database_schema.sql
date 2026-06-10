-- Schema de base de données

CREATE TABLE "migrations" ("id" integer primary key autoincrement not null, "migration" varchar not null, "batch" integer not null);

CREATE TABLE sqlite_sequence(name,seq);

CREATE TABLE "users" ("id" integer primary key autoincrement not null, "nom" varchar not null, "prenom" varchar, "email" varchar not null, "telephone" varchar, "photo" varchar, "is_active" tinyint(1) not null default '1', "last_login_at" datetime, "email_verified_at" datetime, "password" varchar not null, "remember_token" varchar, "created_at" datetime, "updated_at" datetime, "deleted_at" datetime);

CREATE TABLE "password_reset_tokens" ("email" varchar not null, "token" varchar not null, "created_at" datetime, primary key ("email"));

CREATE TABLE "sessions" ("id" varchar not null, "user_id" integer, "ip_address" varchar, "user_agent" text, "payload" text not null, "last_activity" integer not null, primary key ("id"));

CREATE TABLE "cache" ("key" varchar not null, "value" text not null, "expiration" integer not null, primary key ("key"));

CREATE TABLE "cache_locks" ("key" varchar not null, "owner" varchar not null, "expiration" integer not null, primary key ("key"));

CREATE TABLE "jobs" ("id" integer primary key autoincrement not null, "queue" varchar not null, "payload" text not null, "attempts" integer not null, "reserved_at" integer, "available_at" integer not null, "created_at" integer not null);

CREATE TABLE "job_batches" ("id" varchar not null, "name" varchar not null, "total_jobs" integer not null, "pending_jobs" integer not null, "failed_jobs" integer not null, "failed_job_ids" text not null, "options" text, "cancelled_at" integer, "created_at" integer not null, "finished_at" integer, primary key ("id"));

CREATE TABLE "failed_jobs" ("id" integer primary key autoincrement not null, "uuid" varchar not null, "connection" varchar not null, "queue" varchar not null, "payload" text not null, "exception" text not null, "failed_at" datetime not null default CURRENT_TIMESTAMP);

CREATE TABLE "filiales" ("id" integer primary key autoincrement not null, "nom" varchar not null, "adresse" text, "telephone" varchar, "email" varchar, "ville" varchar, "pays" varchar, "statut" varchar not null default 'actif', "created_at" datetime, "updated_at" datetime, "deleted_at" datetime);

CREATE TABLE "permissions" ("id" integer primary key autoincrement not null, "name" varchar not null, "guard_name" varchar not null, "created_at" datetime, "updated_at" datetime);

CREATE TABLE "roles" ("id" integer primary key autoincrement not null, "name" varchar not null, "guard_name" varchar not null, "created_at" datetime, "updated_at" datetime);

CREATE TABLE "model_has_permissions" ("permission_id" integer not null, "model_type" varchar not null, "model_id" integer not null, foreign key("permission_id") references "permissions"("id") on delete cascade, primary key ("permission_id", "model_id", "model_type"));

CREATE TABLE "model_has_roles" ("role_id" integer not null, "model_type" varchar not null, "model_id" integer not null, foreign key("role_id") references "roles"("id") on delete cascade, primary key ("role_id", "model_id", "model_type"));

CREATE TABLE "role_has_permissions" ("permission_id" integer not null, "role_id" integer not null, foreign key("permission_id") references "permissions"("id") on delete cascade, foreign key("role_id") references "roles"("id") on delete cascade, primary key ("permission_id", "role_id"));

CREATE TABLE "sources" ("id" integer primary key autoincrement not null, "nom" varchar not null, "description" text, "statut" varchar not null default 'actif', "created_at" datetime, "updated_at" datetime, "deleted_at" datetime);

CREATE TABLE "campagnes" ("id" integer primary key autoincrement not null, "filiale_id" integer not null, "nom" varchar not null, "description" text, "budget" numeric, "date_debut" date, "date_fin" date, "statut" varchar not null default 'actif', "created_at" datetime, "updated_at" datetime, "deleted_at" datetime, foreign key("filiale_id") references "filiales"("id") on delete cascade);

CREATE TABLE "produits" ("id" integer primary key autoincrement not null, "filiale_id" integer not null, "nom" varchar not null, "description" text, "prix" numeric, "type" varchar, "statut" varchar not null default 'actif', "created_at" datetime, "updated_at" datetime, "deleted_at" datetime, foreign key("filiale_id") references "filiales"("id") on delete cascade);

CREATE TABLE "prospect_histories" ("id" integer primary key autoincrement not null, "prospect_id" integer not null, "user_id" integer, "action" varchar not null, "description" text, "ancien_statut" varchar, "nouveau_statut" varchar, "created_at" datetime, "updated_at" datetime, foreign key("prospect_id") references "prospects"("id") on delete cascade, foreign key("user_id") references "users"("id") on delete set null);

CREATE TABLE "prospects" ("id" integer primary key autoincrement not null, "commercial_id" integer, "source_id" integer, "campagne_id" integer, "filiale_id" integer not null, "nom" varchar not null, "prenom" varchar, "email" varchar, "telephone" varchar, "entreprise" varchar, "profession" varchar, "adresse" text, "ville" varchar, "statut" varchar not null default 'Nouveau', "besoin" text, "commentaire" text, "date_contact" datetime, "prochain_rappel" datetime, "created_at" datetime, "updated_at" datetime, "deleted_at" datetime, foreign key("commercial_id") references "users"("id") on delete set null, foreign key("source_id") references "sources"("id") on delete set null, foreign key("campagne_id") references "campagnes"("id") on delete set null, foreign key("filiale_id") references "filiales"("id") on delete cascade);

CREATE TABLE "relances" ("id" integer primary key autoincrement not null, "prospect_id" integer not null, "commercial_id" integer, "date_relance" date not null, "heure_relance" time, "canal" varchar, "commentaire" text, "statut" varchar not null default 'En attente', "created_at" datetime, "updated_at" datetime, "deleted_at" datetime, foreign key("prospect_id") references "prospects"("id") on delete cascade, foreign key("commercial_id") references "users"("id") on delete set null);

CREATE TABLE "tasks" ("id" integer primary key autoincrement not null, "user_id" integer not null, "prospect_id" integer, "titre" varchar not null, "description" text, "priorite" varchar not null default 'Moyenne', "date_limite" datetime, "statut" varchar not null default 'À faire', "created_at" datetime, "updated_at" datetime, "deleted_at" datetime, foreign key("user_id") references "users"("id") on delete cascade, foreign key("prospect_id") references "prospects"("id") on delete set null);

CREATE TABLE "clients" ("id" integer primary key autoincrement not null, "prospect_id" integer, "commercial_id" integer, "filiale_id" integer not null, "nom" varchar not null, "prenom" varchar, "email" varchar, "telephone" varchar, "adresse" text, "ville" varchar, "entreprise" varchar, "statut" varchar not null default 'Actif', "date_conversion" datetime, "created_at" datetime, "updated_at" datetime, "deleted_at" datetime, foreign key("prospect_id") references "prospects"("id") on delete set null, foreign key("commercial_id") references "users"("id") on delete set null, foreign key("filiale_id") references "filiales"("id") on delete cascade);

CREATE TABLE "ventes" ("id" integer primary key autoincrement not null, "client_id" integer not null, "produit_id" integer, "commercial_id" integer, "filiale_id" integer not null, "montant" numeric not null, "quantite" integer not null default '1', "reduction" numeric not null default '0', "statut" varchar not null default 'En attente', "date_vente" datetime, "created_at" datetime, "updated_at" datetime, "deleted_at" datetime, foreign key("client_id") references "clients"("id") on delete cascade, foreign key("produit_id") references "produits"("id") on delete set null, foreign key("commercial_id") references "users"("id") on delete set null, foreign key("filiale_id") references "filiales"("id") on delete cascade);

CREATE TABLE "activity_logs" ("id" integer primary key autoincrement not null, "user_id" integer, "action" varchar not null, "module" varchar, "description" text, "ip_address" varchar, "user_agent" varchar, "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id") on delete set null);

CREATE TABLE "notifications" ("id" integer primary key autoincrement not null, "user_id" integer not null, "titre" varchar not null, "message" text not null, "type" varchar, "is_read" tinyint(1) not null default '0', "created_at" datetime, "updated_at" datetime, foreign key("user_id") references "users"("id") on delete cascade);

