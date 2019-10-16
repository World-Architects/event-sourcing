# Event Sourcing Concepts

The fundamental idea of Event Sourcing is that of ensuring every change to the state of an application is captured in an event object, and that these event objects are themselves stored in the sequence they were applied for the same lifetime as the application state itself.

If you need a basic introduction to event sourcing check [Martin Fowlers Event Sourcing description](https://martinfowler.com/eaaDev/EventSourcing.html).

## Saving Aggregates Sequence

This describes what happens when an aggregate is saved:

 * The aggregates events that were **NOT** yet applied are read by the event system implementation
   * and stored in the event store
   * and flagged as read / applied
   * Projections (if there are any) are triggered
     * Projections generate the read model / fill the database

## Reading / restoring aggregates Sequence

This describes what happens when an aggregate is read / it's state restored.

 * Attempt to read the aggregate 
   * Check snapshot store
     * If snapshot of the AG is present, get it from there
     * Get remaining events if there are any after the version of the snapshot
   * If no snapshot is present read *all* events from the event store from the beginning
   * Replay the events on the aggregate
