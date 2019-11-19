# Resource Files

The library provides a few resources in the `/resources` folder, that provide you some basics to get started with.

## [EventStore/AuditLogProjection.js](../resources/EventStore/AuditLogProjection.js)

This contains a JS script for the [Event Store](https://eventstore.org) that creates a projection to generate an audit log for user activities.

By default your events must have a `_userId` field in the event meta data, that is used to to create the stream of events for this user id. 

It will by default generate a stream named `UserAudit-<userId>` for each user.
 
## [SQL/event_Store_snapshots_table.sql](../resources/SQL/event_Store_snapshots_table.sql)

This file contains SQL to create the `event_store_snapshots` table in a SQL based database. You are free to use this file or to come up with a kind of migration or script that suits your application and style.
