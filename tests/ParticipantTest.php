<?php

use App\Entity\Event;
use App\Entity\Interested;
use App\Entity\User;
use App\Entity\Participant;
use App\Factory\EventFactory;
use App\Factory\UserFactory;
use App\Repository\EventRepository;
use App\Repository\InterestedRepository;
use App\Repository\UserRepository;
use App\Repository\ParticipantRepository;
use App\Mapping\Participant\ParticipantDTO;
use App\Mapping\Participant\ParticipantOTD;
use Faker\Factory;
use PHPUnit\Framework\TestCase;

class ParticipantTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testCanProcessParticipantObject()
    {
        $participantRepository = new ParticipantRepository();

        $event = EventFactory::random();
        $user = UserFactory::random();

        $participant = new Participant();
        $participant
            ->setEventId($event->getId())
            ->setUserId($user->getId());

        $participantRepository->insertOne($participant);

        self::assertNotNull($participantRepository);

        $participantObject = $participantRepository->findOneBy(['event_id' => $event->getId()]);

        self::assertInstanceOf(Participant::class, $participantObject);

        $participantRepository->delete($participant);
    }
}