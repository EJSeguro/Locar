CREATE TABLE IF NOT EXISTS users (
    id bigserial PRIMARY KEY,
    name varchar(255) NOT NULL,
    email varchar(255) NOT NULL UNIQUE,
    email_verified_at timestamp(0) NULL,
    password varchar(255) NOT NULL,
    remember_token varchar(100) NULL,
    role varchar(255) NOT NULL DEFAULT 'cliente',
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);

CREATE INDEX IF NOT EXISTS users_role_index ON users (role);

CREATE TABLE IF NOT EXISTS vehicles (
    id bigserial PRIMARY KEY,
    plate varchar(7) NOT NULL UNIQUE,
    fipe_code varchar(10) NOT NULL,
    year_id varchar(10) NOT NULL,
    brand varchar(255) NOT NULL,
    model varchar(255) NOT NULL,
    model_year smallint NOT NULL,
    fuel varchar(255) NOT NULL,
    fipe_price numeric(12, 2) NOT NULL,
    daily_rate numeric(10, 2) NOT NULL,
    available boolean NOT NULL DEFAULT true,
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);

CREATE INDEX IF NOT EXISTS vehicles_available_index ON vehicles (available);

CREATE TABLE IF NOT EXISTS rentals (
    id bigserial PRIMARY KEY,
    user_id bigint NOT NULL REFERENCES users (id) ON DELETE RESTRICT,
    vehicle_id bigint NOT NULL REFERENCES vehicles (id) ON DELETE RESTRICT,
    start_date date NOT NULL,
    end_date date NOT NULL,
    daily_rate numeric(10, 2) NOT NULL,
    total numeric(12, 2) NOT NULL,
    status varchar(255) NOT NULL DEFAULT 'ativa',
    created_at timestamp(0) NULL,
    updated_at timestamp(0) NULL
);

CREATE INDEX IF NOT EXISTS rentals_status_index ON rentals (status);
CREATE INDEX IF NOT EXISTS rentals_vehicle_id_start_date_end_date_index ON rentals (vehicle_id, start_date, end_date);
