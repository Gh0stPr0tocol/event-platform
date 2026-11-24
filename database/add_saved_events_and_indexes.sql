-- Additive migration for the existing event_platform database. Run once;
-- do not rerun database/schema.sql because it drops and rebuilds the tables.
CREATE TABLE IF NOT EXISTS saved_events (
    user_id INT UNSIGNED NOT NULL,
    event_id INT UNSIGNED NOT NULL,
    saved_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (user_id, event_id),
    INDEX idx_saved_events_event (event_id),
    CONSTRAINT fk_saved_events_user FOREIGN KEY (user_id)
        REFERENCES users(id) ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_saved_events_event FOREIGN KEY (event_id)
        REFERENCES events(id) ON UPDATE CASCADE ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE events
    ADD INDEX idx_events_date_start (event_date, start_time),
    ADD INDEX idx_events_category_date (category_id, event_date);

ALTER TABLE registrations
    ADD INDEX idx_registrations_event_status_created (event_id, status, created_at, id);
