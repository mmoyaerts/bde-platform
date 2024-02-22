<?php

namespace App\Controller\Admin;

use App\Attribute\Route;
use App\Controller\AbstractController;
use App\Entity\Event;
use App\Repository\EventRepository;
use App\Repository\TagRepository;
use App\Repository\UserRepository;
use DateTime;
use Exception;
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

class AdminEventController extends AbstractController
{
    public function __construct(
        Environment                      $twig,
        private readonly EventRepository $eventRepository,
        private readonly TagRepository   $tagRepository,
        private readonly UserRepository  $userRepository,
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
    #[Route('/admin/event/index', name: 'app_admin_event_index', httpMethod: ['GET', 'POST'])]
    public function index(): string
    {
        $eventObjects = $this->eventRepository->findAll();
        $events = array_map(fn(Event $event): array => $event->toArray(), $eventObjects);

//        var_dump($events);

        $eventsWithOwners = [];
        foreach ($events as $event) {

            $eventsWithOwners[] = [
                ...$event,
                'owner' => $this->userRepository->findOneBy(
                        ['id' => $event['owner_id']]
                    )->getFirstname()
                    . ' '
                    . $this->userRepository->findOneBy(
                        ['id' => $event['owner_id']]
                    )->getLastname()
            ];
        }

        return $this->twig->render('admin/index.html.twig', [
            'items' => $eventsWithOwners,
            'entityName' => 'event'
        ]);
    }

    #[Route('/admin/event/new', name: 'app_admin_event_new', httpMethod: ['GET', 'POST'])]
    public function new(): string
    {
        if (isset($_POST['new-event-submit']) && $_POST['new-event-submit'] == 'new-event') {
            $event = new Event();
            $eventRepository = new EventRepository();

            array_map('trim', $_POST);

            if (isset($_FILES['new-event-file']) && $_FILES['new-event-file']['error'] === UPLOAD_ERR_OK) {

                var_dump('in file upload if');

                $originalFileName = $_FILES['new-event-file']['name'];
                $tmpFileName = $_FILES['new-event-file']['tmp_name'];

                $fileNameCmps = explode(".", $originalFileName);
                $fileExtension = strtolower(end($fileNameCmps));

                $fileSize = $_FILES['new-event-file']['size'];

                $newFileName = md5(time() . $tmpFileName) . '.' . $fileExtension;
                $allowedfileExtensions = array('jpg', 'gif', 'png', 'zip', 'txt', 'xls', 'doc');

                if (in_array($fileExtension, $allowedfileExtensions)) {
                    $uploadFileDir = __DIR__ . '/../../../public/assets/images/';
                    $dest_path = $uploadFileDir . $newFileName;

                    move_uploaded_file($tmpFileName, $dest_path);
                }
            }

            var_dump($_FILES['new-event-file']['error']);

            $event
                ->setName($_POST['name'])
                ->setDescription($_POST['description'])
                ->setStartDate(new DateTime($_POST['startDate']))
                ->setEndDate(new DateTime($_POST['endDate']))
                ->setTag($_POST['tag'])
                ->setCapacity($_POST['capacity'])
                ->setOwnerId(1)
                ->setFileName($newFileName)
                ->setFileSize($fileSize);

            if ($eventRepository->insertOne($event)) {
                $this->redirect('/admin/event/index');
            }
        }

        $tags = $this->tagRepository->findAll();
        return $this->twig->render('admin/new/event-new.html.twig', [
            'tags' => $tags,
        ]);
    }

    #[Route('/admin/event/edit', name: 'app_admin_event_edit', httpMethod: ['GET', 'POST'])]
    public function edit(): string
    {
        //todo: implement /{id}
        $eventRepository = new EventRepository();
        if (isset($_POST['edit-event-input'])) {
            $event = $eventRepository->findOneBy(['id' => $_POST['edit-event-input']]);
        }
        if (isset($_POST['update-event-submit']) && $_POST['update-event-submit'] == 'update-event') {

            $updatedEventArray = [
                'name' => $_POST['name'],
                'description' => $_POST['description'],
                'startDate' => $_POST['startDate'],
                'endDate' => $_POST['endDate'],
                'tag' => $_POST['tag'],
                'capacity' => $_POST['capacity'],
                'owner_id' => $_POST['capacity'],
            ];

            if ($eventRepository->update($updatedEventArray, $event)) {
                $this->redirect('/admin/event/index');
            }
        }
        $tags = $this->tagRepository->findAll();

        return $this->twig->render('admin/edit/event-edit.html.twig', [
            'item' => $event,
            'tags' => $tags,
        ]);
    }

    #[Route('/admin/event/delete', name: 'app_admin_event_delete', httpMethod: ['POST'])]
    public function delete(): void
    {
        if (isset($_POST['delete-event-submit']) && $_POST['delete-event-submit'] == 'delete-event') {
            $eventRepository = new EventRepository();
            $eventToDelete = $eventRepository->findOneBy(['id' => $_POST['delete-event-input']]);

            if ($eventRepository->delete($eventToDelete)) {
                $this->redirect('/admin/event/index');
            }
        }
    }
}