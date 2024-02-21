<?php

namespace App\Controller;

use App\Attribute\Route;
use App\Repository\EventRepository;
use Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use App\Service\EventFilter;

class EventController extends AbstractController
{
    public function __construct(
        Environment                      $twig,
        private readonly EventRepository $eventRepository,
    )
    {
        parent::__construct($twig);
    }

    /**
     * @throws SyntaxError
     * @throws RuntimeError
     * @throws LoaderError
     * @throws Exception
     */
    #[Route('/events', name: 'app_event_index', httpMethod: ['GET'])]
    public function index(): string
    {
        $events = $this->eventRepository->findAll();
        $events = EventFilter::use($events)->sortBy("date")->return();
        return $this->twig->render('event/index.html.twig', [
            'events' => $events,
        ]);
    }

    #[Route('/event/show', name: 'app_event_show', httpMethod: ['POST'])]
    public function show(): string
    {
        if (isset($_POST['showSubmit']) && $_POST['showSubmit'] == 'showEvent') {
            $eventRepository = new EventRepository();
            $event = $eventRepository->findOneBy(['id' => $_POST['idShow']]);
        }

        return $this->twig->render('event/show.html.twig', [
            'event' => $event,
        ]);
    }

    #[Route('/events/delete', name: 'app_events_new', httpMethod: ['GET'])]
    public function delete(): string
    {

    }


    /*#[Route('/events/{id}', name: 'app_event_detail', httpMethod: ['GET'])]
    public function detail(int $idEvent, EventRepository $eventRepository): string
    {

        if($eventRepository->findOneBy(['id' => $idEvent])) {
            return $this->twig->render('event/detail.html.twig', [
                'event' => $eventRepository->findOneBy(['id' => $idEvent])
            ]);
        } else {
            return $this->twig->render('404.html.twig');
        }
    }*/

}