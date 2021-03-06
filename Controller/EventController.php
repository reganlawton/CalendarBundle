<?php

namespace SpecShaper\CalendarBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use SpecShaper\CalendarBundle\Model\CalendarEventInterface;
use SpecShaper\CalendarBundle\Form\CalendarEventType;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use DateTime;
use SpecShaper\CalendarBundle\Doctrine\CalendarEventManager;
use SpecShaper\CalendarBundle\Event\CalendarEvents;
use SpecShaper\CalendarBundle\Event\CalendarLoadEvents;
use SpecShaper\CalendarBundle\Event\CalendarEditEvent;
use SpecShaper\CalendarBundle\Event\CalendarGetAddressesEvent;
use SpecShaper\CalendarBundle\Utils\SimpleICS;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * @Route("/event")
 */
class EventController extends Controller {

    /**
     * Add a new calendar event.
     *
     * @param Request $request
     *

     *
     * @Route("/download", name="event_download")
     * @Method("GET")
     */
    public function downloadAction() {

        $vCalendar = new \Eluceo\iCal\Component\Calendar('www.example.com');
        $vEvent = new \Eluceo\iCal\Component\Event();
        $vEvent
                ->setDtStart(new \DateTime('2012-12-24'))
                ->setDtEnd(new \DateTime('2012-12-24'))
                ->setNoTime(true)
                ->setSummary('Christmas')
        ;
        $vCalendar->addComponent($vEvent);

        $response = new Response();

        $response->setContent($vCalendar->render())
                ->setCharset('ISO-8859-1')
                ->setStatusCode(Response::HTTP_OK)
        ;
        $response->headers->set('Content-Type', 'multipart/alternative');
        $response->headers->set('Content-Disposition', 'attachment; filename="cal.ics"');

        return $response;
    }

    /**
     * Add a new calendar event.
     *
     * @param Request $request
     *
     * @return JsonResponse
     *
     * @Route("/addevent", name="event_new")
     * @Method({"GET","POST"})
     */
    public function addEventAction(Request $request) {

        $eventManager = $this->get('spec_shaper_calendar.manager.event');

        $event = $eventManager->createEvent();

        $form = $this->createForm(CalendarEventType::class, $event, array(
            'action' => $this->generateUrl('event_new'),
            'method' => 'POST',
        ));

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $newEvent = new CalendarEditEvent($event);

            if ($form->get('updateAttendees')->getData()) {
                $newEvent->setSendUpdate();
            }

            $this->getEventManager()->updateDateTimes($form, $event);

            $modifiedEventEntity = $loadedEvents = $this->getDispatcher()
                    ->dispatch(CalendarEvents::CALENDAR_NEW_EVENT, $newEvent)
                    ->getEventEntity();

            $this->getEventManager()->save($modifiedEventEntity);


            return new JsonResponse($modifiedEventEntity->toArray());
        }

