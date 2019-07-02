# Event Sourcing Concepts

## Saving Aggregates

 * The aggregates events that were **NOT** applied are read by the event system implementation
   * and stored in the event store
   * and flagged as read / applied
   * Projections are triggered
     * Projections generate the read model / fill the (SQL) database

## Reading / restoring aggregates

 * Attempt to read the aggregate 
   * Check snapshot store
     * If snapshot of the AG is present, get it from there
     * Get remaining events if there are any after the version of the snapshot
   * If no snapshot is present read *all* events from the event store
   * Apply the events to the aggregate
