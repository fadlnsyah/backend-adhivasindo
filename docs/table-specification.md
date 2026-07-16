# Table Specification

## users

| Column | Type | Nullable | Key | Description |
| --- | --- | --- | --- | --- |
| id | bigint unsigned | No | Primary | User primary key. |
| name | varchar(255) | No | - | User name. |
| email | varchar(255) | No | Unique | User email address. |
| email_verified_at | timestamp | Yes | - | Email verification timestamp. |
| password | varchar(255) | No | - | Hashed password. |
| remember_token | varchar(100) | Yes | - | Remember token for web authentication. |
| created_at | timestamp | Yes | - | Record creation timestamp. |
| updated_at | timestamp | Yes | - | Record update timestamp. |

## contents

| Column | Type | Nullable | Key | Description |
| --- | --- | --- | --- | --- |
| id | bigint unsigned | No | Primary | Content primary key. |
| user_id | bigint unsigned | No | Foreign | References `users.id`. |
| title | varchar(255) | No | - | Content title. |
| content | longtext | No | - | Main content body. |
| image | varchar(255) | Yes | - | Optional image path or URL. |
| created_at | timestamp | Yes | - | Record creation timestamp. |
| updated_at | timestamp | Yes | - | Record update timestamp. |

## password_reset_tokens

| Column | Type | Nullable | Key | Description |
| --- | --- | --- | --- | --- |
| email | varchar(255) | No | Primary | Email address for password reset. |
| token | varchar(255) | No | - | Password reset token. |
| created_at | timestamp | Yes | - | Token creation timestamp. |

## sessions

| Column | Type | Nullable | Key | Description |
| --- | --- | --- | --- | --- |
| id | varchar(255) | No | Primary | Session identifier. |
| user_id | bigint unsigned | Yes | Index | Related user ID when authenticated. |
| ip_address | varchar(45) | Yes | - | Client IP address. |
| user_agent | text | Yes | - | Client user agent. |
| payload | longtext | No | - | Serialized session payload. |
| last_activity | integer | No | Index | Last session activity timestamp. |

## cache

| Column | Type | Nullable | Key | Description |
| --- | --- | --- | --- | --- |
| key | varchar(255) | No | Primary | Cache key. |
| value | mediumtext | No | - | Cached value. |
| expiration | integer | No | - | Cache expiration timestamp. |

## cache_locks

| Column | Type | Nullable | Key | Description |
| --- | --- | --- | --- | --- |
| key | varchar(255) | No | Primary | Cache lock key. |
| owner | varchar(255) | No | - | Lock owner identifier. |
| expiration | integer | No | - | Lock expiration timestamp. |

## jobs

| Column | Type | Nullable | Key | Description |
| --- | --- | --- | --- | --- |
| id | bigint unsigned | No | Primary | Job primary key. |
| queue | varchar(255) | No | Index | Queue name. |
| payload | longtext | No | - | Serialized job payload. |
| attempts | tinyint unsigned | No | - | Number of execution attempts. |
| reserved_at | integer unsigned | Yes | - | Reservation timestamp. |
| available_at | integer unsigned | No | - | Availability timestamp. |
| created_at | integer unsigned | No | - | Job creation timestamp. |

## job_batches

| Column | Type | Nullable | Key | Description |
| --- | --- | --- | --- | --- |
| id | varchar(255) | No | Primary | Job batch identifier. |
| name | varchar(255) | No | - | Batch name. |
| total_jobs | integer | No | - | Total jobs in batch. |
| pending_jobs | integer | No | - | Pending jobs count. |
| failed_jobs | integer | No | - | Failed jobs count. |
| failed_job_ids | longtext | No | - | Failed job identifiers. |
| options | mediumtext | Yes | - | Batch options. |
| cancelled_at | integer | Yes | - | Cancellation timestamp. |
| created_at | integer | No | - | Batch creation timestamp. |
| finished_at | integer | Yes | - | Batch finish timestamp. |

## failed_jobs

| Column | Type | Nullable | Key | Description |
| --- | --- | --- | --- | --- |
| id | bigint unsigned | No | Primary | Failed job primary key. |
| uuid | varchar(255) | No | Unique | Failed job UUID. |
| connection | text | No | - | Queue connection name. |
| queue | text | No | - | Queue name. |
| payload | longtext | No | - | Serialized job payload. |
| exception | longtext | No | - | Exception details. |
| failed_at | timestamp | No | - | Failure timestamp. |