        return $this->render('SpecShaperCalendarBundle:Event:eventModal.html.twig', array(
                    'form' => $form->createView(),
        ));
    }

    /**
     * Update an event with changes from the modal.
     *
     * @param Request $request
     * @param integer $id
     *
     * @return JsonResponse
     * @Route("/{id}/updateevent", name="event_update")
     * @Method({"GET","PUT"})
     */
    public function updateEventAction(Request $request, $id) {

        $event = $this->getEventManager()->getEvent($id);

        $this->getEventManager()->storeOrigionalData($event);

        $form = $this->createForm(CalendarEventType::class, $event, array(
            'action' => $this->generateUrl('event_update', array('id' => $id)),
            'method' => 'PUT',
        ));

        $deleteForm = $this->createDeleteForm($id);
        $deleteSeriesForm = $this->createDeleteSeriesForm($id);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->getEventManager()->updateDateTimes($form, $event);

            $newEvent = new CalendarEditEvent($event);

            if ($form->get('updateAttendees')->getData()) {
                $newEvent->setSendUpdate();
            }

            $modifiedEventEntity = $this->getDispatcher()
                    ->dispatch(CalendarEvents::CALENDAR_EVENT_UPDATED, $newEvent)
                    ->getEventEntity();
          
            $modifiedEventEntity = $this->getEventManager()->updateEvent($modifiedEventEntity);

            $response = new JsonResponse($modifiedEventEntity->toArray());

            return $response;
        }


        return $this->render('SpecShaperCalendarBundle:Event:eventModal.html.twig', array(
                    'form' => $form->createView(),
                    'deleteForm' => $deleteForm->createView(),
                    'deleteSeriesForm' => $deleteSeriesForm->createView(),
        ));
    }

    /**
     * Update the date and times of an event.
     *
     * Used when resizing or dragging a calendar event in the calendar.
     *
     * @param Request                 $request
     * @param CalendarEventInterface $event
     *
     * @return JsonResponse
     * @Route("/{id}/updatedatetime", name="event_updatedatetime")
     * @Method("PUT")
     */
    public function updateDateTimeAction(Request $request, $id) {

        $event = $this->getEventManager()->getEvent($id);

        $start = $request->request->get('start');
        $end = $request->request->get('end');

        $event
                ->setStartDatetime(new DateTime($start))
                ->setEndDatetime(new DateTime($end))
        ;

        $em = $this->getDoctrine()->getManager();
        $em->persist($event);
        $em->flush();

        return new JsonResponse($event->toArray());
    }

    

    /**
     * @param Request $request
     * @Route("/{id}/deleteseries", name="event_deleteseries")
     * @Method({"DELETE"})
     */
    public function deleteSeriesEventAction(Request $request, $id) {
        
        $event = $this->getEventManager()->getEvent($id);

        $removeEvent = new CalendarEditEvent($event);
        
        $this->getDispatcher()
                ->dispatch(CalendarEvents::CALENDAR_EVENT_REMOVED, $removeEvent)
                ->getEventEntity();

        $deletedIdArray = $this->getEventManager()->deleteEventSeries($event);

        return new JsonResponse($deletedIdArray);
    }
    
    /**
     * @param Request $request
     * @Route("/{id}/delete", name="event_delete")
     * @Method({"DELETE"})
     */
    public function deleteEventAction(Request $request, $id) {

        $event = $this->getEventManager()->getEvent($id);

        $removeEvent = new CalendarEditEvent($event);

        $this->getDispatcher()
                ->dispatch(CalendarEvents::CALENDAR_EVENT_REMOVED, $removeEvent)
                ->getEventEntity();

        $deletedIdArray = $this->getEventManager()->deleteEvent($event);

        return new JsonResponse($deletedIdArray);
    }

    /**
     *
     * @Route("/getaddresses", name="calendar_getaddresses")
     * @Method("GET")
     */
    public function getEmailAddressesAction() {

        $addressEvent = new CalendarGetAddressesEvent();

        $addressArray = $this->getDispatcher()
                ->dispatch(CalendarEvents::CALENDAR_GET_ADDRESSES, $addressEvent)
                ->toArray();

        return $this->render('SpecShaperCalendarBundle:Calendar:emailAddressDatalist.html.twig', array(
                    'emailAddresses' => $addressArray
        ));
    }

    /**
     * Creates a form to delete a AvailableDiscipline entity by id.
     *
     * @since  Available since Release 1.0.0
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('event_delete', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     * Creates a form to delete a AvailableDiscipline entity by id.
     *
     * @since  Available since Release 1.0.0
     *
     * @param mixed $id The entity id
     *
     * @return \Symfony\Component\Form\Form The form
     */
    protected function createDeleteSeriesForm($id) {
        return $this->createFormBuilder()
                        ->setAction($this->generateUrl('event_deleteseries', array('id' => $id)))
                        ->setMethod('DELETE')
                        ->getForm()
        ;
    }

    /**
     * Get the Calendar Event CalendarEventManager.
     *
     * @return CalendarEventManager
     */
    protected function getEventManager() {
        return $this->get('spec_shaper_calendar.manager.event');
    }

    /**
     * Get the EventDispatcher.
     *
     * @return \Symfony\Component\EventDispatcher\EventDispatcher
     */
    protected function getDispatcher() {
        return $this->container->get('event_dispatcher');
    }

}
