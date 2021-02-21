<?php
/**
 * Created by PhpStorm.
 * @author Daniel Krizan <danyelkrizan@gmail.com>
 * Date: 04.02.21 15:04
 */

namespace App\Entity;

use Doctrine\ORM\Id\UuidGenerator;
use Doctrine\ORM\Mapping as ORM;

/**
 * Class Recording
 *
 * @author Daniel Krizan <danyelkrizan@gmail.com>
 * @package App\Entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="recording")
 */
class Recording {

    /**
     * ID
     *
     * @ORM\Id
     * @ORM\Column(type="uuid", unique=true)
     * @ORM\GeneratedValue(strategy="CUSTOM")
     * @ORM\CustomIdGenerator(class=UuidGenerator::class)
     * @var \Ramsey\Uuid\UuidInterface
     */
    private $id;

    /**
     * Title
     * @ORM\Column(type="string")
     * @var
     */
    private $name;

    /**
     * Start datetime
     *
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $start;

    /**
     * End datetime
     *
     * @ORM\Column(type="datetime")
     * @var \DateTime
     */
    private $end;

    /**
     * Channel ID
     *
     * @ORM\Column(type="integer", name="channel_id")
     * @var int
     */
    private $channelId;

    /**
     * @return int
     */
    public function getChannelId(): int {
        return $this->channelId;
    }

    /**
     * @param int $channelId
     */
    public function setChannelId(int $channelId): void {
        $this->channelId = $channelId;
    }

    /**
     * @return mixed
     */
    public function getName() {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName($name): void {
        $this->name = $name;
    }

    /**
     * @return \DateTime
     */
    public function getStart(): \DateTime {
        return $this->start;
    }

    /**
     * @param \DateTime $start
     */
    public function setStart(\DateTime $start): void {
        $this->start = $start;
    }

    /**
     * @return \DateTime
     */
    public function getEnd(): \DateTime {
        return $this->end;
    }

    /**
     * @param \DateTime $end
     */
    public function setEnd(\DateTime $end): void{
        $this->end = $end;
    }

}