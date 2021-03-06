<?php

namespace SpecShaper\CalendarBundle\Model;

/**
 * CalendarComment.
 */
abstract class CalendarComment implements CalendarCommentInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var \DateTime
     */
    protected $createdOn;

    /**
     * @var string
     */
    protected $comment;

    /**
     * @var string
     */
    protected $attendee;

    /**
     * Constructor.
     */
    public function __construct()
    {
        
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add event.
     *
     * @param CalendarEventInterface $event
     *
     * @return CalendarComment
     */
    public function addCalendarEvent(CalendarEventInterface $event)
    {
        $this->calendarEvent[] = $event;

        return $this;
    }

    /**
     * Remove event.
     *
     * @param CalendarEventInterface $event
     */
    public function removeCalendarEvent(CalendarEventInterface $event)
    {
        $this->calendarEvent->removeElement($event);
    }

    /**
     * Get event.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCalendarEvent()
    {
        return $this->calendarEvent;
    }

    public function setCalendarAttendee(CalendarAttendeeInterface $attendee)
    {
        $this->attendee = $attendee;

        return $this;
    }

    public function getCalendarAttendee()
    {
        return $this->attendee;
    }
     
    
}
