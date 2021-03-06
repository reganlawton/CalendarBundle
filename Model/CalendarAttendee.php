<?php

namespace SpecShaper\CalendarBundle\Model;

/**
 * CalendarAttendee.
 */
class CalendarAttendee implements CalendarAttendeeInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var string
     */
    protected $emailAddress;

    /**
     * @var boolean
     */
    protected $hasAccepted = false;
    
    /**
     * @var boolean
     */
    protected $hasDenied = false;
    
    /**
     * @var boolean
     */
    protected $isOptional = false;

    /**
     * @var \DateTime
     */
    protected $acceptedOn;

    /**
     * @var string
     */
    protected $message;

    /**
     * @var string
     */
    protected $token = "sadfadsf";

    /**
     * @var \DateTime
     */
    protected $sentOn;
    
//    /**
//     * Constructor.
//     */
//    public function __construct()
//    {
//
//    }

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
     * Set meeting.
     *
     * @param string $meeting
     *
     * @return CalendarAttendee
     */
    public function setMeeting($meeting)
    {
        $this->meeting = $meeting;

        return $this;
    }

    /**
     * Get meeting.
     *
     * @return string
     */
    public function getMeeting()
    {
        return $this->meeting;
    }

    /**
     * Set emailAddress.
     *
     * @param string $emailAddress
     *
     * @return CalendarAttendee
     */
    public function setEmailAddress($emailAddress)
    {
        $this->emailAddress = $emailAddress;

        return $this;
    }

    /**
     * Get emailAddress.
     *
     * @return string
     */
    public function getEmailAddress()
    {
        return $this->emailAddress;
    }
    
    /**
     * Set isOptional.
     *
     * @param isOptional $isOptional
     *
     * @return CalendarAttendee
     */
    public function setIsOptional($isOptional)
    {
        $this->isOptional = $isOptional;

        return $this;
    }

    /**
     * Get isOptional.
     *
     * @return boolean
     */
    public function getIsOptional()
    {
        return $this->isOptional;
    }

    /**
     * Set hasAccepted.
     *
     * @param string $hasAccepted
     *
     * @return CalendarAttendee
     */
    public function setHasAccepted($hasAccepted)
    {
        $this->hasAccepted = $hasAccepted;

        return $this;
    }

    /**
     * Get hasAccepted.
     *
     * @return string
     */
    public function getHasAccepted()
    {
        return $this->hasAccepted;
    }
    
    /**
     * Set hasDenied.
     *
     * @param string $hasDenied
     *
     * @return CalendarAttendee
     */
    public function setHasDenied($hasDenied)
    {
        $this->hasDenied = $hasDenied;

        return $this;
    }

    /**
     * Get hasDenied.
     *
     * @return string
     */
    public function getHasDenied()
    {
        return $this->hasDenied;
    }

    /**
     * Set acceptedOn.
     *
     * @param \DateTime $acceptedOn
     *
     * @return CalendarAttendee
     */
    public function setAcceptedOn($acceptedOn)
    {
        $this->acceptedOn = $acceptedOn;

        return $this;
    }

    /**
     * Get acceptedOn.
     *
     * @return \DateTime
     */
    public function getAcceptedOn()
    {
        return $this->acceptedOn;
    }

    /**
     * Set token.
     *
     * @param string $token
     *
     * @return CalendarAttendee
     */
    public function setToken($token)
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Get token.
     *
     * @return string
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Set sentOn.
     *
     * @param \DateTime $sentOn
     *
     * @return CalendarAttendee
     */
    public function setSentOn($sentOn)
    {
        $this->sentOn = $sentOn;

        return $this;
    }

    /**
     * Get sentOn.
     *
     * @return \DateTime
     */
    public function getSentOn()
    {
        return $this->sentOn;
    }

    /**
     * Set calendarEvent
     *
     * @param CalendarEventInterface $calendarEvent
     *
     * @return CalendarAttendee
     */
    public function setCalendarEvent(CalendarEventInterface $calendarEvent = null)
    {
        $this->calendarEvent = $calendarEvent;

        return $this;
    }

    /**
     * Get calendarEvent
     *
     * @return CalendarEventInterface
     */
    public function getCalendarEvent()
    {
        return $this->calendarEvent;
    }
    
}
